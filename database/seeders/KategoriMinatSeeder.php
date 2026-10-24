<?php

namespace Database\Seeders;

use App\Models\KategoriMinat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriMinatSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar kategori minat default untuk KategoriMinatSeeder.
     *
     * @var array<int, array<string, string>>
     */
    public const KATEGORI_MINATS = [
        [
            'nama' => 'Teknologi Informasi',
            'deskripsi' => 'Minat di bidang teknologi informasi dan pengembangan perangkat lunak',
        ],
        [
            'nama' => 'Kreatif',
            'deskripsi' => 'Minat di bidang desain, seni, dan kreativitas',
        ],
        [
            'nama' => 'Bisnis',
            'deskripsi' => 'Minat di bidang bisnis, manajemen, dan kepemilikan usaha',
        ],
    ];

    /**
     * Seed kategori minat default.
     */
    public function run(): void
    {
        foreach (self::KATEGORI_MINATS as $kategoriMinat) {
            KategoriMinat::create([
                'nama' => $kategoriMinat['nama'],
                'deskripsi' => $kategoriMinat['deskripsi'],
                'is_active' => true,
            ]);
        }
    }
}
