<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\Institusi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakultasSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar fakultas default untuk FakultasSeeder.
     *
     * @var array<int, array{nama: string, institusi_nama: string}>
     */
    public const FAKULTAS = [
        [
            'nama' => 'Fakultas Teknik',
            'institusi_nama' => 'Universitas Bedus',
        ],
        [
            'nama' => 'Fakultas Ekonomi',
            'institusi_nama' => 'Universitas Bedus',
        ],
    ];

    /**
     * Seed fakultas default.
     */
    public function run(): void
    {
        foreach (self::FAKULTAS as $fakultas) {
            $institusi = Institusi::where('nama', $fakultas['institusi_nama'])->first();

            if ($institusi) {
                Fakultas::create([
                    'institusi_id' => $institusi->id,
                    'nama' => $fakultas['nama'],
                ]);
            }
        }
    }
}
