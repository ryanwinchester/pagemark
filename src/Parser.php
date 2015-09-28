<?php

namespace Pagemark;

use Pagemark\Contracts\Parseable;
use Parsedown;

class Parser implements Parseable
{
    /**
     * @var Parsedown
     */
    private $parser;

    /**
     * @param Parsedown $parser
     */
    public function __construct(Parsedown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param string $post
     * @return string
     */
    public function parse($post)
    {
        return $this->parser->text($post);
    }
}