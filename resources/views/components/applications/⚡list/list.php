<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Application;
use Illuminate\Support\Facades\Log;

new class extends Component
{

    use WithPagination;

    public function render(){

        $applications = Application::latest()->Paginate(10);

        return $this->view([
            'applications' => $applications,
        ]);
    }
};
