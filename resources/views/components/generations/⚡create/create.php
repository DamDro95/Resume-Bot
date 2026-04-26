<?php

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Jobs\GenerateDocuments;

new class extends Component
{
    #[Validate('required')]
    public int $applicationId;

    public string $additionalInstructions = '';

    public function createGeneration(): void{
        Log::info('id:' . $this->applicationId);

        $this->validate();

        Log::info('id:' . $this->applicationId);

        try{
            GenerateDocuments::dispatch(applicationId:$this->applicationId, additionalInstructions:$this->additionalInstructions);

            $this->dispatch('close')->self();
            $this->dispatch('generation-created');
        }catch(\Exception $e){
            Log::info($e->getMessage());
        }
    }
};
