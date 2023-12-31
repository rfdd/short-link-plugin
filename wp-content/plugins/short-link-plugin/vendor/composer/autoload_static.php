<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd88aed73658bc9e7891202a05c1ed77d
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Gorjm\\ShortLinkPlugin\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Gorjm\\ShortLinkPlugin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd88aed73658bc9e7891202a05c1ed77d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd88aed73658bc9e7891202a05c1ed77d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd88aed73658bc9e7891202a05c1ed77d::$classMap;

        }, null, ClassLoader::class);
    }
}
