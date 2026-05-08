<?php

namespace Tests\Unit;

use App\Services\MarkdownService;
use Tests\TestCase;

class MarkdownServiceTest extends TestCase
{
    private MarkdownService $markdown;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markdown = new MarkdownService();
    }

    public function test_converts_basic_markdown_to_html(): void
    {
        $html = $this->markdown->toHtml('# Title');

        $this->assertStringContainsString('<h1>Title</h1>', $html);
    }

    public function test_converts_bold_and_italic(): void
    {
        $html = $this->markdown->toHtml('This is **bold** and *italic*.');

        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringContainsString('<em>italic</em>', $html);
    }

    public function test_escapes_raw_html_input(): void
    {
        $html = $this->markdown->toHtml('Hello <script>alert(1)</script> world');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_renders_table_extension(): void
    {
        $markdown = "| Col1 | Col2 |\n| ---- | ---- |\n| A    | B    |";

        $html = $this->markdown->toHtml($markdown);

        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('<th>Col1</th>', $html);
        $this->assertStringContainsString('<td>A</td>', $html);
    }

    public function test_renders_fenced_code_block(): void
    {
        $markdown = "```php\necho 'hi';\n```";

        $html = $this->markdown->toHtml($markdown);

        $this->assertStringContainsString('<pre>', $html);
        $this->assertStringContainsString('<code class="language-php">', $html);
    }

    public function test_creates_autolink_from_url(): void
    {
        $html = $this->markdown->toHtml('Visita https://laravel.com para mas info');

        $this->assertStringContainsString('<a href="https://laravel.com">https://laravel.com</a>', $html);
    }
}
