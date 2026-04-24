<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GeneratedDocument;

new class extends Component
{

    use WithPagination;

    public function render(){

        $generations = GeneratedDocument::Paginate(10);

        return $this->view([
            'generations' => $generations,
        ]);
    }
};
