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

                @error('email')
                    <span>{{ $message }}</span>
                @enderror

                <button type="submit" id="email">
                    Continue
                    <div class="loader" wire:loading></div>
                </button>

                <!-- <button id="linkedin" wire:loading.attr="disabled">
                    Continue with LinkedIn
                </button>

                <button id="google" wire:loading.attr="disabled">
                    Continue with Google
                </button> -->

            </form>
        @elseif($step === 'code')
            <form wire:submit="submitCode">
                <h2>Because rent isn't going to pay itself</h2>

                <p>We sent a code to <strong>{{ $email }}</strong></p>

                <label>
                    Code
                    <input type="text" wire:model="code" required>
                </label>

                @error('code')
                    <span>{{ $message }}</span>
                @enderror

                <button type="submit">
                    Verify
                    <div class="loader" wire:loading wire:target="submitCode"></div>
                </button>

                <button type="button" wire:click="resendCode" class="secondary">
                    Resend code
                    <div class="loader" wire:loading wire:target="resendCode"></div>
                </button>

                <button type="button" wire:click="back" class="secondary">
                    Use a different email
                    <div class="loader" wire:loading wire:target="back"></div>
                </button>
            </form>
        @endif
        @endisland
    </section>
</dialog>
