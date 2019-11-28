<?php
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace ImageCompress;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

//Loc::loadMessages(__FILE__);
IncludeModuleLangFile(__FILE__);

class ImageCompressTable extends Entity\DataManager {

    static $module_id = "image.compress";

    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'b_image_compress_files';
    }

    public static function getTableTitle()
    {
        return Loc::getMessage('IMAGE_COMPRESS_REDIRECTS_TITLE');
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('SIZE_BEFORE'),
            new Entity\IntegerField('SIZE_AFTER'),
            new Entity\IntegerField('FILE_ID', array(
                'primary' => true,
            )),
            new Entity\ReferenceField(
                'FILE',
                'Bitrix\Main\FileTable',
                array(
                    '=this.FILE_ID' => 'ref.ID'
                )
            ),
        );
    }
}