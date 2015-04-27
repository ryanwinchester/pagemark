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

        $this->setupFilesystemMethods($filesystem, $filePath);

        $parser->parse($this->markdown)
            ->willReturn($this->html);

        $this->getContent($this->basePath, $this->post)
             ->shouldReturn([
                'title' => 'File',
                'breadcrumbs' => [
                    ['href' => '/My',           'name' => 'My'],
                    ['href' => '/My/Post',      'name' => 'Post'],
                    ['href' => '/My/Post/File', 'name' => 'File']
                ],
                'index' => [],
                'post'  => $this->html,
            ]);
    }

    function it_gets_content_for_directory_with_index_only(Filesystem $filesystem, Parser $parser)
    {
        $filePath = $this->postPath .'/index.md';

        $this->setupFilesystemMethods($filesystem, $filePath, true);

        $parser->parse($this->markdown)
            ->willReturn($this->html);

        $this->getContent($this->basePath, $this->post)
             ->shouldReturn([
                'title' => 'File',
                'breadcrumbs' => [
                    ['href' => '/My',           'name' => 'My'],
                    ['href' => '/My/Post',      'name' => 'Post'],
                    ['href' => '/My/Post/File', 'name' => 'File'],
                ],
                'index' => ['subcategories' => [], 'files' => []],
                'post'  => $this->html,
            ]);
    }

    function it_gets_content_for_directory_with_subcategories_and_files(Filesystem $filesystem, Parser $parser)
    {
        $filePath = $this->postPath .'/index.md';

        $this->setupFilesystemMethods(
            $filesystem,
            $filePath,
            true, // is directory
            [$this->postPath.'/Some-Subcategory'], // subdirectory list
            [$this->postPath.'/Another-Post.md', $this->postPath . '/Test-Post.md'] // file list
        );

        $parser->parse($this->markdown)
            ->willReturn($this->html);

        $this->getContent($this->basePath, $this->post)
             ->shouldReturn([
                'title' => 'File',
                'breadcrumbs' => [
                    ['href' => '/My', 'name' => 'My'],
                    ['href' => '/My/Post', 'name' => 'Post'],
                    ['href' => '/My/Post/File', 'name' => 'File'],
                ],
                'index' => [
                    'subcategories' => [
                        ['href' => '/My/Post/File/Some-Subcategory', 'name' => 'Some Subcategory'],
                    ],
                    'files' => [
                        ['href' => '/My/Post/File/Another-Post', 'name' => 'Another Post'],
                        ['href' => '/My/Post/File/Test-Post', 'name' => 'Test Post'],
                    ],
                ],
                'post' => $this->html,
            ]);
    }

    /**
     * Set up the filesystem doubles.
     *
     * @param Filesystem $filesystem
     * @param string $filePath
     * @param bool $isDir
     * @param array $returnDirs
     * @param array $returnFiles
     */
    private function setupFilesystemMethods($filesystem, $filePath, $isDir = false, $returnDirs= [], $returnFiles = [])
    {
        $filesystem->isDirectory($this->postPath)
            ->willReturn($isDir);
        $filesystem->exists($filePath)
            ->willReturn(true);
        $filesystem->get($filePath)
            ->willReturn($this->markdown);
        $filesystem->directories($this->postPath)
            ->willReturn($returnDirs);
        $filesystem->files($this->postPath)
            ->willReturn($returnFiles);
    }
}
