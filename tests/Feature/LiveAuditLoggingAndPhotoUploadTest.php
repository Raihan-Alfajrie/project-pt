<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\AuditLog;
use App\Models\DokumentasiPanel;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LiveAuditLoggingAndPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('public');
    }

    public function test_auth_login_and_logout_create_audit_logs(): void
    {
        $user = User::where('jabatan', 'administrator')->first();

        // Login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $user->id,
            'aksi' => 'login',
            'modul' => 'sistem',
        ]);

        // Logout
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $user->id,
            'aksi' => 'logout',
            'modul' => 'sistem',
        ]);
    }

    public function test_alat_crud_creates_live_audit_logs_and_handles_photo_upload(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();
        $file = UploadedFile::fake()->create('rig_test.jpg', 100, 'image/jpeg');

        // 1. Create with photo
        $response = $this->actingAs($admin)->post('/alat', [
            'nama' => 'Rig Cyber 2000',
            'kategori' => 'rig',
            'merek' => 'National Oilwell',
            'model' => 'CYBER-2K',
            'tahun_perolehan' => 2022,
            'no_seri' => 'RIG-CYB-001',
            'status' => 'aktif',
            'foto' => $file,
        ]);
        $response->assertRedirect('/alat');

        $alat = Alat::where('no_seri', 'RIG-CYB-001')->first();
        $this->assertNotNull($alat);
        $this->assertNotNull($alat->foto);
        Storage::disk('public')->assertExists($alat->foto);

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'tambah',
            'modul' => 'alat',
            'keterangan' => "Menambahkan alat: {$alat->nama} ({$alat->no_seri})",
        ]);

        // 2. Update with new photo
        $newFile = UploadedFile::fake()->create('rig_updated.png', 100, 'image/png');
        $oldFoto = $alat->foto;

        $response = $this->actingAs($admin)->put('/alat/' . $alat->id, [
            'nama' => 'Rig Cyber 2000 Updated',
            'kategori' => 'rig',
            'merek' => 'National Oilwell',
            'model' => 'CYBER-2K-V2',
            'tahun_perolehan' => 2023,
            'no_seri' => 'RIG-CYB-001',
            'status' => 'standby',
            'foto' => $newFile,
        ]);
        $response->assertRedirect('/alat');

        $alat->refresh();
        $this->assertSame('Rig Cyber 2000 Updated', $alat->nama);
        Storage::disk('public')->assertMissing($oldFoto);
        Storage::disk('public')->assertExists($alat->foto);

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'edit',
            'modul' => 'alat',
            'keterangan' => "Memperbarui data alat: {$alat->nama}",
        ]);

        // 3. Delete
        $fotoToDelete = $alat->foto;
        $response = $this->actingAs($admin)->delete('/alat/' . $alat->id);
        $response->assertRedirect('/alat');

        Storage::disk('public')->assertMissing($fotoToDelete);
        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'hapus',
            'modul' => 'alat',
            'keterangan' => "Menghapus data alat: {$alat->nama}",
        ]);
    }

    public function test_lokasi_crud_creates_live_audit_logs(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // 1. Create
        $response = $this->actingAs($admin)->post('/lokasi', [
            'kode' => 'TST-01',
            'nama_tempat' => 'Lapangan Uji Coba',
            'provinsi' => 'Sumatera Selatan',
        ]);
        $response->assertRedirect('/lokasi');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'tambah',
            'modul' => 'lokasi',
            'keterangan' => 'Menambahkan lokasi: Lapangan Uji Coba (TST-01)',
        ]);

        $lokasi = Lokasi::where('kode', 'TST-01')->first();

        // 2. Update
        $response = $this->actingAs($admin)->put('/lokasi/' . $lokasi->id, [
            'kode' => 'TST-01',
            'nama_tempat' => 'Lapangan Uji Coba Baru',
            'provinsi' => 'Sumatera Selatan',
        ]);
        $response->assertRedirect('/lokasi');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'edit',
            'modul' => 'lokasi',
            'keterangan' => 'Memperbarui data lokasi: Lapangan Uji Coba Baru (TST-01)',
        ]);

        // 3. Delete
        $response = $this->actingAs($admin)->delete('/lokasi/' . $lokasi->id);
        $response->assertRedirect('/lokasi');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'hapus',
            'modul' => 'lokasi',
            'keterangan' => 'Menghapus data lokasi: Lapangan Uji Coba Baru (TST-01)',
        ]);
    }

    public function test_user_crud_creates_live_audit_logs(): void
    {
        $admin = User::where('jabatan', 'administrator')->first();

        // 1. Create
        $response = $this->actingAs($admin)->post('/users', [
            'nama' => 'User Testing Baru',
            'username' => 'user.testing',
            'email' => 'user.testing@gmail.com',
            'password' => 'secret123',
            'jabatan' => 'pekerja',
            'status' => 'online',
        ]);
        $response->assertRedirect('/users');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'tambah',
            'modul' => 'user',
            'keterangan' => 'Menambahkan akun user: User Testing Baru (@user.testing)',
        ]);

        $user = User::where('username', 'user.testing')->first();

        // 2. Update
        $response = $this->actingAs($admin)->put('/users/' . $user->id, [
            'nama' => 'User Testing Diubah',
            'username' => 'user.testing',
            'email' => 'user.testing@gmail.com',
            'jabatan' => 'pekerja',
            'status' => 'offline',
        ]);
        $response->assertRedirect('/users');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'edit',
            'modul' => 'user',
            'keterangan' => 'Memperbarui data user: User Testing Diubah (@user.testing)',
        ]);

        // 3. Update permissions
        $response = $this->actingAs($admin)->put('/users/' . $user->id . '/permissions', [
            'alat' => '1',
            'lokasi' => '0',
            'jadwal' => '1',
        ]);
        $response->assertRedirect('/users');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'edit',
            'modul' => 'user',
            'keterangan' => 'Memperbarui hak akses modul user: User Testing Diubah',
        ]);

        // 4. Delete
        $response = $this->actingAs($admin)->delete('/users/' . $user->id);
        $response->assertRedirect('/users');

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'aksi' => 'hapus',
            'modul' => 'user',
            'keterangan' => 'Menghapus akun user: User Testing Diubah (@user.testing)',
        ]);
    }

    public function test_profile_photo_upload_and_removal_works(): void
    {
        $user = User::where('jabatan', 'pekerja')->first();
        $avatarFile = UploadedFile::fake()->create('my_avatar.jpg', 100, 'image/jpeg');

        // Upload new avatar
        $response = $this->actingAs($user)->patch('/profile', [
            'nama' => $user->nama,
            'email' => $user->email,
            'foto_profil' => $avatarFile,
        ]);
        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->foto_profil);
        Storage::disk('public')->assertExists($user->foto_profil);

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $user->id,
            'aksi' => 'edit',
            'modul' => 'user',
            'keterangan' => 'Memperbarui profil akun sendiri',
        ]);

        // Remove avatar
        $oldPhoto = $user->foto_profil;
        $response = $this->actingAs($user)->patch('/profile', [
            'nama' => $user->nama,
            'email' => $user->email,
            'hapus_foto' => '1',
        ]);
        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertNull($user->foto_profil);
        Storage::disk('public')->assertMissing($oldPhoto);
    }
}
