<?php
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Application;

new class extends Component
{
    public function deleteApplication(int $id){
        try{
            $application = Application::findOrFail($id);
            $application->delete();

            $this->dispatch('close')->self();
            $this->dispatch('application-deleted');
        }catch(\Exception $e){
            Log::info($e->getMessage());
        }
    }
};
