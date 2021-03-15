# Install

```bash
composer require peidgnad/tag-wrapper
```

# Usage

```php
use Peidgnad\TagWrapper\TagWrapper;

$string = '<p>
    This string contain a keyword
    <span>This string contain keyword</span>
    <a href="test">This string contain a keyword</a>
    <a href=\'test\'>This string contain a keyword</a>
    <a data-keyword>This string contain a keyword</a>
    <a keyword>This string contain a keyword</a>
    <a data-keyword="keyword">This string contain a keyword</a>
    <a keyword="keyword">This string contain a keyword</a>
    <span data-keyword="keyword" keyword>This string contain a keyword</span>
    <img data-keyword src="keyword" />
    <input type="text" value="keyword">
    <input type=text value="keyword">
    <input type="text" value="keyword" >
</p>
';

$wrapper = TagWrapper::find(['a keyword', 'keyword'], $string);

$prepend = function ($keyword) {
    return '<a href="/tag/'.$keyword.'">';
};

$wrapper->wrapWith($prepend, '</a>');
$wrapper->ignoreIndex(0);

$result = $wrapper->exec();

var_dump(
    $result->getWrapped(),
    $result->getIndexes(),
    $result->getPositions()
);

echo $result;
```
