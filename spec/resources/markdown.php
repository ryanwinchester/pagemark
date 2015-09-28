<?php

return <<<'MARKDOWN'

## Parse markdown for blogs and wikis

There are multiple ways to get the content.

```php
use Pagemark\Pagemark;

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = Pagemark::parse($basePath, $post);
```

```php
use Pagemark\Pagemark;

$postmark = Pagemark::create();

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```

```php
use Pagemark\Pagemark;
use Pagemark\Parser;
use Illuminate\Filesystem\Filesystem;
use Parsedown;

$postmark = new Pagemark(new Filesystem, new Parser(new Parsedown));

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```

## Example return value

```php
$content = [
    'breadcrumbs' => ['Category', 'Subcategory', 'My-Post'],
    'index'       => ['subcategories' => [], 'files' => []],
    'post'        => '<p>Some text from My-Post.md</p>'
];
```
Explanation:

1. `$breadcrumbs` is an array of breadcrumbs.
2. `$index` is available if you have navigated to a directory, or an empty array otherwise
    - `$index['subcategories']` is an array of subdirectories in your current directory
    - `$index['files']` is an array of files in your current directory
3. `$post` is a string of your parsed markdown content

## Using a different parser.

By default the markdown parser used is [erusev/parsedown](https://github.com/erusev/parsedown). To use a different one, 
you need to make your own parser that implements the `Parseable` interface or create an adapter for a different library
that implements `Parseable`.

```php
use Pagemark\Pagemark;

$myCustomParser = new CustomParser;

$postmark = Pagemark::create($myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```

```php
use Pagemark\Pagemark;
use Pagemark\Parser;
use Illuminate\Filesystem\Filesystem;

$myCustomParser = new CustomParser;

$postmark = new Pagemark(new Filesystem, $myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```

MARKDOWN;
