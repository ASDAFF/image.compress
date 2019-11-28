<?php
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
IncludeModuleLangFile(__FILE__);

//CModule::IncludeModule("dev2fun.versioncontrol");
global $DBType;

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses(
	"image.compress",
	array(
	    'ImageCompress\ImageCompressTable' => 'classes/general/ImageCompressTable.php',
	    'ImageCompress\AdminList' => 'lib/AdminList.php',
	    'ImageCompress\Check' => 'lib/Check.php',
	    'ImageCompress\Compress' => 'lib/Compress.php',
		"ImageCompress" => __FILE__,
	)
);

class ImageCompress {

    public function DoBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu) {
        $aModuleMenu[] = array(
            "parent_menu" => "global_menu_settings",
            "icon" => "compress_image_menu_icon",
            "page_icon" => "compress_image_page_icon",
            "sort"=>"900",
            "text"=> GetMessage("IMAGE_COMPRESS_MENU_TEXT"),
            "title"=> GetMessage("IMAGE_COMPRESS_MENU_TITLE"),
            "url"=>"/bitrix/admin/image_compress_files.php",
            "items_id" => "menu_compress_image",
            "section" => "imagecompress",
            "more_url"=>array(),
//            "items" => array(
//                array(
//                    "text" => GetMessage("IMAGE_COMPRESS_SUB_SETINGS_MENU_TEXT"),
//                    "title"=> GetMessage("IMAGE_COMPRESS_SUB_SETINGS_MENU_TITLE"),
//                    "url"=>"/bitrix/admin/image_compress_files.php",
//                    "sort"=>"100",
//                    "icon" => "sys_menu_icon",
//                    "page_icon" => "default_page_icon",
//                ),
//            )
        );
    }
}