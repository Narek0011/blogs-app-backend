<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Http\Resources\BlogResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BlogController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the blogs.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $blogs = Blog::with('user')->orderBy('created_at', 'desc')->paginate(4);
            return response()->json([
                'blogs' => BlogResource::collection($blogs),
                'links' => [
                    'first' => $blogs->url(1),
                    'last' => $blogs->url($blogs->lastPage()),
                    'prev' => $blogs->previousPageUrl(),
                    'next' => $blogs->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $blogs->currentPage(),
                    'last_page' => $blogs->lastPage(),
                    'per_page' => $blogs->perPage(),
                    'total' => $blogs->total(),
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(StoreBlogRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $imagePath = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imagePath = $request->file('image')->store('blogs', 'public');
            }

            $blog = Blog::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'image' => $imagePath,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'blog' => new BlogResource($blog)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified blog.
     */
    public function show(Blog $blog): \Illuminate\Http\JsonResponse|BlogResource
    {
        try {
            $blog->load('user');
            return response()->json([
                'blog' => new BlogResource($blog)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): \Illuminate\Http\JsonResponse
    {
        try {
            $this->authorize('update', $blog);
            $validated = $request->validated();
            $imagePath = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imagePath = $request->file('image')->store('blogs', 'public');
            }
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
            $blog->update($validated);
            $blog->save();

            return response()->json([
                'blog' => new BlogResource($blog)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy(Blog $blog): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        try {
            $this->authorize('delete', $blog);
            $blog->delete();

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
