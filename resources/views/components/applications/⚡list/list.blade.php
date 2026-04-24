<section id="applications-list">

    <livewire:applications.create @application-created="$refresh"/>

    <section class="controls">
        <button
            class="cta"
            x-on:click="$wire.dispatchTo('applications.create', 'show')"
        >New Application</button>
    </section>

    <table>
        <caption>Applications</caption>

        <thead>
            <tr>
                <th>Company</th>
                <th>Job Title</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applications as $application)
                <tr>
                    <td>{{ $application->company_name }}</td>
                    <td>{{ $application->job_title }}</td>
                    <td>{{ $application->created_at->format('M d, Y H:i') }}</td>
                    <td class="actions">
                        <button wire:click="viewHistory({{ $application->id }})">View</button>
                        <button wire:click="deleteApplication({{ $application->id }})">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>{{ $applications->links() }}</td>
            </tr>
        </tfoot>
    </table>

    @if(count($applications) < 1)
        <section class="getting-started">
            <p>Upload a resume and/or cover letter and start applying.</p>
            <button
                class="cta"
                x-on:click="$wire.dispatchTo('applications.create', 'show')"
            >First Application</button>
        </section>

    @endif
</section>
