<?php

namespace Livewire\Component;

use App\Models\GeneratedDocument;
use App\Models\MissingSkill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $resume;

    public $coverLetter;

    public $jobDescription = '';

    public $resumeUploaded = false;

    public $coverLetterUploaded = false;

    public $resumeExists = false;

    public $coverLetterExists = false;

    public $resumeText = '';

    public $coverLetterText = '';

    public $missingSkills = [];

    public $skillDescriptions = [];

    public $showResults = false;

    public $showSkills = false;

    public $uploading = false;

    public $resumeUploading = false;

    public $selectedHistory = null;

    public $selectedHistoryTitle = '';

    protected $rules = [
        'resume' => 'file|mimes:pdf,doc,docx|max:10240',
        'coverLetter' => 'file|mimes:pdf,doc,docx|max:10240',
        'jobDescription' => 'required|string|min:10',
    ];

    /* public function mount() */
    /* { */
    /*     $this->checkDocumentsExist(); */
    /* } */

    /* public function render() */
    /* { */
    /*     $userId = Auth::id() ?? 'guest'; */
    /*     $history = GeneratedDocument::where('user_id', $userId) */
    /*         ->orderBy('created_at', 'desc') */
    /*         ->paginate(10); */
    /**/
    /*     return $this->view([ */
    /*         'history' => $history, */
    /*     ]); */
    /* } */


    public function checkDocumentsExist()
    {
        $userId = Auth::id() ?? 'guest';

        try {
            $baseUrl = config('n8n.check_documents_url');
            $response = Http::timeout(30)->get("{$baseUrl}/{$userId}");

            if ($response->successful()) {
                $data = $response->json();
                $this->resumeExists = $data['resumeExists'] ?? false;
                $this->coverLetterExists = $data['coverLetterExists'] ?? false;
            }
        } catch (\Exception $e) {
            Log::error('N8n check documents error: '.$e->getMessage());
        }
    }

    public function updatedResume()
    {
        $this->uploadResume();
    }

    public function updatedCoverLetter()
    {
        $this->uploadCoverLetter();
    }

    public function uploadResume()
    {
        if (! $this->resume) {
            return;
        }

        $this->uploading = true;
        $this->resumeUploading = true;
        $this->error = '';
        $userId = Auth::id() ?? 'guest';

        try {
            $response = Http::timeout(120)
                ->asMultipart()
                ->post(config('n8n.upload_resume_url'), [
                    [
                        'name' => 'resume',
                        'contents' => fopen($this->resume->getRealPath(), 'r'),
                        'filename' => $this->resume->getClientOriginalName(),
                    ],
                    [
                        'name' => 'userId',
                        'contents' => $userId,
                    ],
                ]);

            if ($response->successful()) {
                $this->resumeUploaded = true;
                $this->resumeExists = true;
                $this->resumeText = $response->json('text') ?? $response->body();
            } else {
                $this->error = 'Failed to parse resume';
                Log::error('N8n resume upload error: '.$response->body());
            }
        } catch (\Exception $e) {
            $this->error = 'Error: '.$e->getMessage();
            Log::error('N8n resume upload exception: '.$e->getMessage());
        }

        $this->uploading = false;
        $this->resumeUploading = false;
    }

    public function uploadCoverLetter()
    {
        if (! $this->coverLetter) {
            return;
        }

        $this->uploading = true;
        $this->error = '';
        $userId = Auth::id() ?? 'guest';

        try {
            $response = Http::timeout(120)
                ->asMultipart()
                ->post(config('n8n.upload_cover_letter_url'), [
                    [
                        'name' => 'coverLetter',
                        'contents' => fopen($this->coverLetter->getRealPath(), 'r'),
                        'filename' => $this->coverLetter->getClientOriginalName(),
                    ],
                    [
                        'name' => 'userId',
                        'contents' => $userId,
                    ],
                ]);

            if ($response->successful()) {
                $this->coverLetterUploaded = true;
                $this->coverLetterExists = true;
                $this->coverLetterText = $response->json('text') ?? $response->body();
            } else {
                $this->error = 'Failed to parse cover letter';
                Log::error('N8n cover letter upload error: '.$response->body());
            }
        } catch (\Exception $e) {
            $this->error = 'Error: '.$e->getMessage();
            Log::error('N8n cover letter upload exception: '.$e->getMessage());
        }

        $this->uploading = false;
    }

    public function analyze()
    {
        $this->validate([
            'jobDescription' => 'required|string|min:10',
        ]);

        $this->error = '';

        try {
            $userId = Auth::id() ?? 'guest';

            // Get all previous skill responses for this user
            $previousSkills = MissingSkill::whereHas('generatedDocument', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->where('addressed', true)->get();

            $skillsData = [];
            foreach ($previousSkills as $skill) {
                $skillsData[$skill->skill_name] = $skill->user_response;
            }

            $payload = [
                'jobDescription' => $this->jobDescription,
                'userId' => $userId,
                'skills' => $skillsData,
            ];

            // Add skills if there are any previous skill responses
            if (! empty($skillsData)) {
                $payload['skills'] = $skillsData;
            }

            $response = Http::timeout(300)
                ->post(config('n8n.generate_url'), $payload);

            if ($response->successful()) {
                $data = $response->json();
                $this->resumeText = $data['resume'] ?? '';
                $this->coverLetterText = $data['coverLetter'] ?? '';
                $this->missingSkills = $data['missingSkills'] ?? [];
                $this->showResults = true;
                $this->showSkills = ! empty($this->missingSkills);
                $this->selectedHistory = null;

                $document = GeneratedDocument::create([
                    'user_id' => $userId,
                    'resume_text' => $this->resumeText,
                    'cover_letter_text' => $this->coverLetterText,
                    'has_missing_skills' => ! empty($this->missingSkills),
                ]);

                // Save missing skills
                foreach ($this->missingSkills as $skill) {
                    MissingSkill::create([
                        'generated_document_id' => $document->id,
                        'skill_name' => $skill,
                        'addressed' => false,
                    ]);
                }

                $this->selectedHistory = $document;
                $this->dispatch('show-modal');
            } else {
                error_log('@#@@');
                $this->error = 'Failed to analyze documents';
                Log::error('N8n analyze error: '.$response->body());
            }
        } catch (\Exception $e) {
            error_log('############################');
            $this->error = 'Error: '.$e->getMessage();
            Log::error('N8n analyze exception: '.$e->getMessage());
        } finally {
        }
    }

    public function regenerate($skillDescriptions)
    {
        if (empty($this->resumeText) || empty($this->coverLetterText)) {
            $this->error = 'No documents to regenerate';

            return;
        }

        $this->error = '';

        try {
            $userId = Auth::id() ?? 'guest';

            $response = Http::timeout(300)
                ->post(config('n8n.generate_url'), [
                    'jobDescription' => $this->jobDescription,
                    'userId' => $userId,
                    'skills' => $skillDescriptions,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->resumeText = $data['resume'] ?? '';
                $this->coverLetterText = $data['coverLetter'] ?? '';
                $this->showSkills = false;
            } else {
                $this->error = 'Failed to regenerate documents';
                Log::error('N8n regenerate error: '.$response->body());
            }
        } catch (\Exception $e) {
            $this->error = 'Error: '.$e->getMessage();
            Log::error('N8n regenerate exception: '.$e->getMessage());
        } finally {
        }
    }

    public function saveSkillResponses($skillDescriptions)
    {
        if (! $this->selectedHistory) {
            return;
        }

        foreach ($skillDescriptions as $skill => $description) {
            $response = empty(trim($description)) ? '[NO_EXPERIENCE]' : $description;

            MissingSkill::where('generated_document_id', $this->selectedHistory->id)
                ->where('skill_name', $skill)
                ->update([
                    'user_response' => $response,
                    'addressed' => true,
                ]);
        }
    }

    public function viewMissingSkills($id)
    {
        $userId = Auth::id() ?? 'guest';

        $this->selectedHistory = GeneratedDocument::with('missingSkills')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($this->selectedHistory) {
            $this->skillDescriptions = [];
            foreach ($this->selectedHistory->missingSkills as $skill) {
                $displayResponse = $skill->user_response === '[NO_EXPERIENCE]'
                    ? ''
                    : $skill->user_response;
                $this->skillDescriptions[$skill->skill_name] = $displayResponse;
            }

            $this->dispatch('show-modal.missing-skills');
        }
    }

    public function closeDialog()
    {
        $this->showResults = false;
        $this->selectedHistory = null;
        $this->dispatch('hide-dialog');
    }

    public function copyToClipboard(string $type): void
    {
        $text = $type === 'resume' ? $this->resumeText : $this->coverLetterText;
        $this->dispatch('copy-to-clipboard', $text);
    }

    public function viewHistory($id)
    {
        $userId = Auth::id() ?? 'guest';
        $this->selectedHistory = GeneratedDocument::with('missingSkills')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($this->selectedHistory) {
            $this->resumeText = $this->selectedHistory->resume_text;
            $this->coverLetterText = $this->selectedHistory->cover_letter_text;
            $this->showResults = true;
            $this->selectedHistoryTitle = "Generated Documents {$this->selectedHistory->created_at->format('M d, Y H:i')}";
            $this->dispatch('show-modal.history');
        }
    }

    public function deleteHistory($id)
    {
        $userId = Auth::id() ?? 'guest';
        GeneratedDocument::where('id', $id)
            ->where('user_id', $userId)
            ->delete();
    }

};
