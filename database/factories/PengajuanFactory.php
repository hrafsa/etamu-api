<?php

namespace Database\Factories;

use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Pengajuan> */
class PengajuanFactory extends Factory
{
    protected $model = Pengajuan::class;

    public function definition(): array
    {
        $category = Category::query()->inRandomOrder()->first() ?? Category::factory()->create();
        $sub = $category->subCategories()->inRandomOrder()->first() ?? \App\Models\SubCategory::factory()->create(['category_id'=>$category->id]);
        return [
            'user_id' => User::factory(),
            'nomor_pengajuan' => 'PJG-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'nama_instansi' => $this->faker->company(),
            'atas_nama' => $this->faker->name(),
            'jumlah_peserta' => $this->faker->numberBetween(1,50),
            'phone' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->safeEmail(),
            'category_id' => $category->id,
            'sub_category_id' => $sub->id,
            'tanggal_kunjungan' => now()->addDays(rand(1,30))->format('Y-m-d'),
            'waktu_kunjungan' => sprintf('%02d:%02d:00', rand(8,15), rand(0,1)?'00':'30'),
            'tujuan' => $this->faker->sentence(8),
            'dokumen_path' => null,
            'status' => Pengajuan::STATUS_PENDING,
        ];
    }
}

