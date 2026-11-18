<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_view_public_faq_page_and_seeded_items(): void
    {
        $response = $this->get('/faq');

        $response->assertOk();
        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Pusat Bantuan &amp; FAQ Publik', false);
        $response->assertSee('Ajukan Pertanyaan');
    }

    public function test_guest_can_submit_faq_question_and_triggers_admin_notification(): void
    {
        $response = $this->post('/faq/tanya', [
            'nama' => 'Pengunjung Tamu',
            'email' => 'tamu@pertamina.com',
            'instansi' => 'Mitra Rig Services',
            'kategori' => 'alat_rig',
            'pertanyaan' => 'Berapa kapasitas horse power maksimum rig di Limau?',
        ]);

        $response->assertRedirect('/faq?view=public');
        $response->assertSessionHas('success_pertanyaan');

        $this->assertDatabaseHas('kontak_bantuan', [
            'nama' => 'Pengunjung Tamu',
            'email' => 'tamu@pertamina.com',
            'kategori' => 'pertanyaan_faq',
        ]);
    }

    public function test_pekerja_can_view_faq_page_and_seeded_items(): void
    {
        $user = User::where('jabatan', 'pekerja')->first();

        $response = $this->actingAs($user)->get('/faq');

        $response->assertOk();
        $response->assertSee('Pusat Bantuan &amp; FAQ', false);
        $response->assertSee('Operasional Rig &amp; Alat', false);
        $response->assertSee('Butuh Bantuan Operasional Tambahan?');
        $response->assertDontSee('Tambah FAQ');
    }

    public function test_pekerja_cannot_create_or_modify_faq(): void
    {
        $user = User::where('jabatan', 'pekerja')->first();
        $faq = Faq::first();

        // Coba Create
        $responseCreate = $this->actingAs($user)->post('/faq', [
            'pertanyaan' => 'Pertanyaan terlarang',
            'jawaban' => 'Jawaban terlarang',
            'kategori' => 'alat_rig',
        ]);
        $responseCreate->assertForbidden();

        // Coba Update
        $responseUpdate = $this->actingAs($user)->put("/faq/{$faq->id}", [
            'pertanyaan' => 'Pertanyaan diubah terlarang',
            'jawaban' => 'Jawaban diubah terlarang',
            'kategori' => 'alat_rig',
        ]);
        $responseUpdate->assertForbidden();

        // Coba Delete
        $responseDelete = $this->actingAs($user)->delete("/faq/{$faq->id}");
        $responseDelete->assertForbidden();
    }

    public function test_admin_can_view_faq_with_management_controls(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->get('/faq');

        $response->assertOk();
        $response->assertSee('Tambah FAQ');
    }

    public function test_admin_can_create_faq_and_generates_audit_log(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->post('/faq', [
            'pertanyaan' => 'Bagaimana prosedur pergantian shift kru rig?',
            'jawaban' => 'Pergantian shift wajib diawali dengan safety briefing dan serah terima checklist harian.',
            'kategori' => 'alat_rig',
            'urutan' => 12,
        ]);

        $response->assertRedirect('/faq');
        $this->assertDatabaseHas('faqs', [
            'pertanyaan' => 'Bagaimana prosedur pergantian shift kru rig?',
            'kategori' => 'alat_rig',
            'urutan' => 12,
        ]);

        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'tambah',
            'modul' => 'sistem',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_update_faq_and_generates_audit_log(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();
        $faq = Faq::first();

        $response = $this->actingAs($admin)->put("/faq/{$faq->id}", [
            'pertanyaan' => 'Pertanyaan yang telah diedit secara resmi',
            'jawaban' => 'Jawaban telah disesuaikan dengan SOP terbaru.',
            'kategori' => 'jadwal_gantt',
            'urutan' => 5,
        ]);

        $response->assertRedirect('/faq');
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'pertanyaan' => 'Pertanyaan yang telah diedit secara resmi',
            'kategori' => 'jadwal_gantt',
        ]);

        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'edit',
            'modul' => 'sistem',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_delete_faq_and_generates_audit_log(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();
        $faq = Faq::first();
        $faqId = $faq->id;

        $response = $this->actingAs($admin)->delete("/faq/{$faqId}");

        $response->assertRedirect('/faq');
        $this->assertDatabaseMissing('faqs', [
            'id' => $faqId,
        ]);

        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'hapus',
            'modul' => 'sistem',
            'user_id' => $admin->id,
        ]);
    }

    public function test_validation_fails_on_empty_fields(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        $response = $this->actingAs($admin)->post('/faq', [
            'pertanyaan' => '',
            'jawaban' => '',
            'kategori' => 'invalid_cat',
        ]);

        $response->assertSessionHasErrors(['pertanyaan', 'jawaban', 'kategori']);
    }
}
