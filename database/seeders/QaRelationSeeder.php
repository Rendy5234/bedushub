<?php

namespace Database\Seeders;

use App\Models\KategoriMinat;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QaRelationSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'siswa@bedushub.test')->first();
        $skill = Skill::where('nama', 'PHP')->first();
        $kategoriMinat = KategoriMinat::where('nama', 'Teknologi Informasi')->first();

        if ($user && $skill) {
            $user->userSkills()->create([
                'skill_id' => $skill->id,
                'level' => 'mahir',
            ]);

            $this->command->info("Skill {$skill->nama} berhasil dilampirkan ke user {$user->email}");
        }

        if ($user && $kategoriMinat) {
            $user->userMinat()->create([
                'kategori_minat_id' => $kategoriMinat->id,
            ]);

            $this->command->info("Kategori minat {$kategoriMinat->nama} berhasil dilampirkan ke user {$user->email}");
        }
    }
}
