<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Application;

new class extends Component
{

    use WithPagination;

    public function render(){

        $applications = Application::Paginate(10);

        return $this->view([
            'applications' => $applications,
        ]);
    }
};
