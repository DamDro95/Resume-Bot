<?php

use App\Enums\GenerationStatus;
use App\Models\Application;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component
{
    use WithPagination;

    public $generations = [];

    public string $company_name = '';

    public string $job_title = '';


    #[On('fetch-and-show')]
    public function fetchAndShow(Application $application){
        $this->generations = $application->generations;
        $this->company_name = $application->company_name;
        $this->job_title = $application->job_title;

        $this->dispatch('show')->self();
    }

    public function displayStatus($status){
        $generationStatus = GenerationStatus::tryFrom($status);
        return $generationStatus->getIcon();
    }
};
