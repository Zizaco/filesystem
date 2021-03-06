<?php

namespace React\Tests\Filesystem\Eio;

use React\Filesystem\Eio\WritableStream;
use React\Promise\RejectedPromise;

class WritableStreamTest extends \PHPUnit_Framework_TestCase
{
    public function classNamesProvider()
    {
        return [
            [
                'React\Filesystem\Eio\WritableStream',
            ],
            [
                'React\Filesystem\Eio\DuplexStream',
            ],
        ];
    }

    /**
     * @dataProvider classNamesProvider
     */
    public function testWrite($className)
    {
        $path = 'foo.bar';
        $fd = '0123456789abcdef';
        $filesystem = $this->getMock('React\Filesystem\EioAdapter', [
            'write',
        ], [
            $this->getMock('React\EventLoop\StreamSelectLoop'),
        ]);

        $filesystem
            ->expects($this->at(0))
            ->method('write')
            ->with($fd, 'abc', 3, 0)
            ->will($this->returnValue($fd))
        ;

        $filesystem
            ->expects($this->at(1))
            ->method('write')
            ->with($fd, 'def', 3, 3)
            ->will($this->returnValue($fd))
        ;

        $filesystem
            ->expects($this->at(2))
            ->method('write')
            ->with($fd, 'ghijklmnopqrstuvwxyz', 20, 6)
            ->will($this->returnValue($fd))
        ;

        $stream = (new $className($path, $fd, $filesystem));
        $stream->write('abc');
        $stream->write('def');
        $stream->write('ghijklmnopqrstuvwxyz');
    }

    /**
     * @dataProvider classNamesProvider
     */
    public function testIsWritable($className)
    {
        $path = 'foo.bar';
        $fd = '0123456789abcdef';
        $filesystem = $this->getMock('React\Filesystem\EioAdapter', [], [
            $this->getMock('React\EventLoop\StreamSelectLoop'),
        ]);

        $this->assertTrue((new $className($path, $fd, $filesystem))->isWritable());
    }

    /**
     * @dataProvider classNamesProvider
     */
    public function testIsNotWritable($className)
    {
        $path = 'foo.bar';
        $fd = '0123456789abcdef';
        $filesystem = $this->getMock('React\Filesystem\EioAdapter', [
            'close',
        ], [
            $this->getMock('React\EventLoop\StreamSelectLoop'),
        ]);


        $filesystem
            ->expects($this->once())
            ->method('close')
            ->with($fd)
            ->will($this->returnValue(new RejectedPromise()))
        ;


        $stream = (new $className($path, $fd, $filesystem));
        $stream->close();
        $this->assertTrue(!$stream->isWritable());
    }

    /**
     * @dataProvider classNamesProvider
     */
    public function testEnd($className)
    {
        $data = 'iahbfeq';
        $stream = $this->getMock($className, [
            'write',
            'close',
        ], [
            'foo.bar',
            '0123456789abcdef',
            $this->getMock('React\Filesystem\EioAdapter', [
                'close',
            ], [
                $this->getMock('React\EventLoop\StreamSelectLoop'),
            ]),
        ]);

        $stream
            ->expects($this->once())
            ->method('write')
            ->with($data)
        ;

        $stream
            ->expects($this->once())
            ->method('close')
            ->with()
        ;

        $stream->end($data);
    }

    /**
     * @dataProvider classNamesProvider
     */
    public function testEndNoWrite($className)
    {
        $stream = $this->getMock($className, [
            'write',
            'close',
        ], [
            'foo.bar',
            '0123456789abcdef',
            $this->getMock('React\Filesystem\EioAdapter', [
                'close',
            ], [
                $this->getMock('React\EventLoop\StreamSelectLoop'),
            ]),
        ]);

        $stream
            ->expects($this->never())
            ->method('write')
            ->with()
        ;

        $stream
            ->expects($this->once())
            ->method('close')
            ->with()
        ;

        $stream->end();
    }

    /**
     * @dataProvider classNamesProvider
     */
    public function testClose($className)
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

        $stream = $this->getMock($className, [
            'emit',
            'removeAllListeners',
        ], [
            $path,
            $fd,
            $filesystem,
        ]);

        $stream
            ->expects($this->at(0))
            ->method('emit')
            ->with('end', [$stream])
        ;

        $stream
            ->expects($this->at(1))
            ->method('emit')
            ->with('close', [$stream])
        ;

        $stream
            ->expects($this->at(2))
            ->method('removeAllListeners')
            ->with()
        ;

        $stream->close();
    }

    /**
     * @dataProvider classNamesProvider
     */
    public function testAlreadyClosed($className)
    {
        $path = 'foo.bar';
        $fd = '0123456789abcdef';
        $filesystem = $this->getMock('React\Filesystem\EioAdapter', [
            'close',
        ], [
            $this->getMock('React\EventLoop\StreamSelectLoop'),
        ]);


        $filesystem
            ->expects($this->once())
            ->method('close')
            ->with($fd)
            ->will($this->returnValue(new RejectedPromise()))
        ;


        $stream = (new $className($path, $fd, $filesystem));
        $stream->close();
        $stream->close();
    }
}
