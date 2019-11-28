<?php
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace ImageCompress;

use Bitrix\Main\Config\Option;

class Check
{
    public static function isPNGOptim() {
        $path = Option::get('image.compress','path_to_optipng', '/usr/bin');
        exec($path.'/optipng -v',$s);
        return ($s?true:false);
    }

    public static function isJPEGOptim() {
        $path = Option::get('image.compress','path_to_jpegoptim', '/usr/bin');
        exec($path.'/jpegoptim --version',$s);
        return ($s?true:false);
    }

    public static function isRead($path) {
        return is_readable($path);
    }

    public static function isWrite($path) {
        return is_writable($path);
    }
}