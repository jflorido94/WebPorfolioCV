<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesOwnership;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    use AuthorizesOwnership;

    public function index(): View
    {
        $posts = Auth::user()->posts()->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $post = Auth::user()->posts()->create($this->postPayload($request));
            $this->syncTags($post, (string) $request->input('tags', ''));
        });

        return redirect()->route('admin.posts.index')->with('success', 'Post creado exitosamente');
    }

    public function edit(Post $post): View
    {
        $this->authorizeOwnership($post);

        return view('admin.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorizeOwnership($post);

        DB::transaction(function () use ($request, $post): void {
            $post->update($this->postPayload($request, $post));
            $this->syncTags($post, (string) $request->input('tags', ''));
        });

        return redirect()->route('admin.posts.index')->with('success', 'Post actualizado exitosamente');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorizeOwnership($post);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post eliminado exitosamente');
    }

    /**
     * @return array<string, mixed>
     */
    private function postPayload(FormRequest $request, ?Post $post = null): array
    {
        $publish = $request->boolean('publish', $post?->published ?? false);

        return [
            'title' => $request->input('title'),
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'category' => $request->input('category'),
            'published' => $publish,
            'published_at' => $publish ? ($post?->published_at ?? now()) : null,
        ];
    }

    private function syncTags(Post $post, string $tags): void
    {
        $post->tags()->delete();

        if ($tags === '') {
            return;
        }

        $tagArray = array_filter(array_map('trim', explode(',', $tags)));

        foreach ($tagArray as $tag) {
            $post->tags()->create(['tag' => $tag]);
        }
    }
}
