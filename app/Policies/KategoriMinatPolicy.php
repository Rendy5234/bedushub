<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\KategoriMinat;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class KategoriMinatPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KategoriMinat');
    }

    public function view(AuthUser $authUser, KategoriMinat $kategoriMinat): bool
    {
        return $authUser->can('View:KategoriMinat');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KategoriMinat');
    }

    public function update(AuthUser $authUser, KategoriMinat $kategoriMinat): bool
    {
        return $authUser->can('Update:KategoriMinat');
    }

    public function delete(AuthUser $authUser, KategoriMinat $kategoriMinat): bool
    {
        return $authUser->can('Delete:KategoriMinat');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KategoriMinat');
    }

    public function restore(AuthUser $authUser, KategoriMinat $kategoriMinat): bool
    {
        return $authUser->can('Restore:KategoriMinat');
    }

    public function forceDelete(AuthUser $authUser, KategoriMinat $kategoriMinat): bool
    {
        return $authUser->can('ForceDelete:KategoriMinat');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KategoriMinat');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KategoriMinat');
    }

    public function replicate(AuthUser $authUser, KategoriMinat $kategoriMinat): bool
    {
        return $authUser->can('Replicate:KategoriMinat');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KategoriMinat');
    }
}
