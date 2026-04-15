<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengajuanApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        // seed baseline categories
        $cat = Category::create(['name' => 'AKD']);
        $sub = SubCategory::create(['category_id' => $cat->id, 'name' => 'Komisi A']);
    }

    public function test_user_can_create_pengajuan(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $category = Category::first();
        $sub = $category->subCategories()->first();

        $payload = [
            'nama_instansi' => 'Instansi Test',
            'atas_nama' => 'Nama Pengaju',
            'jumlah_peserta' => 5,
            'phone' => '08123456789',
            'email' => 'pemohon@example.com',
            'kategori' => $category->id,
            'sub_kategori' => $sub->id,
            'tanggal_kunjungan' => now()->addDays(2)->format('Y-m-d'),
            'waktu_kunjungan' => '10:30',
            'tujuan' => 'Kunjungan kerja',
            'dokumen' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ];

        $res = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/pengajuan', $payload)
            ->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['data' => ['nomor_pengajuan','nama_instansi','status']]);

        $this->assertDatabaseCount('pengajuans', 1);
        $this->assertDatabaseHas('pengajuans', [
            'nama_instansi' => 'Instansi Test',
            'status' => 'pending',
        ]);
    }

    public function test_validation_failure_returns_json(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/pengajuan', [])
            ->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonStructure(['errors' => ['nama_instansi','kategori','dokumen']]);
    }

    public function test_inactive_user_cannot_login_api(): void
    {
        $user = User::factory()->inactive()->create(['email' => 'inactive@example.com']);

        $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'password'
        ])->assertStatus(403)->assertJsonPath('status', false);
    }
}
