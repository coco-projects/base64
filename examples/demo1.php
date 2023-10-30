<?php

    use Coco\base64\Base64;

    require '../vendor/autoload.php';

    $key = Base64::makeRandomKey();

    $instance = Base64::getInstance($key['key'], $key['padding']);

    $str = 'hello 你好 123456';

    $s = $instance->encode($str);
    echo $s;
    echo PHP_EOL;

    echo $instance->decode($s);
    echo PHP_EOL;
