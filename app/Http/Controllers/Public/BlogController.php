<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\MarkdownService;
use Illuminate\Contracts\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::published()->paginate(10);

        return view('public.blog.index', compact('posts'));
    }

    public function show(string $slug, MarkdownService $markdown): View
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $post->content_html = $markdown->toHtml($post->content);

        return view('public.blog.show', compact('post'));
    }
}
