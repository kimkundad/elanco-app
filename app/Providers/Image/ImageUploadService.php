<?php

namespace App\Providers\Image;

class ImageUploadService
{
    protected static function resolveFacade($name)
    {
        return app()[$name];
    }

    public static function __callStatic($method, $arguments)
    {
        return (self::resolveFacade('ImageUploadService'))
            ->$method(...$arguments);
    }
}
