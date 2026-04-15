<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        User::factory()->create([
            'name' => 'Admin Root',
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'role' => 'admin',
            'status' => true,
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'phone' => '1234567891',
            'role' => 'user',
            'status' => true,
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'phone' => '1234567892',
            'role' => 'user',
            'status' => false,
            'password' => bcrypt('password'),
        ]);

        // Categories
        $akd = Category::firstOrCreate(['name' => 'AKD']);
        $sek = Category::firstOrCreate(['name' => 'Sekretaris Dewan']);

        foreach (
            [
                'Pimpinan Dewan',
                'Komisi A',
                'Komisi B',
                'Komisi C',
                'Komisi D',
                'Komisi E',
                'Badan Anggaran',
                'Badan Kehormatan',
                'Badan Musyawarah',
                'Badan Pembentukan Peraturan Daerah',
            ] as $komisi) {
            SubCategory::firstOrCreate(['category_id' => $akd->id, 'name' => $komisi]);
        }

        foreach (
            [
                'Bagian Umum',
                'Bagian Perencanaan dan Keuangan',
                'Bagian Produk Hukum dan Persidangan',
                'Hubungan Masyarakat dan Protokol',
            ] as $name) {
            SubCategory::firstOrCreate(['category_id' => $sek->id, 'name' => $name]);
        }
    }
}
