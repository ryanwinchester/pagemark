# Postmark

[![Version](https://img.shields.io/packagist/v/fungku/postmark.svg?style=flat-square)](https://packagist.org/packages/fungku/postmark)
 [![Total Downloads](https://img.shields.io/packagist/dt/fungku/postmark.svg?style=flat-square)](https://packagist.org/packages/fungku/postmark)
 [![License](https://img.shields.io/packagist/l/fungku/postmark.svg?style=flat-square)](https://packagist.org/packages/fungku/postmark)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/fungku/postmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/fungku/postmark/?branch=master)
 [![Build Status](https://img.shields.io/travis/fungku/postmark.svg?style=flat-square)](https://travis-ci.org/fungku/postmark)


## Parse markdown for blogs and wikis

There are multiple ways to get the content.

```php
use Fungku\Postmark\Postmark;

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = Postmark::parse($basePath, $post);
```

```php
use Fungku\Postmark\Postmark;

$postmark = Postmark::create();

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```

```php
use Fungku\Postmark\Postmark;
use Fungku\Postmark\Parser;
use Illuminate\Filesystem\Filesystem;
use Parsedown;

$postmark = new Postmark(new Filesystem, new Parser(new Parsedown));

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```

## Example return value

```php
$content = [
    'breadcrumbs' => [
        [
            'href' => 'Category',
            'name' => 'Category'
        ],
        [
            'href' => 'Category/Subcategory',
            'name' => 'Subcategory'
        ],
        [
            'href' => 'Category/Subcategory/My-Post',
            'name' => 'My Post'
        ],
    ],
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
use Fungku\Postmark\Postmark;

$myCustomParser = new CustomParser;

$postmark = Postmark::create($myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```

```php
use Fungku\Postmark\Postmark;
use Fungku\Postmark\Parser;
use Illuminate\Filesystem\Filesystem;

$myCustomParser = new CustomParser;

$postmark = new Postmark(new Filesystem, $myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark->getContent($basePath, $post);
```
