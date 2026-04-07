<?php

use Livewire\Component;
use Livewire\Attributes\Validate;

new class extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $code = '';

    public bool $remember = false;

    public string $step = 'email';

    public bool $auth = false;

    public function rendered(): void
    {
        /* if (!auth()->check()) { */
        if(!$this->auth){
            $this->dispatch('show')->self();
        }
    }

    public function submitEmail(): void{
        $this->validateOnly('email');
        $this->step = 'code';
    }

    public function submitCode(): void{
        $this->validateOnly('code');

        $this->auth = true;
        $this->email='';
        $this->code = '';
        $this->step = '';
        $this->dispatch('close');
        /* $this->dispatch('close-modal', id: 'auth-modal'); */
    }

};
