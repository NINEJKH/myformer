<?php

namespace App\Transformer;

class Persistence
{
    protected static $storage;

    public static function set($key, $value)
    {
        static::$storage[$key] = $value;
    }

    public static function get($key)
    {
        if (isset(static::$storage[$key])) {
            return static::$storage[$key];
        }
    }
}
