<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Daftar user demo untuk setiap role.
     *
     * @var array<int, array{name: string, email: string, role: string}>
     */
    private const USERS = [
        [
            'name' => 'Super Admin',
            'email' => 'superadmin@bedushub.test',
            'role' => 'super_admin',
        ],
        [
            'name' => 'Admin Dinas Pendidikan',
            'email' => 'disdik@bedushub.test',
            'role' => 'admin_disdik',
        ],
        [
            'name' => 'Admin Dinas Tenaga Kerja',
            'email' => 'disnaker@bedushub.test',
            'role' => 'admin_disnaker',
        ],
        [
            'name' => 'Admin UMKM',
            'email' => 'umkm@bedushub.test',
            'role' => 'admin_umkm',
        ],
        [
            'name' => 'Mitra Perusahaan',
            'email' => 'perusahaan@bedushub.test',
            'role' => 'perusahaan',
        ],
        [
            'name' => 'Siswa Demo',
            'email' => 'siswa@bedushub.test',
            'role' => 'siswa',
        ],
        [
            'name' => 'Mahasiswa Demo',
            'email' => 'mahasiswa@bedushub.test',
            'role' => 'mahasiswa',
        ],
        [
            'name' => 'Alumni Demo',
            'email' => 'alumni@bedushub.test',
            'role' => 'alumni',
        ],
    ];

    /**
     * Seed user demo dan assign role sesuai perannya.
     */
    public function run(): void
    {
        foreach (self::USERS as $data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password',
                ],
            );

            if ($user->email_verified_at === null) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            $user->assignRole($data['role']);
        }
    }
}
