<?php

use Livewire\Component;

new class extends Component
{
    public string $title = 'Are you sure?';
    public string $message = 'This action is not reverable.';
};
?>

<dialog
    x-data="{data:{}}"
    x-on:show="$el.showModal(); data = event.detail"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
>
    <section>
        <h2>{{ $title }}</h2>
        <p>{{ $message }}</p>
        <section class="actions">
            <button type="button" @click="$root.close()">Cancel</button>
            <button type="button" class="cta" @click="$dispatch('confirmed', data );$root.close();">OK</button>
        </section>
    </section>
</dialog>
