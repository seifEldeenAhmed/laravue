<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Exception;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if (!Gate::allows('viewAny', Post::class)) {
                return $this->responseWithForbidden('You do not have permission to view posts');
            }

            $posts = Post::query()->with('author')->visibleTo(auth()->user())
                ->when($request->input('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', '%' . $search . '%')
                            ->orWhere('content', 'like', '%' . $search . '%');
                    });
                })
                ->latest()
                ->paginate($request->input('per_page', 10))
                ->withQueryString();

            return (new PostCollection($posts))
                ->additional([
                    'success' => true,
                    'message' => 'Posts retrieved successfully'
                ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve posts', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_params' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to retrieve posts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        try {
            if (!Gate::allows('create', Post::class)) {
                return $this->responseWithForbidden('You do not have permission to create posts');
            }
            $data = $request->validated();
            $data['author_id'] = auth()->id();
            $data['author_type'] = auth()->user()::class;

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('posts', 'public');
            }
            $post = Post::create($data);
            $post->load('author');

            return $this->responseWithSuccess([
                'post' => new PostResource($post)
            ], 'Post created successfully', 201);
        } catch (Exception $e) {
            Log::error('Failed to create post', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to create post: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        try {
            if (!Gate::allows('view', $post)) {
                return $this->responseWithForbidden('You do not have permission to view this post');
            }
            $response = Cache::remember("posts.{$post->id}",now()->addHour(),function()use($post){
                return new PostResource($post->load('author'));
            });
            return $this->responseWithSuccess([
                'post' => $response
            ], 'Post retrieved successfully');
        } catch (Exception $e) {
            Log::error('Failed to retrieve post', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'post_id' => $post->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to retrieve post: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        try {
            if (!Gate::allows('update', $post)) {
                return $this->responseWithForbidden('You do not have permission to update this post');
            }

            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('posts', 'public');
            }
            $post->update($data);

            // Clear the existing cache entry and set the new one
            Cache::forget("posts.{$post->id}");
            $response = Cache::remember("posts.{$post->id}", now()->addHour(), function() use($post){
                return new PostResource($post->load('author'));
            });

            return $this->responseWithSuccess([
                'post' => $response
            ], 'Post updated successfully');
        } catch (Exception $e) {
            Log::error('Failed to update post', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'post_id' => $post->id ?? null,
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to update post: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            if (!Gate::allows('delete', $post)) {
                return $this->responseWithForbidden('You do not have permission to delete this post');
            }

            $post->delete();
            
            // Clear the cache entry for the deleted post
            Cache::forget("posts.{$post->id}");

            return $this->responseWithSuccess(null, 'Post deleted successfully');
        } catch (Exception $e) {
            Log::error('Failed to delete post', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'post_id' => $post->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to delete post: ' . $e->getMessage(), 500);
        }
    }
}
