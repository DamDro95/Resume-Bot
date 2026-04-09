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

                @error('email')>{{ $message }}</span> @enderror

                <button type="submit" id="email">
                    Continue
                </button>

                <button id="linkedin">
                    Continue with LinkedIn
                </button>

                <button id="google">
                    Continue with Google
                </button>

            </form>
        @elseif($step === 'code')
            <form wire:submit="submitCode">
                <h2>Because rent isn't going to pay itself</h2>

                <p>We sent a code to <strong>{{ $email }}</strong></p>

                <label>
                    Code
                    <input type="text" wire:model="code" required>
                </label>

                @error('code') <span>{{ $message }}</span> @enderror

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
