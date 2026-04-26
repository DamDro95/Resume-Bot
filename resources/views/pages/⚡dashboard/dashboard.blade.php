<main id="dashboard">

    <h1>Start Generating</h1>

    <section>

        <h2>Upload Documents (PDF/DOCX)</h2>

        <div class="row">

            <label class="row">
                <input type="file" wire:model="resume" accept=".pdf">
                Upload Resume
                <div class="loader" wire:loading wire:target="resume"></div>
                <i title="uploaded" wire:show="resumeExists" wire:loading.remove wire:target="resume">✔</i>
            </label>

            <label class="row">
                <input type="file" wire:model="coverLetter" accept=".pdf">
                Upload Cover Letter
                <div class="loader" wire:loading wire:target="coverLetter"></div>
                <i title="uploaded" wire:show="coverLetterExists" wire:loading.remove wire:target="coverLetter">✔</i>
            </label>
        </div>
    </section>

    <livewire:applications.list/>

    {{--<section>
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

            @if(!$this->isGenerating)
                <button
                    type="submit"
                    class="cta"
                >
                    Generate
                </button>
            @else
                <button
                    type="submit"
                    wire:poll.5="checkGeneration"
                    class="cta"
                    disabled
                >
                    Generating...
                    <div class="loader"></div>
                </button>
            @endif

        </form>
    </section> --}}
</main>
