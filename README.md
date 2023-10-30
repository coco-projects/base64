
# Base64 tool

##### Base64 tool, allowing customization of the factor.

---

### Here's a quick example:

```php
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
    
    // ";xn+;YIOp-IO"BR.48qr'yF!Ioo
    // hello 你好 123456

```


```php
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

    //aGVsbG8g5L2g5aW9IDEyMzQ1Ng==
    //aGVsbG8g5L2g5aW9IDEyMzQ1Ng==

```

## Installation

You can install the package via composer:

```bash
composer require coco-project/base64 --no-dev
```

## Testing

``` bash
composer test
```

## License

---

MIT
