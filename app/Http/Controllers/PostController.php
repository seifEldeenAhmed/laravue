<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (!Gate::allows('viewAny', Post::class)) {
                return $this->responseWithForbidden('You do not have permission to view posts');
            }

            $posts = Post::with('author')->visibleTo(auth()->user())->paginate(1);

            return (new PostCollection($posts))
                ->additional([
                    'success' => true,
                    'message' => 'Posts retrieved successfully'
                ]);
        } catch (Exception $e) {
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

            $post = Post::create($data);
            $post->load('author');

            return $this->responseWithSuccess([
                'post' => new PostResource($post)
            ], 'Post created successfully', 201);
        } catch (Exception $e) {
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

            $post->load('author');

            return $this->responseWithSuccess([
                'post' => new PostResource($post)
            ], 'Post retrieved successfully');
        } catch (Exception $e) {
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
            // Don't allow updating author information - security vulnerability
            $post->update($data);

            $post->load('author');

            return $this->responseWithSuccess([
                'post' => new PostResource($post)
            ], 'Post updated successfully');
        } catch (Exception $e) {
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

            return $this->responseWithSuccess(null, 'Post deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError('Failed to delete post: ' . $e->getMessage(), 500);
        }
    }
}
