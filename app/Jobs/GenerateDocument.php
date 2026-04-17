<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Enums\DocumentType;
use App\Models\GeneratedDocument;
use App\Models\MissingSkill;
use App\Enums\GenerationStatus;

class GenerateDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $userId,
        public bool $attachResume,
        public bool $attachCoverLetter,
        public array $payload
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::findOrFail($this->userId);

        $request = Http::asMultipart();
        $request->timeout(300);

        if($this->attachResume){
            $resumeDocument = $user->getDocument(DocumentType::Resume);
            $resume = Storage::disk('user_documents')->get($resumeDocument->getPath());
            $request->attach('resume', $resume, $resumeDocument->filename);
        }

        if($this->attachCoverLetter){
            $coverLetterDocument = $user->getDocument(DocumentType::CoverLetter);
            $coverLetter = Storage::disk('user_documents')->get($coverLetterDocument->getPath());
            $request->attach('coverLetter', $coverLetter, $coverLetterDocument->filename);
        }

        $generation = GeneratedDocument::Create([
            'user_id' => $user->id,
            'status' => GenerationStatus::Pending->value,
            'resume_text' => '',
            'cover_letter_text' => '',
        ]);

        try {
            $response = $request->post(config('n8n.generate_url'), $this->payload )->throw();

            $generation->status = GenerationStatus::Processing;
            $generation->save();

            if($response->successful()){

                $data = $response->json();

                $generation->update([
                    'status' => GenerationStatus::Completed,
                    'resume_text' => $data['resume_text'] ?? '',
                    'cover_letter_text' => $data['cover_letter_text'] ?? '',
                ]);

                $missingSkills = $data['missingSkills'] ?? [];

                foreach($missingSkills as $skill){
                    MissingSkill::create([
                        'generated_document_id' => $generation->id,
                        'skill_name' => $skill,
                    ]);
                }
            } else {
                /* $this->error = 'Failed to analyze documents'; */
                $generation->status = GenerationStatus::Failed;
                $generation->save();
                Log::error('N8n analyze error: '. $response->body());
            }
        } catch (\Exception $e) {
            $generation->status = GenerationStatus::Failed;
            $generation->save();
            Log::error('N8n analyze exception: '.$e->getMessage());
        }
    }
}
