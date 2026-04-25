<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Enums\DocumentType;
use App\Models\Application;
use App\Models\MissingSkill;
use App\Models\Generation;
use App\Enums\GenerationStatus;

class GenerateDocuments implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $application_id,
        public string $additional_instructions,
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $application = Application::findOrFail($this->application_id);

            $generation = Generation::create([
                'application_id' => $application->user_id,
                'additional_instructions' => $this->additional_instructions,
                'status' => GenerationStatus::Pending->value,
                'resume_text' => '',
                'cover_letter_text' => '',
                'viewed' => false,
            ]);

            $request = Http::asMultipart();
            $request->timeout(300);

            $user = User::findOrFail($application->user_id);

            $resumeDocument = $user->getDocument(DocumentType::Resume);
            $resume = Storage::disk('user_documents')->get($resumeDocument->getPath());
            $request->attach('resume', $resume, $resumeDocument->filename);

            $coverLetterDocument = $user->getDocument(DocumentType::CoverLetter);
            $coverLetter = Storage::disk('user_documents')->get($coverLetterDocument->getPath());
            $request->attach('coverLetter', $coverLetter, $coverLetterDocument->filename);

            $payload = [
                'jobDescription' => $application->job_description,
                'skills'         => 'IIS: While at LANSA I had to maanage some web applications using IIS.',
            ];

            $generation->status = GenerationStatus::Processing;
            $generation->save();

            $response = $request->post(config('n8n.generate_url'), $payload )->throw();

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
            /* $generation->status = GenerationStatus::Failed; */
            /* $generation->save(); */
            Log::error('N8n analyze exception: '.$e->getMessage());
        }
    }
}
