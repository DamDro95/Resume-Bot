<main id="dashboard">

    <h1>Start Generating</h1>

    <section>

        <h2>Upload Documents (PDF/DOCX)</h2>

        <div class="horizontal">

            <label class="horizontal">
                <input type="file" wire:model="resume" accept=".pdf,.doc,.docx">
                Upload Resume
                <div class="loader" wire:loading wire:target="resume"></div>
                <i title="uploaded" wire:show="resumeExists" wire:loading.remove wire:target="resume">✓</i>
            </label>

            <label class="horizontal">
                <input type="file" wire:model="coverLetter" accept=".pdf,.doc,.docx">
                Upload Cover Letter
                <div class="loader" wire:loading wire:target="coverLetter"></div>
                <i title="uploaded" wire:show="coverLetterExists" wire:loading.remove wire:target="coverLetter">✓</i>
            </label>
        </div>
    </section>

    <section>
        <form wire:submit.prevent="analyze">

            <h2>Enter Details</h2>

            <label>
                Job Description
                <textarea
                    id="job-description"
                    wire:model="jobDescription"
                    rows="6"
                    class="form-textarea"
                    placeholder="Paste job description here..."
                ></textarea>
            </label>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="analyze"
                class="cta"
            >
                Analyze & Generate
                <div class="loader" wire:loading wire:target="analyze"></div>
            </button>
        </form>
    </section>

    {{--@if($history->count() > 0)
        <section>
            <h2>History</h2>
            <table>
                <thead>
                    <tr>
                        <td><h3>ID</h3></td>
                        <td><h3>Actions</h3></td>
                    </tr>
                <thead>
                <tbody>
                    @foreach($history as $item)
                        <tr>
                            <td>
                                <span>{{ $item->created_at->format('M d, Y H:i') }}</span>
                            </td>
                            <td>
                                <button wire:click="viewHistory({{ $item->id }})">View</button>
                                @if($item->has_missing_skills)
                                    <button wire:click="viewMissingSkills({{ $item->id }})">Skills</button>
                                @endif
                                <button wire:click="deleteHistory({{ $item->id }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <ul>
            </ul>
            <div>
                @if($history->previousPageUrl())
                    <button wire:click="previousPage">Previous</button>
                @endif
                @if($history->nextPageUrl())
                    <button wire:click="nextPage">Next</button>
                @endif
            </div>
        </section>
    @endif

    <livewire:modal id="history" :title="$selectedHistoryTitle">
        <livewire:slot name="content">
            @if($selectedHistory && $selectedHistory->has_missing_skills)
                <div role="alert" class="horizontal">
                    <p>Missing skills identified for this job application.</p>
                    <button wire:click="viewMissingSkills($selectedHistory->id)">Add Skill Responses</button>
                </div>
            @endif

            <section class="horizontal top">
                <section class="document-result">
                    <header>
                        <h3>Resume</h3>
                        <button type="button" class="cta" wire:click="copyToClipboard('resume')">Copy</button>
                    </header>
                    <pre>{{ $resumeText }}</pre>
                </section>

                <section class="document-result">
                    <header>
                        <h3>Cover Letter</h3>
                        <button type="button" class="cta" wire:click="copyToClipboard('coverLetter')">Copy</button>
                    </header>
                    <pre>{{ $coverLetterText }}</pre>
                </section>
            </section>
        </livewire:slot>
    </livewire:modal>

    <livewire:modal id="missing-skills" title="Missing Skills">
        @if($selectedHistory && $selectedHistory->missingSkills)
            <section>
                <p>Describe your experience with each skill:</p>

                <table>
                    <tbody>
                        @foreach($selectedHistory->missingSkills as $skill)
                            <tr>
                                <label>
                                    {{ $skill->skill_name }}
                                    <textarea
                                        wire:model="skillDescriptions.{{ $skill->skill_name }}"
                                        rows="2"
                                        class="skill-textarea"
                                        placeholder="Describe your experience with {{ $skill->skill_name }}..."
                                    ></textarea>
                                </label>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="horizontal">
                    <button wire:click="saveSkillResponses($wire.get('skillDescriptions'))">
                        Save Only
                    </button>
                    <button
                        wire:click="saveSkillResponses($wire.get('skillDescriptions')); regenerate($wire.get('skillDescriptions'))"
                        wire:loading.attr="disabled"
                        wire:target="saveSkillResponses"
                    >
                        Save & Regenerate
                    </button>
                </div>
            </section>
        @endif
    </livewire:modal>
--}}
</main>
