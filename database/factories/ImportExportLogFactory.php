<?php

namespace Database\Factories;

use App\Models\ImportExportLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImportExportLog>
 */
class ImportExportLogFactory extends Factory
{
    protected $model = ImportExportLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'jenis' => fake()->randomElement(['import', 'export']),
            'modul' => fake()->randomElement(['alat', 'lokasi', 'jadwal', 'audit_log']),
            'nama_file' => fake()->word() . '.xlsx',
            'baris_berhasil' => fake()->numberBetween(1, 50),
            'baris_total' => fake()->numberBetween(1, 50),
            'status' => 'sukses',
        ];
    }
}
