<?php

namespace Ogsteam\Ogspy\Abstracts;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


abstract class Helper_Abstract
{
    protected static $name = "undefined";
    protected static $description = "";
    protected static $version = "0.0.0";

    public static function getVersion()
    {
        return static::$version;
    }

    public static function getName()
    {
        return static::$name;
    }

    public static function getDescription()
    {
        return static::$description;
    }


}
