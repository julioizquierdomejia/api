<?php

use PHPUnit\Framework\TestCase;

class MyFirstTest extends TestCase
{
    public function testHelloWorld()
    {
        $this->assertEquals("hello world", "hello world");
    }
}

?>