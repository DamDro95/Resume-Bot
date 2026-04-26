<dialog
    id="generation-list"
    x-on:show="$el.showModal()"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
    x-on:close="$el.close()"
>

    <livewire:generations.document/>

    <section>
        <h2>Generated Documents</h2>

        <section id="application-details">
            <h4>Company</h4>
            <h4>Job</h4>
            <label>{{ $company_name }}</label>
            <label>{{ $job_title }}</label>
        </section>

        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($generations as $generation)
                    <tr>
                        <td>{!! $this->displayStatus( $generation->status ) !!}</td>
                        <td>{{ $generation->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <section class="actions">
                                @if($generation->status === \App\Enums\GenerationStatus::Completed->value)
                                <button
                                    type="button"
                                    x-on:click="$wire.dispatchTo('generations.document', 'show', { 'document': '{{$generation->cover_letter_text}}' } )"
                                >
                                    <label>Cover Letter</label>
                                </button>

                                <button
                                    type="button"
                                    x-on:click="$wire.dispatchTo('generations.document', 'show', { 'document': '{{$generation->resume_text}}' } )"
                                >
                                    <label>Resume</label>
                                </button>
                                @endif
                            </section>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="actions">
            <button type="button" @click="$root.close()">Cancel</button>
            <button type="submit" class="cta">
                <label wire:loading.remove>Submit</label>
                <div class="loader" wire:loading></div>
            </button>
        </section>

    </section>
</dialog>

