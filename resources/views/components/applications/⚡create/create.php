<?php

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Jobs\GenerateDocuments;

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
        ]);

        GenerateDocuments::dispatch($application->id, $this->additionalInstructions);

        $this->reset();
        $this->dispatch('application-created', id: $application->id );
        $this->dispatch('close')->self();
    }
};
