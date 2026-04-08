<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use App\Models\Auth\VerificationCode;
use App\Notifications\VerificationCodeNotification;

new class extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|digits:6')]
    public string $code = '';

    public bool $remember = false;

    public string $step = 'email';

    public bool $auth = false;

    public function rendered(): void
    {
        if (!auth()->check()) {
            $this->dispatch('show')->self();
        }
    }

    #[On('auth-logout')]
    public function authLogout(): void{
        $this->dispatch('show')->self();
    }

    public function submitEmail(): void{
        $this->validateOnly('email');

        $key = 'send-code:' . $this->email;
        if (RateLimiter::tooManyAttempts($key, 30)) {
            $this->addError('email', 'Too many attempts. Try again later.');
            return;
        }
        RateLimiter::hit($key, 60 * 10);

        $code = (string) random_int(100000, 999999);

        VerificationCode::updateOrCreate(
            ['email' => $this->email],
            ['code' => bcrypt($code), 'expires_at' => now()->addMinutes(10)]
        );

        // check if user exists — if not they'll register after code verify
        $user = User::firstOrNew(['email' => $this->email]);
        $user->notify(new VerificationCodeNotification($code));

        $this->step = 'code';
    }

    public function submitCode(): void{
        $this->validateOnly('code');

        $record = VerificationCode::where('email', $this->email)->first();

        if (!$record || $record->isExpired() || !password_verify($this->code, $record->code)) {
            $this->addError('code', 'Invalid or expired code.');
            return;
        }

        $record->delete();

        $user = User::firstOrCreate(['email' => $this->email]);

        Auth::login($user, remember: true);
        $this->reset();
        $this->dispatch('close')->self();
        $this->dispatch('auth-success');
    }

    public function resendCode(): void
    {
        $this->submitEmail();
    }

};
