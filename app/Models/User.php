<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser // <-- TAMBAHKAN INTERFACE INI
{
    use HasRoles;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Menentukan panel mana yang boleh diakses oleh user.
     */
        public function canAccessPanel(Panel $panel): bool
    {
        // Super Admin bebas masuk
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return match ($panel->getId()) {
            'admin' => $this->hasRole(['admin_disdik', 'admin_disnaker']),
            'perusahaan' => $this->hasRole('perusahaan'),
            
            // Ubah 'siswa' menjadi 'dashboard', dan tampung 3 role
            'dashboard' => $this->hasRole(['siswa', 'mahasiswa', 'alumni']),
            
            default => false,
        };
    }
}