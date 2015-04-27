<?php

namespace spec\Fungku\Postmark;

use Fungku\Postmark\Parser;
use Illuminate\Filesystem\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PostmarkSpec extends ObjectBehavior
{
    private $input;

    function __construct()
    {
        $markdown = require "resources/markdown.php";

        $this->input = [
            'basePath'  => 'path/to/wiki',
            'dirPath'   => 'path/to/wiki/My/Post/File',
            'indexPath' => 'path/to/wiki/My/Post/File/index.md',
            'filePath'  => 'path/to/wiki/My/Post/File.md',
            'post'      => 'My/Post/File',
            'file'      => 'My/Post/File.md',
            'markdown'  => $markdown,
        ];
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
        $content = [
            'title' => 'File',
            'breadcrumbs' => [
                ['href' => '/My',           'name' => 'My'],
                ['href' => '/My/Post',      'name' => 'Post'],
                ['href' => '/My/Post/File', 'name' => 'File']
            ],
            'index'       => [],
            'post'        => require "resources/html.php",
        ];

        $filesystem->isDirectory($this->input['dirPath'])
            ->willReturn(false);
        $filesystem->exists($this->input['filePath'])
            ->willReturn(true);
        $filesystem->get($this->input['filePath'])
            ->willReturn($this->input['markdown']);

        $parser->parse($this->input['markdown'])
            ->willReturn($content['post']);

        $this->getContent($this->input['basePath'], $this->input['post'])
            ->shouldReturn($content);
    }

    function it_gets_content_for_directory_with_index_only(Filesystem $filesystem, Parser $parser)
    {
        $content = [
            'title' => 'File',
            'breadcrumbs' => [
                ['href' => '/My',           'name' => 'My'],
                ['href' => '/My/Post',      'name' => 'Post'],
                ['href' => '/My/Post/File', 'name' => 'File'],
            ],
            'index'       => ['subcategories' => [], 'files' => []],
            'post'        => require "resources/html.php",
        ];

        $filesystem->isDirectory($this->input['dirPath'])
            ->willReturn(true);
        $filesystem->exists($this->input['indexPath'])
            ->willReturn(true);
        $filesystem->get($this->input['indexPath'])
            ->willReturn($this->input['markdown']);
        $filesystem->directories($this->input['dirPath'])
            ->willReturn([]);
        $filesystem->files($this->input['dirPath'])
            ->willReturn([]);

        $parser->parse($this->input['markdown'])
            ->willReturn($content['post']);

        $this->getContent($this->input['basePath'], $this->input['post'])
            ->shouldReturn($content);
    }

    function it_gets_content_for_directory_with_subcategories_and_files(Filesystem $filesystem, Parser $parser)
    {
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
            'post' => require "resources/html.php",
        ];

        $filesystem->isDirectory($this->input['dirPath'])
            ->willReturn(true);
        $filesystem->exists($this->input['indexPath'])
            ->willReturn(true);
        $filesystem->get($this->input['indexPath'])
            ->willReturn($this->input['markdown']);
        $filesystem->directories($this->input['dirPath'])
            ->willReturn(['Some-Subcategory']);
        $filesystem->files($this->input['dirPath'])
            ->willReturn(['Another-Post.md']);

        $parser->parse($this->input['markdown'])
            ->willReturn($content['post']);

        $this->getContent($this->input['basePath'], $this->input['post'])
            ->shouldReturn($content);
    }
}
