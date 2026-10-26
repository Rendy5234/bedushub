<?php

namespace Database\Seeders;

use App\Enums\JenisInstitusi;
use App\Models\Institusi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstitusiSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar institusi default untuk InstitusiSeeder.
     *
     * @var array<int, array{nama: string, jenis: JenisInstitusi, kecamatan: string, alamat: string}>
     */
    public const INSTITUSI = [
        [
            'nama' => 'SMK Negeri 1 Bedus',
            'jenis' => JenisInstitusi::Smk,
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Raya Bedus No. 1, Bojonegoro',
        ],
        [
            'nama' => 'Universitas Bedus',
            'jenis' => JenisInstitusi::Pt,
            'kecamatan' => 'Bojonegoro',
            'alamat' => 'Jl. Pendidikan No. 10, Bojonegoro',
        ],
    ];

    /**
     * Seed institusi default.
     */
    public function run(): void
    {
        foreach (self::INSTITUSI as $institusi) {
            Institusi::create([
                'nama' => $institusi['nama'],
                'jenis' => $institusi['jenis'],
                'kecamatan' => $institusi['kecamatan'],
                'alamat' => $institusi['alamat'],
                'is_active' => true,
            ]);
        }
    }
}
