<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User|Admin $user): bool
    {
        return true; // All authenticated users can view the posts index
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User|Admin $user, Post $post): bool
    {
        // Admins can view all posts
        if ($user instanceof Admin) {
            return true;
        }

        // Users can only view their own posts
        return $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User|Admin $user): bool
    {
        return true; // All authenticated users can create posts
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Admin $user, Post $post): bool
    {
        // Admins can update all posts
        if ($user instanceof Admin) {
            return true;
        }

        // Users can only update their own posts
        return $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Admin $user, Post $post): bool
    {
        // Admins can delete all posts
        if ($user instanceof Admin) {
            return true;
        }

        // Users can only delete their own posts
        return $post->author_id === $user->id;
    }
}
