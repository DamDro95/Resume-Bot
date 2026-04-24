<dialog
    x-data="{id:0}"
    x-on:show="$el.showModal()"
    x-on:close="$el.close()"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
>
    <section>
        @island

        <h2>Manage Application</h2>

        <form method="POST" wire:submit.stop="updateApplication">

            <input type="hidden" wire:model="id" requred value="{{ $id }}">

            <label>
                Company
                <input type="text" wire:model="companyName" required/>
                @error('companyName') <label class="error">{{ $message }}</label> @enderror
            </label>

            <label>
                Job Title
                <input type="text" wire:model="jobTitle" required/>
                @error('jobTitle') <label class="error">{{ $message }}</label> @enderror
            </label>

            <label>
                Job Description
                <textarea
                    wire:model="jobDescription"
                    rows="6"
                    placeholder="Paste job description here..."
                    required
                ></textarea>
                @error('jobDescription') <label class="error">{{ $message }}</label> @enderror
            </label>

            <label>
                Additional Instructions
                <textarea
                    wire:model="additionalInstructions"
                    rows="3"
                    placeholder="Add additional instructions for the LLM here..."
                ></textarea>
                @error('additionalInstructions') <label class="error">{{ $message }}</label> @enderror
            </label>

            <section class="actions">
                <button type="button" @click="$root.close()">Cancel</button>
                <button type="submit" class="cta">
                    <label wire:loading.remove>Submit</label>
                    <div class="loader" wire:loading></div>
                </button>
            </section>
        </form>
        @endisland
    </section>
</dialog>
