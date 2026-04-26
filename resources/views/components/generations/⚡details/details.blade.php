<dialog
    id="generation-details"
    x-on:show="$el.showModal()"
    x-on:click="$event.target.nodeName === 'DIALOG' && $el.close()"
    x-on:close="$el.close()"
>
    <section>
        <h2>Results</h2>

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
                @foreach ($missingSkills as $skill)
                    <tr>
                        <td>asd</td>
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

