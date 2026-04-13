<?php

namespace Livewire\Component;

use App\Models\GeneratedDocument;
use App\Models\MissingSkill;
use App\Enums\DocumentType;
use App\Models\UserDocument;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Attributes\Session;

new class extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Validate('file|mimes:pdf,doc,docx|max:10240')]
    public $resume;

    #[Validate('file|mimes:pdf,doc,docx|max:10240')]
    public $coverLetter;

    #[Validate('required|string|min:10'), Session]
    public $jobDescription = '';

    public $resumeExists = false;

    public $coverLetterExists = false;

    public $resumeText = '';

    public $coverLetterText = '';

    public $missingSkills = [];

    public $skillDescriptions = [];

    public $showResults = false;

    public $showSkills = false;

    public $uploading = false;

    public $selectedHistory = null;

    public $selectedHistoryTitle = '';

    public function mount()
    {
        $this->checkDocumentsExist();
    }

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


    #[On('auth-success')]
    public function checkDocumentsExist()
    {
        $userId = Auth::id();

        $this->resumeExists = UserDocument::where('user_id', $userId)
            ->where('documentType', DocumentType::Resume->value)->exists();

        $this->coverLetterExists = UserDocument::where('user_id', $userId)
            ->where('documentType', DocumentType::CoverLetter->value)->exists();

    }

    public function updateDocument(DocumentType $documentType, TemporaryUploadedFile $document){
        $userId = Auth::id();

        $userDocument = UserDocument::where('user_id', $userId)
            ->where('documentType', $documentType->value)->first();

        // Delete the old record
        if(!empty($userDocument)){
            Storage::disk('user_documents')->delete($userDocument->getPath());
        }

        $filename = $documentType->value . '.' . $document->getClientOriginalExtension();
        $path = $userId;
        $this->resume->storeAs(path: $path, name: $filename, options: 'user_documents');

        UserDocument::updateOrCreate([
            'user_id' => $userId,
            'documentType' => $documentType->value,
            'filename' => $filename,
            'path' => $path,
        ]);
    }

    public function updatedResume()
    {
        $this->updateDocument(DocumentType::Resume, $this->resume);
    }

    public function updatedCoverLetter()
    {
        $this->updateDocument(DocumentType::CoverLetter, $this->coverLetter);
    }

    public function analyze()
    {
        $user = User::findOrFail(Auth::id());
        $request = Http::asMultipart();

        if($this->resumeExists){
            $resumeDocument = $user->getDocument(DocumentType::Resume);
            $resume = Storage::disk('user_documents')->get($resumeDocument->getPath());
            $request->attach('resume', $resume, $resumeDocument->filename);
        }

        if($this->coverLetterExists){
            $coverLetterDocument = $user->getDocument(DocumentType::CoverLetter);
            $coverLetter = Storage::disk('user_documents')->get($coverLetterDocument->getPath());
            $request->attach('coverLetter', $coverLetter, 'coverLetter');
        }

        $payload = [
            'jobDescription' => $this->jobDescription,
            'skills'         => 'IIS: While at LANSA I had to maanage some web applications using IIS.',
        ];

        try {
            $response = $request->post(config('n8n.generate_url'), $payload )
            ->throw()->json();

            if ($response->successful()) {
                Log::info($response->json());
        /*         $data = $response->json(); */
        /*         $this->resumeText = $data['resume'] ?? ''; */
        /*         $this->coverLetterText = $data['coverLetter'] ?? ''; */
        /*         $this->missingSkills = $data['missingSkills'] ?? []; */
        /*         $this->showResults = true; */
        /*         $this->showSkills = ! empty($this->missingSkills); */
        /*         $this->selectedHistory = null; */
        /**/
        /*         $document = GeneratedDocument::create([ */
        /*             'user_id' => $userId, */
        /*             'resume_text' => $this->resumeText, */
        /*             'cover_letter_text' => $this->coverLetterText, */
        /*             'has_missing_skills' => ! empty($this->missingSkills), */
        /*         ]); */
        /**/
        /*         // Save missing skills */
        /*         foreach ($this->missingSkills as $skill) { */
        /*             MissingSkill::create([ */
        /*                 'generated_document_id' => $document->id, */
        /*                 'skill_name' => $skill, */
        /*             ]); */
        /*         } */
        /**/
        /*         $this->selectedHistory = $document; */
        /*         $this->dispatch('show-modal'); */
        /*     } else { */
        /*         error_log('@#@@'); */
        /*         $this->error = 'Failed to analyze documents'; */
        /*         Log::error('N8n analyze error: '.$response->body()); */
            }
        } catch (\Exception $e) {
            /* $this->error = 'Error: '.$e->getMessage(); */
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
