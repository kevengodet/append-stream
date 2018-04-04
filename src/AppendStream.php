<?php

namespace Keven\AppendStream;

final class AppendStream
{
    /** @var resource[] */
    private $streams = [];

    public function __construct(iterable $streams = [])
    {
        foreach ($streams as $stream) {
            $this->append($stream);
        }
    }

    /** @param resource $stream */
    public function append($stream): void
    {
        if (!is_resource($stream)) {
            throw InvalidStreamException::fromVar($var);
        }

        if (get_resource_type($stream) !== 'stream') {
            throw InvalidStreamException::fromVar($var);
        }

        $this->streams[] = $stream;
    }

    /** @return resource */
    public function getResource()
    {
        if (!$this->streams) {
            return fopen('data://text/plain,','r');
        }

        if (count($this->streams) == 1) {
            return reset($this->streams);
        }

        $head = array_shift($this->streams);
        $tail = $this->streams;

        $anonymous = new class($tail) extends \php_user_filter
        {
            private static $streams = [];

            public function __construct(array $streams = [])
            {
                self::$streams = $streams;
            }

            /**
             *
             * @param resource $in       Incoming bucket brigade
             * @param resource $out      Outgoing bucket brigade
             * @param int      $consumed Number of bytes consumed
             * @param bool     $closing  Last bucket brigade in stream?
             */
            public function filter($in, $out, &$consumed, $closing)
            {
                $maxLength = 1024;

                while ($bucket = stream_bucket_make_writeable($in)) {
                    $consumed += $bucket->datalen;
                    $maxLength = max($maxLength, $bucket->datalen);
                    stream_bucket_append($out, $bucket);
                }

                $stream = fopen('php://memory', 'r');
                foreach (self::$streams as $stream) {
                    while (feof($stream) !== true) {
                        $bucket = stream_bucket_new($stream, fgets($stream, $maxLength));
                        $consumed += $bucket->datalen;
                        stream_bucket_append($out, $bucket);
                    }
                }

                return PSFS_PASS_ON;
            }
        };

        stream_filter_register($filter = bin2hex(random_bytes(32)), get_class($anonymous));
        stream_filter_append($head, $filter);

        return $head;
    }
}
