<?php namespace Fungku\Postmark;

use Illuminate\Filesystem\Filesystem;
use Parsedown;

class Parser
{
    private $filesystem;
    private $parser;

    public function __construct(Filesystem $filesystem, Parsedown $parser)
    {
        $this->filesystem = $filesystem;
        $this->parser = $parser;
    }

    public function parse($wikiPath, $post)
    {
        $postPath = $wikiPath .'/'. $post;
        $isDir = false;
        $index = [];

        if ($this->filesystem->isDirectory($postPath)) {
            $isDir = true;
            $index['subcategories'] = $this->filesystem->directories($postPath);
            $index['files'] = $this->filesystem->files($postPath);
            $postPath .= '/index';
        }

        $file = $postPath .'.md';
        if ($this->filesystem->exists($file)) {
            $post = $this->parser->text($this->filesystem->get($file));
        } else {
            $post = '';
        }

        if ($isDir) {
            foreach ($index['subcategories'] as $i => $item) {
                $item = str_replace($wikiPath, '', $item);
                $paths = explode('/', $item);
                $index['subcategories'][$i] = [
                    'href' => $item,
                    'name' => array_pop($paths),
                ];
            }
            foreach ($index['files'] as $i => $item) {
                $item = str_replace($wikiPath, '', $item);
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
            'index' => $index,
            'post'  => $post,
        ];
    }
}
