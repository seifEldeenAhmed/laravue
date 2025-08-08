<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $published_posts = Post::where('status', 'published')->count();
        $draft_posts = Post::where('status', 'draft')->count();

        return [
            'top_users' => User::withCount('posts')
                ->orderBy('posts_count', 'desc')->take(5)->get(),
            'published_posts' => $published_posts,
            'draft_posts' => $draft_posts,
            'total_posts' => $published_posts + $draft_posts,
            'total_users' => User::count(),
        ];
    }
}

