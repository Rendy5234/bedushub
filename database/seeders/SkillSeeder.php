<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Daftar skill default untuk SkillSeeder.
     *
     * @var array<int, array<string, string>>
     */
    public const SKILLS = [
        [
            'nama' => 'PHP',
            'kategori' => 'Programming',
        ],
        [
            'nama' => 'Laravel',
            'kategori' => 'Programming',
        ],
        [
            'nama' => 'Vue.js',
            'kategori' => 'Programming',
        ],
        [
            'nama' => 'Desain Grafis',
            'kategori' => 'Design',
        ],
        [
            'nama' => 'Digital Marketing',
            'kategori' => 'Marketing',
        ],
    ];

    /**
     * Seed skill default.
     */
    public function run(): void
    {
        foreach (self::SKILLS as $skill) {
            Skill::create([
                'nama' => $skill['nama'],
                'kategori' => $skill['kategori'],
                'is_active' => true,
            ]);
        }
    }
}
