<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProgramStudi;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProgramStudiPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProgramStudi');
    }

    public function view(AuthUser $authUser, ProgramStudi $programStudi): bool
    {
        return $authUser->can('View:ProgramStudi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProgramStudi');
    }

    public function update(AuthUser $authUser, ProgramStudi $programStudi): bool
    {
        return $authUser->can('Update:ProgramStudi');
    }

    public function delete(AuthUser $authUser, ProgramStudi $programStudi): bool
    {
        return $authUser->can('Delete:ProgramStudi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProgramStudi');
    }

    public function restore(AuthUser $authUser, ProgramStudi $programStudi): bool
    {
        return $authUser->can('Restore:ProgramStudi');
    }

    public function forceDelete(AuthUser $authUser, ProgramStudi $programStudi): bool
    {
        return $authUser->can('ForceDelete:ProgramStudi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProgramStudi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProgramStudi');
    }

    public function replicate(AuthUser $authUser, ProgramStudi $programStudi): bool
    {
        return $authUser->can('Replicate:ProgramStudi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProgramStudi');
    }
}
