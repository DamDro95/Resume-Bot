<dialog
    id="generation-document"
    x-data="{document:''}"
    x-on:show="$el.showModal(); document = event.detail.document"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
    x-on:close="$el.close()"
>
    <section>
        <h2>Results</h2>

        <p x-text="document"></p>

        <section class="actions">
            <button type="button" @click="$root.close()">Cancel</button>
        </section>

    </section>
</dialog>

