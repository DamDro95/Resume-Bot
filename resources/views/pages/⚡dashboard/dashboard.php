<?php

namespace Livewire\Component;

use App\Models\Application;
use App\Models\MissingSkill;
use App\Enums\DocumentType;
use App\Enums\GenerationStatus;
use App\Models\UserDocument;
use App\Jobs\GenerateDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Attributes\Session;

new class extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Validate('file|mimes:pdf|max:10240')]
    public $resume;

    #[Validate('file|mimes:pdf|max:10240')]
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

    public bool $isGenerating = false;

    public array $history = [];

    public function mount()
    {
        $this->checkDocumentsExist();
        $this->checkGeneration();
    }

    #[Computed]
    public function generations()
    {
        return Application::Paginate(10);
    }

    public function checkGeneration(): void{
        $userId = Auth::id();

        $isGenerating = Application::where('user_id', $userId)
        ->exists();

        //If the state is the same do nothing
        if( $this->isGenerating === $isGenerating){
            return;
        }

        // Find a document that has not been viewed, which is most likely the one that got generated
        $generation = Application::where('user_id', $userId)
        ->first();

        $this->isGenerating = $isGenerating;

        // If nothing found do nothing
        if(empty($generation)){
            return;
        }

        $this->dispatch('document-generation-complete');
    }

    public function updatedResume()
    {
        $this->updateDocument(DocumentType::Resume, $this->resume);
        $this->resumeExists = true;
    }

    public function updatedCoverLetter()
    {
        $this->updateDocument(DocumentType::CoverLetter, $this->coverLetter);
        $this->coverLetterExists = true;
    }

    /* public function render() */
    /* { */
    /*     $userId = Auth::id() ?? 'guest'; */
    /*     $history = Application::where('user_id', $userId) */
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
        $document->storeAs(path: $path, name: $filename, options: 'user_documents');

        UserDocument::updateOrCreate([
            'user_id' => $userId,
            'documentType' => $documentType->value,
        ],[

            'filename' => $filename,
            'path' => $path,
        ]);
    }

    public function analyze()
    {
        Log::info('asd');
        $payload = [
            'jobDescription' => $this->jobDescription,
            'skills'         => 'IIS: While at LANSA I had to maanage some web applications using IIS.',
        ];

        GenerateDocument::dispatch(
            userId: Auth::id(),
            payload: $payload,
            attachResume: $this->resumeExists,
            attachCoverLetter: $this->coverLetterExists,
        );

        $this->isGenerating = true;
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

        $this->selectedHistory = Application::with('missingSkills')
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
        $this->selectedHistory = Application::with('missingSkills')
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
        Application::where('id', $id)
            ->where('user_id', $userId)
            ->delete();
    }

};
