<dialog
    x-data="{id:0}"
    x-on:show="$el.showModal(); id = event.detail.id"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
>
    <section>
        <h2>Your about delete an application.</h2>
        <p>This actions is not reverable. Are you sure you want to continue?</p>
        <section class="actions">
            <button type="button" @click="$root.close()">Cancel</button>
            <button type="button" class="cta" wire:click="deleteApplication(id)">
                <label wire:loading.remove>OK</label>
                <div class="loading" wire:loading></div>
            </button>
        </section>
    </section>
</dialog>
