<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use App\Models\Generation;
use App\Models\Application;

new class extends Component
{
    use WithPagination;

    public string $company_name = '';

    public string $job_title = '';

    public $missingSkills = ['asd'];


    #[On('fetch-and-show')]
    public function fetchAndShow(Generation $generation){
        $this->company_name = $generation->application->company_name;
        $this->job_title = $generation->application->job_title;

        $this->dispatch('show')->self();
    }
};
