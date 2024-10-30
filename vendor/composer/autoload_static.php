<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc74454a12f686337d5bdc63a5dd3514c
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc74454a12f686337d5bdc63a5dd3514c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc74454a12f686337d5bdc63a5dd3514c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc74454a12f686337d5bdc63a5dd3514c::$classMap;

        }, null, ClassLoader::class);
    }
}
