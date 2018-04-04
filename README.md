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

// Concatenate 3 streams
$handle = (new AppendStream([
    fopen('data://text/plain,stream1','r'),
    fopen('data://text/plain,stream2','r'),
    fopen('data://text/plain,stream3','r'),
]))->getResource();

echo stream_get_contents($handle);
// stream1stream2stream3
```
