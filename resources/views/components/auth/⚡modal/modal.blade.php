<dialog
    id="auth-modal"
    x-on:show="$el.showModal()"
    x-on:close="$el.close()"
>
    <section>

        <img src="/images/resume-bot-login.svg" />

        @island
        @if($step === 'email')
            <form wire:submit="submitEmail">

                <h2>Because rent isn't going to pay itself</h2>

                <label>
                    Email
                    <input type="email" wire:model="email" required>
                </label>

                <label class="horizontal">
                    <input type="checkbox" wire:model="remember">
                    Remember me
                </label>

                @error('email') <span class="error">{{ $message }}</span> @enderror

                <button type="submit">
                    Continue
                </button>

                <a href="{{ route('auth.redirect', 'google') }}" class="btn-oauth">
                    Continue with Google
                </a>
                <a href="{{ route('auth.redirect', 'linkedin-openid') }}" class="btn-oauth">
                    Continue with LinkedIn
                </a>

            </form>
        @elseif($step === 'code')
            <form wire:submit="submitCode">
                <h2>Because rent isn't going to pay itself</h2>

                <p>We sent a code to <strong>{{ $email }}</strong></p>

                <label>
                    Code
                    <input type="text" wire:model="code" required>
                </label>

                @error('code') <span class="error">{{ $message }}</span> @enderror

                <button type="submit">
                    Verify
                </button>
                <button type="button" wire:click="resendCode">Resend code</button>
                <button type="button" wire:click="$set('step', 'email')">Use a different email</button>
            </form>
        @endif
        @endisland
    </section>
</dialog>
