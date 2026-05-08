<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $user = User::query()->withCv()->first();
        $posts = Post::query()->latestPublished(3)->get();

        return view('public.home', compact('user', 'posts'));
    }
}
