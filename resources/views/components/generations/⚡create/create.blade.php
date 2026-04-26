<dialog
    id="generation-create"
    x-data="{applicationId:@entangle('applicationId')}"
    x-on:show="$el.showModal(); applicationId = event.detail.applicationId"
    x-on:close="$el.close()"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
>
    <section>
        @island

        <h2>Create New A Job</h2>

        <form method="POST" wire:submit.stop="createGeneration">

            <input type="hidden" wire:model="applicationId" x-model="applicationId" requred>

            <label>
                Additional Instructions (optional)
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
