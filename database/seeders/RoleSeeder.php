<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Daftar role dasar aplikasi Bedushub.
     *
     * @var array<int, string>
     */
    public const ROLES = [
        'super_admin',
        'admin_disdik',
        'admin_disnaker',
        'admin_umkm',
        'perusahaan',
        'siswa',
        'mahasiswa',
        'alumni',
    ];

    /**
     * Seed role dasar aplikasi.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::ROLES as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }
    }
}
