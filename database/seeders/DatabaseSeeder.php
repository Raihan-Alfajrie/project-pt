<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LokasiSeeder::class,
            AlatSeeder::class,
            DokumentasiSeeder::class,
            JadwalSeeder::class,
            TargetRkSeeder::class,
            AuditLogSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
