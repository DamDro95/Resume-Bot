<dialog
    id="application-create"
    x-on:show="$el.showModal()"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
    x-on:close="$el.close()"
>
    <section>

        <h2>Create A New Application Process</h2>

        <form wire:submit="createApplication">
            <label>
                Company
                <input type="text"/>
            </label>

            <label>
                Job Title
                <input type="text"/>
            </label>

            <label>
                Job Description
                <textarea
                    rows="6"
                    placeholder="Paste job description here..."
                ></textarea>
            </label>

            <label>
                Additional Instructions
                <textarea
                    rows="3"
                    placeholder="Add additional instructions for the LLM here..."
                ></textarea>
            </label>


            <section class="actions">
                <button type="button" @click="$dispatch('close')">Cancel</button>
                <button type="submit" class="cta">Submit</button>
            </section>
        </form>

    </section>
</dialog>
