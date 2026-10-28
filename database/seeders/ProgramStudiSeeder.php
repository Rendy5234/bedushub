<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar program studi default untuk ProgramStudiSeeder.
     *
     * @var array<int, array{nama: string, kode: string, jenjang: string, fakultas_nama: string}>
     */
    public const PROGRAM_STUDI = [
        [
            'nama' => 'Teknik Informatika',
            'kode' => 'IF001',
            'jenjang' => 'S1',
            'fakultas_nama' => 'Fakultas Teknik',
        ],
        [
            'nama' => 'Akuntansi',
            'kode' => 'AK001',
            'jenjang' => 'S1',
            'fakultas_nama' => 'Fakultas Ekonomi',
        ],
    ];

    /**
     * Seed program studi default.
     */
    public function run(): void
    {
        foreach (self::PROGRAM_STUDI as $prodi) {
            $fakultas = Fakultas::where('nama', $prodi['fakultas_nama'])->first();

            if ($fakultas) {
                ProgramStudi::firstOrCreate(
                    [
                        'nama' => $prodi['nama'],
                        'fakultas_id' => $fakultas->id,
                    ],
                    [
                        'kode' => $prodi['kode'],
                        'jenjang' => $prodi['jenjang'],
                    ]
                );
            }
        }
    }
}
