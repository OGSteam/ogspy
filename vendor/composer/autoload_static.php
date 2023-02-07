<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfabe2773b4340eab1e0f4f4782b45d05
{
    public static $prefixLengthsPsr4 = array (
        'b' => 
        array (
            'bdk\\PubSub\\' => 11,
            'bdk\\HttpMessage\\' => 16,
            'bdk\\ErrorHandler\\' => 17,
            'bdk\\Debug\\' => 10,
            'bdk\\Container\\' => 14,
            'bdk\\Backtrace\\' => 14,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'O' => 
        array (
            'Ogsteam\\Ogspy\\Model\\' => 20,
            'Ogsteam\\Ogspy\\Install\\' => 22,
            'Ogsteam\\Ogspy\\Helper\\' => 21,
            'Ogsteam\\Ogspy\\Core\\' => 19,
            'Ogsteam\\Ogspy\\Abstracts\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'bdk\\PubSub\\' => 
        array (
            0 => __DIR__ . '/..' . '/bdk/debug/src/PubSub',
        ),
        'bdk\\HttpMessage\\' => 
        array (
            0 => __DIR__ . '/..' . '/bdk/debug/src/HttpMessage',
        ),
        'bdk\\ErrorHandler\\' => 
        array (
            0 => __DIR__ . '/..' . '/bdk/debug/src/ErrorHandler',
        ),
        'bdk\\Debug\\' => 
        array (
            0 => __DIR__ . '/..' . '/bdk/debug/src/Debug',
        ),
        'bdk\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/bdk/debug/src/Container',
        ),
        'bdk\\Backtrace\\' => 
        array (
            0 => __DIR__ . '/..' . '/bdk/debug/src/Backtrace',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/bdk/debug/src/Psr7',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Ogsteam\\Ogspy\\Model\\' => 
        array (
            0 => __DIR__ . '/../..' . '/model',
        ),
        'Ogsteam\\Ogspy\\Install\\' => 
        array (
            0 => __DIR__ . '/../..' . '/install',
        ),
        'Ogsteam\\Ogspy\\Helper\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/helper',
        ),
        'Ogsteam\\Ogspy\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core',
        ),
        'Ogsteam\\Ogspy\\Abstracts\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/abstract',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'bdk\\Backtrace' => __DIR__ . '/..' . '/bdk/debug/src/Backtrace/Backtrace.php',
        'bdk\\Container' => __DIR__ . '/..' . '/bdk/debug/src/Container/Container.php',
        'bdk\\Debug' => __DIR__ . '/..' . '/bdk/debug/src/Debug/Debug.php',
        'bdk\\Debug\\Utility' => __DIR__ . '/..' . '/bdk/debug/src/Debug/Utility/Utility.php',
        'bdk\\ErrorHandler' => __DIR__ . '/..' . '/bdk/debug/src/ErrorHandler/ErrorHandler.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfabe2773b4340eab1e0f4f4782b45d05::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfabe2773b4340eab1e0f4f4782b45d05::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfabe2773b4340eab1e0f4f4782b45d05::$classMap;

        }, null, ClassLoader::class);
    }
}
