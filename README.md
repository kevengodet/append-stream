# keven/append-stream

Concatenate streams without bloating the memory.

## Install

```shell
$ composer install keven/append-stream
```

## Usage

```php
<?php

use Keven\AppendStream\AppendStream;

$handle = (new AppendStream([$stream1, $stream2, $stream2]))->getResource();

while (feof($handle) !== true) {
    echo fgets($handle); // Concatenate output of $stream1, $stream2 and $stream3
}
```
