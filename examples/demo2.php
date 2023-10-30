<?php

    use Coco\base64\Base64;

    require '../vendor/autoload.php';

    $instance = Base64::getInstance();

    $str = 'hello 你好 123456';

    $s = $instance->encode($str);
    echo $s;
    echo PHP_EOL;

    $s1 = base64_encode($str);
    echo $s1;
    echo PHP_EOL;
