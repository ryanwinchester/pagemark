<?php

namespace Pagemark;

class Page
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $breadcrumbs;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $post;

    /**
     * @param string $title
     * @param \Illuminate\Support\Collection $breadcrumbs
     * @param string $index
     * @param string $post
     */
    public function __construct($title, $breadcrumbs, $index, $post = null)
    {
        $this->title = $title;
        $this->breadcrumbs = $breadcrumbs;
        $this->index = $index;
        $this->post = $post;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (! property_exists(static, $name) {
            return null;
        }

        return $this->$name;
    }
}