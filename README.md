# Pagemark

[![Version](https://img.shields.io/packagist/v/fungku/postmark.svg?style=flat-square)](https://packagist.org/packages/fungku/postmark)
 [![Total Downloads](https://img.shields.io/packagist/dt/fungku/postmark.svg?style=flat-square)](https://packagist.org/packages/fungku/postmark)
 [![License](https://img.shields.io/packagist/l/fungku/postmark.svg?style=flat-square)](https://packagist.org/packages/fungku/postmark)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/fungku/postmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/fungku/postmark/?branch=master)
 [![Build Status](https://img.shields.io/travis/fungku/postmark.svg?style=flat-square)](https://travis-ci.org/fungku/postmark)


## Parse markdown pages for blogs and wikis

There are multiple ways to get the content.

```php
use Pagemark\Pagemark;

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = Pagemark::parse($basePath, $post);
```

```php
use Pagemark\Pagemark;

$pagemark = Pagemark::create();

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $pagemark->getContent($basePath, $post);
```

```php
use Pagemark\Pagemark;
use Pagemark\Parser;
use Illuminate\Filesystem\Filesystem;
use Parsedown;

$pagemark = new Pagemark(new Filesystem, new Parser(new Parsedown));

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $pagemark->getContent($basePath, $post);
```

## Example return value

```php
$content = [
    'title' => 'File',
    'breadcrumbs' => [
        [
            'href' => '/Category',
            'name' => 'Category'
        ],
        [
            'href' => '/Category/Subcategory',
            'name' => 'Subcategory'
        ],
        [
            'href' => '/Category/Subcategory/My-Post',
            'name' => 'My Post'
        ],
    ],
    'index'       => ['subcategories' => [], 'files' => []],
    'post'        => '<p>Some text from My-Post.md</p>'
];
```
Explanation:

1. `$title` - The title of the post or category taken from the file or directory name.
2. `$breadcrumbs` is an array of breadcrumbs.
3. `$index` is available if you have navigated to a directory, or an empty array otherwise
    - `$index['subcategories']` is an array of subdirectories in your current directory
    - `$index['files']` is an array of files in your current directory
4. `$post` is a string of your parsed markdown content

## Using a different parser.

By default the markdown parser used is [erusev/parsedown](https://github.com/erusev/parsedown). To use a different one, 
you need to make your own parser that implements the `Parseable` interface or create an adapter for a different library
that implements `Parseable`.

```php
use Pagemark\Pagemark;

$myCustomParser = new CustomParser;

$pagemark = Pagemark::create($myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $pagemark->getContent($basePath, $post);
```

```php
use Pagemark\Pagemark;
use Pagemark\Parser;
use Illuminate\Filesystem\Filesystem;

$myCustomParser = new CustomParser;

$pagemark = new Pagemark(new Filesystem, $myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $pagemark->getContent($basePath, $post);
```
