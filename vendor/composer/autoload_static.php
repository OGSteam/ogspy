<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7f8ed55ac5aa63c4f86a51e8a17b034a
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Ghunti\\HighchartsPHP\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Ghunti\\HighchartsPHP\\' => 
        array (
            0 => __DIR__ . '/..' . '/ghunti/highcharts-php/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7f8ed55ac5aa63c4f86a51e8a17b034a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7f8ed55ac5aa63c4f86a51e8a17b034a::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}