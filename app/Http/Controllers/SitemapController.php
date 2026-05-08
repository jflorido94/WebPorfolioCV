<?php

namespace App\Http\Controllers;

use App\Models\Post;
use DateTimeInterface;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()->select('slug', 'published_at')->get();

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        $sitemap .= $this->addUrl('/', now());
        $sitemap .= $this->addUrl('/cv', now());
        $sitemap .= $this->addUrl('/blog', now());

        foreach ($posts as $post) {
            $sitemap .= $this->addUrl("/blog/{$post->slug}", $post->published_at);
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function addUrl(string $path, ?DateTimeInterface $lastmod = null): string
    {
        $url = config('app.url') . $path;
        $lastmodDate = ($lastmod ?? now())->format(DateTimeInterface::ATOM);

        return "\t<url>\n\t\t<loc>{$url}</loc>\n\t\t<lastmod>{$lastmodDate}</lastmod>\n\t</url>\n";
    }
}
