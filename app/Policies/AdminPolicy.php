<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User|Admin $user): bool
    {
        // Only admins can view admin lists
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User|Admin $user, Admin $admin): bool
    {
        // Only admins can view admin details
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User|Admin $user): bool
    {
        // Only admins can create other admins
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Admin $user, Admin $admin): bool
    {
        // Only admins can update admin details
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Admin $user, Admin $admin): bool
    {
        // Admins can delete other admins, but not themselves
        return $user instanceof Admin && $user->id !== $admin->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Admin $admin): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Admin $admin): bool
    {
        return false;
    }
}
