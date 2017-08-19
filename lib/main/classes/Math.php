<?php

class Math
{
    public static function randomString($length, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $ret = '';
        $size = strlen($chars);
        $i = 0;
        while ($i < $length) {
            $ret .= $chars[rand(0, $size - 1)];
            $i++;
        }

        return $ret;
    }

}
