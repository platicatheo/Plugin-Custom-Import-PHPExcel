<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit785a1041dd3d81081d79f6bec1314a2e
{
    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPExcel' => 
            array (
                0 => __DIR__ . '/..' . '/phpoffice/phpexcel/Classes',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit785a1041dd3d81081d79f6bec1314a2e::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
