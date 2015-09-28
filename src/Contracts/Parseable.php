<?php

namespace Pagemark\Contracts;

interface Parseable
{
    /**
     * @param string $post The markdown text to parse.
     * @return string
     */
    public function parse($post);
}