<?php

use Livewire\Component;
use Livewire\Attributes\Validate;

new class extends Component
{

    #[Validate('required')]
    public string $company_name;

    #[Validate('required')]
    public string $job_title;

    #[Validate('required')]
    public string $job_description;

    #[Validate('required')]
    public string $additional_instructions;

    public function createApplication(): void{

    }
};
