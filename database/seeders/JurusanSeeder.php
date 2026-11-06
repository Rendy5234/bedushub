<?php

namespace Database\Seeders;

use App\Models\Institusi;
use App\Models\Jurusan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar jurusan default untuk JurusanSeeder.
     *
     * @var array<int, array{nama: string, kode: string, institusi_nama: string}>
     */
    public const JURUSAN = [
        [
            'nama' => 'Teknik Komputer dan Jaringan',
            'kode' => 'TKJ',
            'institusi_nama' => 'SMK Negeri 1 Bedus',
        ],
        [
            'nama' => 'Akuntansi',
            'kode' => 'AK',
            'institusi_nama' => 'SMK Negeri 1 Bedus',
        ],
    ];

    /**
     * Seed jurusan default.
     */
    public function run(): void
    {
        foreach (self::JURUSAN as $jurusan) {
            $institusi = Institusi::where('nama', $jurusan['institusi_nama'])->first();

            if ($institusi) {
                Jurusan::create([
                    'institusi_id' => $institusi->id,
                    'nama' => $jurusan['nama'],
                    'kode' => $jurusan['kode'],
                ]);
            }
        }
    }
}
