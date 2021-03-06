<?php

namespace React\Tests\Filesystem;

class ArgsExceptionTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testArgs()
    {
        $args = [
            'foo' => 'bar',
            'wibbly wobbly' => time(),
        ];

        $mock = $this->getMockForTrait('React\Filesystem\ArgsExceptionTrait');

        $this->assertSame([], $mock->getArgs());
        $mock->setArgs($args);
        $this->assertSame($args, $mock->getArgs());
    }
} 