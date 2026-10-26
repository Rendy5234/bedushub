<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Institusi;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class InstitusiPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Institusi');
    }

    public function view(AuthUser $authUser, Institusi $institusi): bool
    {
        return $authUser->can('View:Institusi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Institusi');
    }

    public function update(AuthUser $authUser, Institusi $institusi): bool
    {
        return $authUser->can('Update:Institusi');
    }

    public function delete(AuthUser $authUser, Institusi $institusi): bool
    {
        return $authUser->can('Delete:Institusi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Institusi');
    }

    public function restore(AuthUser $authUser, Institusi $institusi): bool
    {
        return $authUser->can('Restore:Institusi');
    }

    public function forceDelete(AuthUser $authUser, Institusi $institusi): bool
    {
        return $authUser->can('ForceDelete:Institusi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Institusi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Institusi');
    }

    public function replicate(AuthUser $authUser, Institusi $institusi): bool
    {
        return $authUser->can('Replicate:Institusi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Institusi');
    }
}
