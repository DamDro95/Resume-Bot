<?php

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use App\Models\Application;
use Illuminate\Support\Facades\App;

new class extends Component
{
    #[Locked]
    public int $id;

    #[Validate('required')]
    public string $companyName;

    #[Validate('required')]
    public string $jobTitle;

    #[Validate('required')]
    public string $jobDescription;

    public string $additionalInstructions;

    #[On('load-application')]
    public function load(Application $application): void{
        try{
            $this->id = $application->id;
            $data = collect($application->toArray())
                ->mapWithKeys(fn($value, $key) => [Str::camel($key) => $value])
                ->all();
            $this->fill($data);
            $this->dispatch('show')->self();
        }catch(\Exception $e){
            Log::info($e->getMessage());
        }
    }

    public function updateApplication(): void{
        $this->validate();

        Log::info('test:' . $this->id);

        try{
            $application = Application::findOrFail($this->id);
            $application->updateOrFail([
                'company_name' => $this->companyName,
                'job_title' => $this->jobTitle,
                'job_description' => $this->jobDescription,
                'additional_instructions' => $this->additionalInstructions,
            ]);

            $this->dispatch('close')->self();
            $this->dispatch('application-updated');
        }catch(\Exception $e){
            Log::info($e->getMessage());
        }
    }
};
