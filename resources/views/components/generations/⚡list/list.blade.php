<section id="generations-list">

    <section class="controls">
        <button
            class="cta"
            x-on:click="$wire.dispatchTo('generations.create', 'show')"
        >New Application</button>
        <livewire:generations.create/>
    </section>

    <table>
        <caption>Generated Documents</caption>

        <thead>
            <tr>
                <th>Company</th>
                <th>Job Title</th>
                <th>Create At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($generations as $generation)
                <tr>
                    <td>{{ $generation->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ $generation->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ $generation->created_at->format('M d, Y H:i') }}</td>
                    <td class="actions">
                        <button wire:click="viewHistory({{ $generation->id }})">View</button>
                        <button wire:click="deleteHistory({{ $generation->id }})">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>{{ $generations->links() }}</td>
            </tr>
        </tfoot>
    </table>
</section>
