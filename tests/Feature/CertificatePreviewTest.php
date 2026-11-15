<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\CertificateMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificatePreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSiteContext();
        Storage::fake('public');
    }

    public function test_image_certificate_shows_its_file_as_the_card_preview(): void
    {
        Storage::disk('public')->put('certificates/award.jpg', 'fake image');

        Certificate::factory()->create([
            'title' => 'Image Award',
            'file_path' => 'certificates/award.jpg',
            'file_type' => 'image',
        ]);

        $this->get(route('certificates'))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url('certificates/award.jpg'), false);
    }

    public function test_supporting_media_image_is_used_when_the_main_file_is_a_pdf(): void
    {
        Storage::disk('public')->put('certificates/award.pdf', 'fake pdf');
        Storage::disk('public')->put('certificates/documentation/preview.jpg', 'fake image');

        $certificate = Certificate::factory()->create([
            'title' => 'Documented Award',
            'file_path' => 'certificates/award.pdf',
            'file_type' => 'pdf',
        ]);

        CertificateMedia::query()->create([
            'certificate_id' => $certificate->id,
            'path' => 'certificates/documentation/preview.jpg',
            'caption' => 'Supporting image',
            'sort_order' => 1,
        ]);

        $this->get(route('certificates'))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url('certificates/documentation/preview.jpg'), false);
    }

    public function test_the_file_type_is_detected_from_the_extension(): void
    {
        $image = Certificate::factory()->create([
            'file_path' => 'certificates/photo.png',
            'file_type' => null,
        ]);

        $pdf = Certificate::factory()->create([
            'file_path' => 'certificates/document.pdf',
            'file_type' => 'image',
        ]);

        $this->assertSame('image', $image->file_type);
        $this->assertSame('pdf', $pdf->file_type);
        $this->assertTrue($pdf->is_pdf);
    }

    public function test_admin_table_renders_image_previews_and_a_pdf_placeholder(): void
    {
        Storage::disk('public')->put('certificates/award.jpg', 'fake image');

        Certificate::factory()->create([
            'title' => 'Image Preview Certificate',
            'file_path' => 'certificates/award.jpg',
            'file_type' => 'image',
        ]);

        Certificate::factory()->create([
            'title' => 'PDF Preview Certificate',
            'file_path' => 'certificates/document.pdf',
            'file_type' => 'pdf',
        ]);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/certificates')
            ->assertOk()
            ->assertSee(Storage::disk('public')->url('certificates/award.jpg'), false)
            ->assertSee(asset('images/pdf-preview.svg'), false);
    }
}
