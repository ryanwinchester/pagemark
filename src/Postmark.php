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
     * @param Parseable $parser
     * @return static
     */
    public static function create(Parseable $parser = null)
    {
        $parser = $parser ?: new Parser(new \Parsedown());

        return new static(new Filesystem(), $parser);
    }

    /**
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
            $index['subcategories'] = $this->filesystem->directories($postPath);
            $index['files'] = $this->filesystem->files($postPath);
            $postPath .= '/index';
        }

        $file = $postPath.'.md';
        if ($this->filesystem->exists($file)) {
            $post = $this->parser->parse($this->filesystem->get($file));
        } else {
            $post = '';
        }

        if ($isDir) {
            foreach ($index['subcategories'] as $i => $item) {
                $item = str_replace($basePath, '', $item);
                $paths = explode('/', $item);
                $index['subcategories'][$i] = [
                    'href' => $item,
                    'name' => array_pop($paths),
                ];
            }
            foreach ($index['files'] as $i => $item) {
                $item = str_replace($basePath, '', $item);
                $item = str_replace('.md', '', $item);
                $paths = explode('/', $item);
                $index['files'][$i] = [
                    'href' => $item,
                    'name' => array_pop($paths),
                ];
                if ($index['files'][$i]['name'] === 'index') {
                    unset($index['files'][$i]);
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
