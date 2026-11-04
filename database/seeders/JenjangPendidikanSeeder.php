<?php

namespace Database\Seeders;

use App\Models\JenjangPendidikan;
use Illuminate\Database\Seeder;

class JenjangPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Dasar
            ['kode' => 'SD', 'nama' => 'SD/MI', 'kelompok' => 'Dasar', 'is_active' => true, 'urutan' => 1],
            ['kode' => 'SMP', 'nama' => 'SMP/MTs', 'kelompok' => 'Dasar', 'is_active' => true, 'urutan' => 2],

            // Menengah
            ['kode' => 'SMA', 'nama' => 'SMA/MA', 'kelompok' => 'Menengah', 'is_active' => true, 'urutan' => 3],
            ['kode' => 'SMK', 'nama' => 'SMK/MAK', 'kelompok' => 'Menengah', 'is_active' => true, 'urutan' => 4],

            // Tinggi
            ['kode' => 'D1', 'nama' => 'D1', 'kelompok' => 'Tinggi', 'is_active' => true, 'urutan' => 5],
            ['kode' => 'D2', 'nama' => 'D2', 'kelompok' => 'Tinggi', 'is_active' => true, 'urutan' => 6],
            ['kode' => 'D3', 'nama' => 'D3', 'kelompok' => 'Tinggi', 'is_active' => true, 'urutan' => 7],
            ['kode' => 'D4', 'nama' => 'D4', 'kelompok' => 'Tinggi', 'is_active' => true, 'urutan' => 8],
            ['kode' => 'S1', 'nama' => 'S1', 'kelompok' => 'Tinggi', 'is_active' => true, 'urutan' => 9],
            ['kode' => 'S2', 'nama' => 'S2', 'kelompok' => 'Tinggi', 'is_active' => true, 'urutan' => 10],
            ['kode' => 'S3', 'nama' => 'S3', 'kelompok' => 'Tinggi', 'is_active' => true, 'urutan' => 11],
        ];

        foreach ($data as $item) {
            JenjangPendidikan::firstOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
