<?php
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;

new class extends Component
{

    #[Validate('required')]
    public string $companyName;

    #[Validate('required')]
    public string $jobTitle;

    #[Validate('required')]
    public string $jobDescription;

    public string $additionalInstructions = '';

    public function createApplication(): void{
        $this->validate();

        $userId = Auth::id();

        $application = Application::Create([
            'user_id' => $userId,
            'company_name' => $this->companyName,
            'job_title' => $this->jobTitle,
            'job_description' => $this->jobDescription,
            'additional_instruction' => $this->additionalInstructions,
        ]);

        $this->reset();
        $this->dispatch('application-created', id: $application->id );
        $this->dispatch('close')->self();
    }

    #[On('application-created')]
    public function startGeneration(string $id){
        Log::info('start generation for:' . $id);
    }
};
