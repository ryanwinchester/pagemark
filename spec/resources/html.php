<?php

return <<<'HTML'
<h2>Parse markdown for blogs and wikis</h2>
<p>There are multiple ways to get the content.</p>
<pre><code class="language-php">use Pagemark\Pagemark;

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = Pagemark::parse($basePath, $post);</code></pre>
<pre><code class="language-php">use Pagemark\Pagemark;

$postmark = Pagemark::create();

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark-&gt;getContent($basePath, $post);</code></pre>
<pre><code class="language-php">use Pagemark\Pagemark;
use Pagemark\Parser;
use Illuminate\Filesystem\Filesystem;
use Parsedown;

$postmark = new Pagemark(new Filesystem, new Parser(new Parsedown));

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark-&gt;getContent($basePath, $post);</code></pre>
<h2>Example return value</h2>
<pre><code class="language-php">$content = [
    'breadcrumbs' =&gt; ['Category', 'Subcategory', 'My-Post'],
    'index'       =&gt; ['subcategories' =&gt; [], 'files' =&gt; []],
    'post'        =&gt; '&lt;p&gt;Some text from My-Post.md&lt;/p&gt;'
];</code></pre>
<p>Explanation:</p>
<ol>
<li><code>$breadcrumbs</code> is an array of breadcrumbs.</li>
<li><code>$index</code> is available if you have navigated to a directory, or an empty array otherwise
<ul>
<li><code>$index['subcategories']</code> is an array of subdirectories in your current directory</li>
<li><code>$index['files']</code> is an array of files in your current directory</li>
</ul></li>
<li><code>$post</code> is a string of your parsed markdown content</li>
</ol>
<h2>Using a different parser.</h2>
<p>By default the markdown parser used is <a href="https://github.com/erusev/parsedown">erusev/parsedown</a>. To use a different one,
you need to make your own parser that implements the <code>Parseable</code> interface or create an adapter for a different library
that implements <code>Parseable</code>.</p>
<pre><code class="language-php">use Pagemark\Pagemark;

$myCustomParser = new CustomParser;

$postmark = Pagemark::create($myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark-&gt;getContent($basePath, $post);</code></pre>
<pre><code class="language-php">use Pagemark\Pagemark;
use Pagemark\Parser;
use Illuminate\Filesystem\Filesystem;

$myCustomParser = new CustomParser;

$postmark = new Pagemark(new Filesystem, $myCustomParser);

$basePath = '/my/path/to/wiki';
$post = 'Category/Subcategory/My-Post';

$content = $postmark-&gt;getContent($basePath, $post);</code></pre>
HTML;
