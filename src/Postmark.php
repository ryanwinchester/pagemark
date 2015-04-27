<?php namespace Fungku\Postmark;

use Fungku\Postmark\Contracts\Parseable;
use Illuminate\Filesystem\Filesystem;

class Postmark
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var Parseable
     */
    private $parser;

    /**
     * TODO: Not liking the filesystem dependency, so look at other options
     *
     * @param Filesystem $filesystem
     * @param Parseable $parser
     */
    public function __construct(Filesystem $filesystem, Parseable $parser)
    {
        $this->filesystem = $filesystem;
        $this->parser = $parser;
    }

    /**
     * Return a new postmark instance.
     *
     * @param Parseable $parser
     * @return static
     */
    public static function create(Parseable $parser = null)
    {
        $parser = $parser ?: new Parser(new \Parsedown());

        return new static(new Filesystem(), $parser);
    }

    /**
     * Parse a post into an array of content elements
     * without needing to instantiate the class first.
     *
     * @param string $basePath
     * @param string $post
     * @return mixed
     */
    public static function parse($basePath, $post)
    {
        $postmark = static::create();

        return $postmark->getContent($basePath, $post);
    }

    /**
     * Parse a post into an array of content elements.
     *
     * TODO: Refactor
     *
     * @param string $basePath
     * @param string $post
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getContent($basePath, $post)
    {
        $breadcrumbs = $this->makeBreadcrumbsFromPost($post);

        $postPath = $basePath .'/'. $post;
        $isDir = false;
        $index = [];

        if ($this->filesystem->isDirectory($postPath)) {
            $isDir = true;
            $subcategories = $this->filesystem->directories($postPath);
            $files = $this->filesystem->files($postPath);
            $postPath .= '/index';
        }

        $file = $postPath.'.md';
        if ($this->filesystem->exists($file)) {
            $post = $this->parser->parse($this->filesystem->get($file));
        } else {
            $post = '';
        }

        if ($isDir) {
            foreach ($subcategories as $i => $item) {
                $item = str_replace($basePath, '', $item);
                $paths = explode('/', $item);
                $name = array_pop($paths);
                $index['subcategories'][$i] = [
                    'href' => $item,
                    'name' => $this->deslug($name),
                ];
            }
            foreach ($files as $i => $item) {
                $item = str_replace($basePath, '', $item);
                $item = str_replace('.md', '', $item);
                $paths = explode('/', $item);
                $name = array_pop($paths);
                $index['files'][$i] = [
                    'href' => $item,
                    'name' => $this->deslug($name),
                ];
                if ($index['files'][$i]['name'] === 'index') {
                    unset($files[$i]);
                }
            }
        }

        return [
            'breadcrumbs' => $breadcrumbs,
            'index'       => $index,
            'post'        => $post,
        ];
    }

    /**
     * Make an array of breadcrumb items.
     *
     * @param string $post
     * @return array
     */
    private function makeBreadcrumbsFromPost($post)
    {
        $breadcrumbs = explode('/', $post);
        $breadcrumbs = empty($breadcrumbs[0]) ? array() : $breadcrumbs;

        return $this->deslug($breadcrumbs);
    }

    /**
     * Take a string with dash or underscore word separators and convert the to spaces.
     *
     * @param array|string $slugged
     * @return array|string
     */
    private function deslug($slugged)
    {
        if (is_array($slugged)) {
            return array_map('static::deslug', $slugged);
        }

        return str_replace(['-', '_'], ' ', $slugged);
    }
}
