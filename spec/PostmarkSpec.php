<?php

namespace spec\Fungku\Postmark;

use Fungku\Postmark\Parser;
use Illuminate\Filesystem\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostmarkSpec extends ObjectBehavior
{
    private $basePath = '/path/to/wiki';
    private $post = 'My/Post/File';

    private $postPath;
    private $markdown;
    private $html;

    function __construct()
    {
        $this->postPath = $this->basePath .'/'. $this->post;
        $this->markdown = require "resources/markdown.php";
        $this->html = require "resources/html.php";
    }

    function let(Filesystem $filesystem, Parser $parser)
    {

        $this->beConstructedWith($filesystem, $parser);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Fungku\Postmark\Postmark');
    }

    function it_gets_content_for_file(Filesystem $filesystem, Parser $parser)
    {
        $filePath = $this->postPath .'.md';
        
        $content = [
            'title' => 'File',
            'breadcrumbs' => [
                ['href' => '/My',           'name' => 'My'],
                ['href' => '/My/Post',      'name' => 'Post'],
                ['href' => '/My/Post/File', 'name' => 'File']
            ],
            'index' => [],
            'post'  => $this->html,
        ];

        $filesystem->isDirectory($this->postPath)
            ->willReturn(false);
        $filesystem->exists($filePath)
            ->willReturn(true);
        $filesystem->get($filePath)
            ->willReturn($this->markdown);

        $parser->parse($this->markdown)
            ->willReturn($content['post']);

        $this->getContent($this->basePath, $this->post)
            ->shouldReturn($content);
    }

    function it_gets_content_for_directory_with_index_only(Filesystem $filesystem, Parser $parser)
    {
        $filePath = $this->postPath .'/index.md';

        $content = [
            'title' => 'File',
            'breadcrumbs' => [
                ['href' => '/My',           'name' => 'My'],
                ['href' => '/My/Post',      'name' => 'Post'],
                ['href' => '/My/Post/File', 'name' => 'File'],
            ],
            'index' => ['subcategories' => [], 'files' => []],
            'post'  => $this->html,
        ];

        $filesystem->isDirectory($this->postPath)
            ->willReturn(true);
        $filesystem->exists($filePath)
            ->willReturn(true);
        $filesystem->get($filePath)
            ->willReturn($this->markdown);
        $filesystem->directories($this->postPath)
            ->willReturn([]);
        $filesystem->files($this->postPath)
            ->willReturn([]);

        $parser->parse($this->markdown)
            ->willReturn($content['post']);

        $this->getContent($this->basePath, $this->post)
            ->shouldReturn($content);
    }

    function it_gets_content_for_directory_with_subcategories_and_files(Filesystem $filesystem, Parser $parser)
    {
        $filePath = $this->postPath .'/index.md';

        $content = [
            'title' => 'File',
            'breadcrumbs' => [
                ['href' => '/My',           'name' => 'My'],
                ['href' => '/My/Post',      'name' => 'Post'],
                ['href' => '/My/Post/File', 'name' => 'File'],
            ],
            'index' => [
                'subcategories' => [
                    ['href' => '/Some-Subcategory', 'name' => 'Some Subcategory'],
                ],
                'files' => [
                    ['href' => '/Another-Post', 'name' => 'Another Post'],
                ],
            ],
            'post' => $this->html,
        ];

        $filesystem->isDirectory($this->postPath)
            ->willReturn(true);
        $filesystem->exists($filePath)
            ->willReturn(true);
        $filesystem->get($filePath)
            ->willReturn($this->markdown);
        $filesystem->directories($this->postPath)
            ->willReturn(['/Some-Subcategory']);
        $filesystem->files($this->postPath)
            ->willReturn(['Another-Post.md']);

        $parser->parse($this->markdown)
            ->willReturn($content['post']);

        $this->getContent($this->basePath, $this->post)
            ->shouldReturn($content);
    }
}
