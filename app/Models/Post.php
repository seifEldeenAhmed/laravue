<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'author_type',
        'status',
        'image'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => \App\Enums\PostStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    #[Scope]
    protected function visibleTo(Builder $query, $user): Builder
    {
        if ($user instanceof \App\Models\Admin) {
            return $query; // Admins can see all posts
        }
        return $query->where('author_id', $user->id); // Users see only their own posts
    }

    /**
     * Get the author of the post.
     */

    public function author(): MorphTo
    {
        return $this->morphTo();
    }
}
