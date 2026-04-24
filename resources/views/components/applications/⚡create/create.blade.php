<dialog
    id="application-create"
    x-on:show="$el.showModal()"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
    x-on:close="$el.close()"
>
    <section>

        <h2>Create A New Application Process</h2>

        @island
        <form method="POST" wire:submit.stop="createApplication">
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
                <button type="button" @click="$dispatch('close')">Cancel</button>
                <button type="submit" class="cta">Submit</button>
            </section>
        </form>
        @endisland
    </section>
</dialog>
