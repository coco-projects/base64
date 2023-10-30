<?php

    declare(strict_types = 1);

    namespace Coco\Tests\Unit;

    use Coco\base64\Base64;
    use PHPUnit\Framework\TestCase;

final class Base64Test extends TestCase
{
    public function testA()
    {
        $key = Base64::makeRandomKey();

        $instance = Base64::getInstance($key['key'], $key['padding']);

        $str = 'hello 你好 123456';

        $s = $instance->encode($str);

        $this->assertEquals($str, $instance->decode($s));
    }

    public function testB()
    {
        $instance = Base64::getInstance();

        $str = 'hello 你好 123456';

        $s = $instance->encode($str);

        $s1 = base64_encode($str);

        $this->assertEquals($s, $s1);
    }

    public function testC()
    {
        $key = Base64::makeRandomKey();

        $instance = Base64::getInstance($key['key'], $key['padding']);

        $str = 'fd';

        $s = $instance->encode($str);

        $this->assertEquals($str, $instance->decode($s));
    }

    public function testD()
    {
        $instance = Base64::getInstance();

        $str = 'fdf';

        $s = $instance->encode($str);

        $s1 = base64_encode($str);

        $this->assertEquals($s, $s1);
    }

    public function testE()
    {
        $instance = Base64::getInstance();

        $str = '';

        $s = $instance->encode($str);

        $s1 = base64_encode($str);

        $this->assertEquals($s, $s1);
    }

    public function testF()
    {
        $this->expectException(\InvalidArgumentException::class);
        $instance = Base64::getInstance('123', '');

        $str = 'hello';

        $instance->encode($str);
    }

    public function testG()
    {
        $this->expectException(\InvalidArgumentException::class);
        $instance = Base64::getInstance('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/', '');

        $str = 'hello';

        $instance->encode($str);
    }
}
