<?php

namespace React\Tests\Filesystem\Stream;

class ReadableStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $path = 'foo.bar';
        $fd = '0123456789abcdef';

        $promise = $this->getMock('React\Promise\PromiseInterface', [
            'then',
        ]);

        $promise
            ->expects($this->once())
            ->method('then')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb([
                    'size' => 123,
                ]);
            }))
        ;

        $filesystem = $this->getMock('React\Filesystem\EioAdapter', [], [
            $this->getMock('React\EventLoop\StreamSelectLoop'),
        ]);

        $filesystem
            ->expects($this->once())
            ->method('stat')
            ->with($path)
            ->will($this->returnValue($promise))
        ;

        $this->getMock('React\Filesystem\Eio\ReadableStream', [
            'readChunk',
        ], [
            $path,
            $fd,
            $filesystem,
        ]);
    }

    public function testResume()
    {
        $path = 'foo.bar';
        $fd = '0123456789abcdef';

        $promise = $this->getMock('React\Promise\PromiseInterface', [
            'then',
        ]);

        $promise
            ->expects($this->once())
            ->method('then')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb([
                    'size' => 123,
                ]);
            }))
        ;

        $filesystem = $this->getMock('React\Filesystem\EioAdapter', [], [
            $this->getMock('React\EventLoop\StreamSelectLoop'),
        ]);

        $filesystem
            ->expects($this->once())
            ->method('stat')
            ->with($path)
            ->will($this->returnValue($promise))
        ;

        $this->getMock('React\Filesystem\Eio\ReadableStream', [
            'readChunk',
        ], [
            $path,
            $fd,
            $filesystem,
        ]);
    }

    public function testClose()
    {
        $path = 'foo.bar';
        $fd = '0123456789abcdef';

        $filesystem = $this->getMock('React\Filesystem\EioAdapter', [
            'close',
        ], [
            $this->getMock('React\EventLoop\StreamSelectLoop'),
        ]);


        $promise = $this->getMock('React\Promise\PromiseInterface', [
            'then',
        ]);

        $promise
            ->expects($this->once())
            ->method('then')
            ->with($this->isType('callable'))
            ->will($this->returnCallback(function ($resolveCb) {
                $resolveCb();
            }))
        ;

        $filesystem
            ->expects($this->once())
            ->method('close')
            ->with($fd)
            ->will($this->returnValue($promise))
        ;

        $stream = $this->getMock('React\Filesystem\Eio\ReadableStream', [
            'emit',
        ], [
            $path,
            $fd,
            $filesystem,
        ]);

        $stream
            ->expects($this->once())
            ->method('emit')
            ->with('close', [$stream])
        ;

        $stream->close();
    }
}