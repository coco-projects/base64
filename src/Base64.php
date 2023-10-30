<?php

    declare(strict_types = 1);

    namespace Coco\base64;

class Base64
{
    private $key = '';

    private $paddingChar = '';

    private static array $instance = [];


    public static function getInstance($_alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/', $paddingChar = '='): static
    {
        static::validate($_alpha, $paddingChar);

        $key = self::makeKey($_alpha . $paddingChar);

        if (!isset(self::$instance[$key])) {
            $obj = new static();
            $obj->setKey($_alpha);
            $obj->setPaddingChar($paddingChar);

            self::$instance[$key] = $obj;
        }

        return self::$instance[$key];
    }

    public static function makeRandomKey(): array
    {
        $factor = [
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "abcdefghijklmnopqrstuvwxyz",
            "1234567890",
            "\\~!@#$%^&*()_+`-=[]{};':\" |,./?",
        ];
        $factor = implode('', $factor);
        $factor = str_split($factor);
        shuffle($factor);
        $factor = implode('', $factor);

        return [
            'key'     => substr($factor, 0, 64),
            'padding' => substr($factor, 64, 1),
        ];
    }

    private function setKey($key): static
    {
        $this->key = $key;

        return $this;
    }

    private static function validate($key, $paddingChar): void
    {
        if (!preg_match('/^(?-i)((?!.*\1)[ ~!@#$%^&*()_+`\-=[\]{};\'\:"|,.\/?\da-zA-Z\\\\]){64}$/im', $key)) {
            throw new \InvalidArgumentException("key 输入的不是有效的字符串，要求字符必须为 64 位不重复的区分大小写的字母，数字，或者指定范围的特殊字符，正则表达式表示为 ： ^(?-i)((?!.*\1)[~!@#$%^&*()_+`\-=[\]{};'\:\"|,.\/<>?\da-zA-Z\\]){64}$ ，建议使用内置的 makeRandomKey 方法自动生成");
        }

        if (false !== strpos($key, $paddingChar)) {
            throw new \InvalidArgumentException("paddingChar 输入的不是有效的字符串，要求字符必须为 1 位字母，数字，或者指定范围的特殊字符，正则表达式表示为 ： ^[~!@#$%^&*()_+`\-=[\]{};'\:\"|,.\/<>?\da-zA-Z]$ ,且不能与 key 包含的字符重叠 ，建议使用内置的 makeRandomKey 方法自动生成");
        }
    }

    private function setPaddingChar(string $paddingChar): static
    {
        $this->paddingChar = $paddingChar;

        return $this;
    }


    private function _getbyte64($str, $i): mixed
    {
        return strpos($this->key, $str[$i]);
    }

    private function _getbyte($s, $i): int
    {
        $x = ord($s[$i]);
        if ($x > 255) {
            throw new \InvalidArgumentException("key 输入的不是有效的字符串，要求字符必须为 64 位不重复的区分大小写的字母，数字，或者指定范围的特殊字符，正则表达式表示为 ： ^(?-i)((?!.*\1)[ ~!@#$%^&*()_+`\-=[\]{};'\:\"|,.\/<>?\da-zA-Z]){64}$，建议使用内置的 makeRandomKey 方法自动生成");
        }

        return $x;
    }

    public function encode($s = ''): string
    {
        $s    = (string)$s;
        $x    = [];
        $imax = strlen($s) - strlen($s) % 3;

        if (strlen($s) === 0) {
            return $s;
        }
        for ($i = 0; $i < $imax; $i += 3) {
            $b10 = ($this->_getbyte($s, $i) << 16) | ($this->_getbyte($s, $i + 1) << 8) | $this->_getbyte($s, $i + 2);
            $x[] = ($this->key[($b10 >> 18)]);
            $x[] = ($this->key[(($b10 >> 12) & 0x3F)]);
            $x[] = ($this->key[(($b10 >> 6) & 0x3f)]);
            $x[] = ($this->key[($b10 & 0x3f)]);
        }
        switch (strlen($s) - $imax) {
            case 1:
                $b10 = $this->_getbyte($s, $i) << 16;
                $x[] = ($this->key[($b10 >> 18)] . $this->key[(($b10 >> 12) & 0x3F)] . $this->paddingChar . $this->paddingChar);
                break;
            case 2:
                $b10 = ($this->_getbyte($s, $i) << 16) | ($this->_getbyte($s, $i + 1) << 8);
                $x[] = ($this->key[($b10 >> 18)] . $this->key[(($b10 >> 12) & 0x3F)] . $this->key[(($b10 >> 6) & 0x3f)] . $this->paddingChar);
                break;
        }

        return implode('', $x);
    }

    public function decode($s = ''): string
    {
        $s    = (string)$s;
        $pads = 0;
        $imax = strlen($s);
        $x    = [];

        if ($imax === 0) {
            return $s;
        }
        if ($imax % 4 !== 0) {
            throw new \InvalidArgumentException("输入的不是有效的 Base-64 字符串，因为它包含非 Base-64 字符、两个以上的填充字符，或者填充字符间包含非法字符，建议使用内置的 makeRandomKey 方法自动生成");
        }

        if ($s[$imax - 1] === $this->paddingChar) {
            $pads = 1;
            if ($s[$imax - 2] === $this->paddingChar) {
                $pads = 2;
            }

            $imax -= 4;
        }
        for ($i = 0; $i < $imax; $i += 4) {
            $b10 = ($this->_getbyte64($s, $i) << 18) | ($this->_getbyte64($s, $i + 1) << 12) | ($this->_getbyte64($s, $i + 2) << 6) | $this->_getbyte64($s, $i + 3);
            $x[] = (chr($b10 >> 16) . chr(($b10 >> 8) & 0xff) . chr($b10 & 0xff));
        }
        switch ($pads) {
            case 1:
                $b10 = ($this->_getbyte64($s, $i) << 18) | ($this->_getbyte64($s, $i + 1) << 12) | ($this->_getbyte64($s, $i + 2) << 6);
                $x[] = (chr($b10 >> 16) . chr(($b10 >> 8) & 0xff));
                break;
            case 2:
                $b10 = ($this->_getbyte64($s, $i) << 18) | ($this->_getbyte64($s, $i + 1) << 12);
                $x[] = (chr($b10 >> 16));
                break;
        }

        return implode('', $x);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPaddingChar(): string
    {
        return $this->paddingChar;
    }

    public static function makeKey(string $key): string
    {
        return md5($key);
    }
}
