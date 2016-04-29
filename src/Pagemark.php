<?php

namespace Pagemark;

use Illuminate\Filesystem\Filesystem;
use Pagemark\Contracts\Parseable;

class Pagemark
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
     * Return a new pagemark instance.
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
        $pagemark = static::create();

        return $pagemark->getContent($basePath, $post);
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
        $title = $this->makeTitleFromBreadcrumbs($breadcrumbs);

        $postPath = $basePath .($post? '/'. $post : '');
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
                $name = array_pop($paths);
                $index['subcategories'][$i] = [
                    'href' => $item,
                    'name' => $this->deslugify($name),
                ];
            }
            foreach ($index['files'] as $i => $item) {
                $item = str_replace($basePath, '', $item);
                $item = str_replace('.md', '', $item);
                $paths = explode('/', $item);
                $name = array_pop($paths);
                $index['files'][$i] = [
                    'href' => $item,
                    'name' => $this->deslugify($name),
                ];
                if ($index['files'][$i]['name'] === 'index') {
                    unset($index['files'][$i]);
                }
            }
        }

        return [
            'title'       => $title,
            'breadcrumbs' => $breadcrumbs,
            'index'       => $index,
            'post'        => $post,
        ];
    }

    /**
     * Get the Post or Category title from the breadcrumbs array.
     *
     * @param $breadcrumbs
     * @return mixed
     */
    private function makeTitleFromBreadcrumbs($breadcrumbs)
    {
        $lastCrumb = current(array_slice($breadcrumbs, -1));

        return $lastCrumb['name'];
    }

    /**
     * Make an array of breadcrumb items.
     *
     * @param string $post
     * @return array
     */
    private function makeBreadcrumbsFromPost($post)
    {
        $crumbs = explode('/', $post);
        $crumbs = empty($crumbs[0]) ? [] : $crumbs;

        $breadcrumbs = [];

        foreach ($crumbs as $i => $crumb) {
            $breadcrumbs[$i] = [
                'href' => '/'.$this->slugify(implode('/', array_slice($crumbs, 0, $i+1))),
                'name' => $this->deslugify($crumbs[$i]),
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Take a string with spaces and convert the spaces to the specified separator.
     *
     * @param string $string
     * @param string $separator
     * @return mixed
     */
    private function slugify($string, $separator = '-')
    {
        return str_replace(' ', $separator, $string);
    }

    /**
     * Take a string with dash or underscore word separators and convert the to spaces.
     *
     * @param array|string $slugged
     * @return array|string
     */
    private function deslugify($slugged)
    {
        if (is_array($slugged)) {
            return array_map('static::deslugify', $slugged);
        }

        return str_replace(['-', '_'], ' ', $slugged);
    }
}
