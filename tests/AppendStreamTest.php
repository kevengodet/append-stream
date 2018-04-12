<?php

namespace Keven\AppendStream\Tests;

use Keven\AppendStream\AppendStream;

class AppendStreamTest extends \PHPUnit\Framework\TestCase
{
    function testNoStream()
    {
        $stream = new AppendStream([], 8);
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('', stream_get_contents($resource));
    }

    function testAppendOneStream()
    {
        $stream = new AppendStream([], 8);
        $stream->append(fopen('data://text/plain,test1','r'));
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test1', stream_get_contents($resource));
    }

    function testConstructOneStream()
    {
        $stream = new AppendStream([fopen('data://text/plain,test2','r')], 8);
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test2', stream_get_contents($resource));
    }

    function testAppendSeveralStreams()
    {
        $stream = new AppendStream([], 8);
        $stream->append(fopen('data://text/plain,test3','r'));
        $stream->append(fopen('data://text/plain,test4','r'));
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test3test4', stream_get_contents($resource));
    }

    function testConstructSeveralStreams()
    {
        $stream = new AppendStream([
            fopen('data://text/plain,test3','r'),
            fopen('data://text/plain,test4','r'),
        ], 8);
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals('test3test4', stream_get_contents($resource));
    }

    function testAppendBigStreams()
    {
        $stream = new AppendStream([], 8);
        $stream->append(fopen('data://text/plain,'.$s1=str_repeat('1', 10240),'r'));
        $stream->append(fopen('data://text/plain,'.$s2=str_repeat('2', 10240),'r'));
        $stream->append(fopen('data://text/plain,'.$s3=str_repeat('3', 10240),'r'));
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals("$s1$s2$s3", $s = stream_get_contents($resource));
    }

    function testConstructBigStreams()
    {
        $stream = new AppendStream([
            fopen('data://text/plain,'.$s1=str_repeat('1', 10240),'r'),
            fopen('data://text/plain,'.$s2=str_repeat('2', 10240),'r'),
            fopen('data://text/plain,'.$s3=str_repeat('3', 10240),'r'),
        ], 8);
        $resource = $stream->getResource();

        $this->assertTrue(is_resource($resource));
        $this->assertEquals('stream', get_resource_type($resource));
        $this->assertEquals("$s1$s2$s3", $s = stream_get_contents($resource));
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
}
