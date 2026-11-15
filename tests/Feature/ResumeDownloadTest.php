<?php

namespace Tests\Feature;

use App\Models\Resume;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResumeDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
        Storage::fake('local');
    }

    public function test_download_returns_not_found_when_no_active_resume_exists(): void
    {
        $this->get(route('resume.download'))->assertNotFound();
    }

    public function test_download_returns_not_found_when_the_file_is_missing(): void
    {
        Resume::query()->create([
            'label' => 'Latest Resume',
            'file_path' => 'resumes/missing.pdf',
            'is_active' => true,
            'uploaded_at' => now(),
        ]);

        $this->get(route('resume.download'))->assertNotFound();
    }

    public function test_active_resume_is_downloaded_as_a_pdf_attachment(): void
    {
        Storage::disk('local')->put('resumes/latest.pdf', '%PDF-1.4 test document');

        Resume::query()->create([
            'label' => 'Latest Resume',
            'file_path' => 'resumes/latest.pdf',
            'is_active' => true,
            'uploaded_at' => now(),
        ]);

        $response = $this->get(route('resume.download'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertDownload('latest-resume.pdf');
    }

    public function test_preview_is_served_inline(): void
    {
        Storage::disk('local')->put('resumes/latest.pdf', '%PDF-1.4 test document');

        Resume::query()->create([
            'label' => 'Latest Resume',
            'file_path' => 'resumes/latest.pdf',
            'is_active' => true,
            'uploaded_at' => now(),
        ]);

        $this->get(route('resume.preview'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename="latest-resume.pdf"');
    }

    public function test_resume_page_renders_the_active_document_actions(): void
    {
        Storage::disk('local')->put('resumes/latest.pdf', '%PDF-1.4 test document');

        Resume::query()->create([
            'label' => 'Latest Resume',
            'file_path' => 'resumes/latest.pdf',
            'is_active' => true,
            'uploaded_at' => now(),
        ]);

        $this->get(route('resume'))
            ->assertOk()
            ->assertSee('Download CV')
            ->assertSee('Open in new tab')
            ->assertSee(route('resume.preview'), false);
    }

    public function test_activating_a_resume_deactivates_the_previous_one(): void
    {
        $old = Resume::query()->create([
            'label' => 'Old Resume',
            'file_path' => 'resumes/old.pdf',
            'is_active' => true,
            'uploaded_at' => now()->subYear(),
        ]);

        Resume::query()->create([
            'label' => 'New Resume',
            'file_path' => 'resumes/new.pdf',
            'is_active' => true,
            'uploaded_at' => now(),
        ]);

        $this->assertFalse($old->fresh()->is_active);
    }
}
