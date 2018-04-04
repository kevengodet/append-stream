<?php

namespace Keven\AppendStream\Tests;

use Keven\AppendStream\AppendStream;

class AppendStreamTest extends \PHPUnit\Framework\TestCase
{
    function testNoStream()
    {
        $stream = new AppendStream;
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('', stream_get_contents($resource));
    }

    function testAppendOneStream()
    {
        $stream = new AppendStream;
        $stream->append($this->createStream('test1'));
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test1', stream_get_contents($resource));
    }

    function testConstructOneStream()
    {
        $stream = new AppendStream([$this->createStream('test2')]);
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test2', stream_get_contents($resource));
    }

    function testAppendSeveralStreams()
    {
        $stream = new AppendStream;
        $stream->append($this->createStream('test3'));
        $stream->append($this->createStream('test4'));
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test3test4', stream_get_contents($resource));
    }

    function testConstructSeveralStreams()
    {
        $stream = new AppendStream([
            $this->createStream('test3'),
            $this->createStream('test4'),
        ]);
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test3test4', stream_get_contents($resource));
    }

    /**
     * @expectedException \Keven\AppendStream\InvalidStreamException
     */
    function testConstructResourceOnly()
    {
        new AppendStream([new \stdclass]);
    }

    /**
     * @expectedException \Keven\AppendStream\InvalidStreamException
     */
    function testAppendResourceOnly()
    {
        $stream = new AppendStream;
        $stream->append(new \stdclass);
    }

    /**
     * @expectedException \Keven\AppendStream\InvalidStreamException
     */
    function testConstructStreamOnly()
    {
        new AppendStream([stream_context_create()]);
    }

    /**
     * @expectedException \Keven\AppendStream\InvalidStreamException
     */
    function testAppendStreamOnly()
    {
        $stream = new AppendStream;
        $stream->append(stream_context_create());
    }

    function createStream($content)
    {
        $handle = fopen('php://memory', 'r+w');
        fwrite($handle, $content);
        rewind($handle);

        return $handle;
    }
}
