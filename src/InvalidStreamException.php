<?php

namespace Keven\AppendStream;

final class InvalidStreamException extends \InvalidArgumentException
{
    public static function fromVar($var): InvalidStreamException
    {
        $message = 'Invalid stream resource given: '.gettype($var);

        if (is_resource($var)) {
            $message .= ' '.get_resource_type($var);
        } elseif (is_object($var)) {
            $message .= ' '.get_class($var);
        }

        throw new self($message);
    }
}
