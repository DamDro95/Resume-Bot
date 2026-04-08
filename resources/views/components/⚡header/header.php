<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{

    #[On('auth-success')]
    public function authSuccess(): void{

    }

    public function logout(){
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        $this->authenticated = false;
        $this->dispatch('auth-logout');
    }
};
