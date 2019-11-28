<?php
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Dev2fun\ImageCompress\AdminList;
use Dev2fun\ImageCompress\Compress;
use Bitrix\Main\Localization\Loc;

$curModuleName = "image.compress";
Loader::includeModule($curModuleName);
//CModule::IncludeModule($curModuleName);

IncludeModuleLangFile(__FILE__);

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 **/

$canRead = $USER->CanDoOperation('image_compress_list_read');
$canWrite = $USER->CanDoOperation('image_compress_list_write');
if(!$canRead && !$canWrite)
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


$EDITION_RIGHT = $APPLICATION->GetGroupRight($curModuleName);
if ($EDITION_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = array(
    array(
        "DIV" => "main",
        "TAB" => GetMessage("SEC_MAIN_TAB"),
        "ICON"=>"main_user_edit",
        "TITLE"=>GetMessage("SEC_MAIN_TAB_TITLE"),
    ),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);

$bVarsFromForm = false;
$APPLICATION->SetTitle(GetMessage("SEC_IMG_COMPRESS_TITLE"));

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$recCompress = null;
if ($_REQUEST["compress"]) {

    if($compress = $_REQUEST["compress"]) {
        if(!is_array($compress)) {
            $compress = array($compress);
        }
        foreach($compress as $fileID) {
            $fileID = intval($fileID);
            $recCompress = Compress::getInstance()->compressImageByID($fileID);
        }
    }

} elseif($_REQUEST["action"] == "compress") {

    $arIDs = $_REQUEST["ID"];
    if(is_array($arIDs) && count($arIDs)) {
        set_time_limit(0);
        foreach($arIDs as $fileID) {
            $recCompress = Compress::getInstance()->compressImageByID($fileID);
        }
    }

}

$list = new AdminList($curModuleName);
$list->generalKey = 'ID';
$list->SetRights();
$list->SetTitle(GetMessage('IMAGE_COMPRESS_TITLE'));
$list->SetGroupAction(array(
    'compress' => function($hash) {},
));
$list->SetContextMenu(false);
$list->SetHeaders(array(
    'ID' => "ID",
    'MODULE_ID' => GetMessage('IMAGE_COMPRESS_MODULE_ID'),
    'CONTENT_TYPE' => GetMessage("IMAGE_COMPRESS_CONTENT_TYPE"),
    'FILE_NAME' => GetMessage("IMAGE_COMPRESS_FILE_NAME"),
    'ORIGINAL_NAME' => GetMessage("IMAGE_COMPRESS_ORIGINAL_NAME"),
    'DESCRIPTION' => GetMessage("IMAGE_COMPRESS_DESCRIPTION"),
    'FILE_SIZE' => GetMessage("IMAGE_COMPRESS_FILE_SIZE"),
    'IMAGE' => GetMessage("IMAGE_COMPRESS_IMAGE"),
    'COMPRESS' => GetMessage("IMAGE_COMPRESS_COMPRESS"),
    'SIZE_BEFORE' => GetMessage("IMAGE_COMPRESS_SIZE_BEFORE"),
    'SIZE_AFTER' => GetMessage("IMAGE_COMPRESS_SIZE_AFTER"),
));
$list->SetFilter(array(
    'id' => array('TITLE' => GetMessage('IMAGE_COMPRESS_FILTER_ID'), 'OPER' => ''),
    'file_size' => array(
        'TITLE' => GetMessage('IMAGE_COMPRESS_FILTER_FILE_SIZE'),
    ),
    'comressed' => array(
        'TITLE' => GetMessage('IMAGE_COMPRESS_FILTER_COMRESSED'),
        'TYPE' => 'select',
        'VARIANTS' => Array(
            "Y" => GetMessage('IMAGE_COMPRESS_FILTER_COMRESSED_Y'),
            "N" => GetMessage('IMAGE_COMPRESS_FILTER_COMRESSED_N'),
        )
    ),
    'module_id' => array('TITLE' => GetMessage('IMAGE_COMPRESS_FILTER_MODULE_ID')),
    'original_name' => array('TITLE' => GetMessage('IMAGE_COMPRESS_FILTER_ORIGINAL_NAME'), 'OPER' => '?'),
    'file_name' => array('TITLE' => GetMessage('IMAGE_COMPRESS_FILTER_FILE_NAME'), 'OPER' => '?'),
    'content_type' => array(
        'TITLE' => GetMessage('IMAGE_COMPRESS_FILTER_FILE_TYPE'),
        'TYPE' => 'select',
        'OPER' => '@',
        'VARIANTS' => array(
            'image/png' => GetMessage('IMAGE_COMPRESS_MIME_PNG'),
            'image/jpeg' => GetMessage('IMAGE_COMPRESS_MIME_JPG')
        )
    ),
));
if (!isset($by))
    $by = 'ID';
if (!isset($order))
    $order = 'ASC';

$rsFiles = Compress::getInstance()->getFileList(Array($by => $order), $list->makeFilter());

$list->SetList(
    $rsFiles,
    array(
        'IMAGE' => function($val, $arRec){
            $strFilePath = \CFile::GetPath($arRec["ID"]);
            if(file_exists($_SERVER['DOCUMENT_ROOT'].$strFilePath)) {
                return "<img style='max-width: 200px; height: auto;' src='" . $strFilePath . "'>";
            } else {
                return "<span class='text-error'>".GetMessage('IMAGE_COMPRESS_FILE_NOT_FOUND')."</span>";
            }
        },
        'COMPRESS' => function($val, $arRec){
            $strFilePath = \CFile::GetPath($arRec["ID"]);
            if(file_exists($_SERVER['DOCUMENT_ROOT'].$strFilePath)) {
                if (intval($arRec['FILE_ID']) <= 0) {
                    return "<button value='" . $arRec["ID"] . "' name='compress' data-image-id='" . $arRec["ID"] . "' href='#'>" . GetMessage("IMAGE_COMPRESS_COMPRESS") . "</button>";
                } else {
                    return GetMessage('IMAGE_COMPRESS_COMRESSED');
                }
            } else {
                return "<span class='text-error'>".GetMessage('IMAGE_COMPRESS_FILE_NOT_FOUND')."</span>";
            }
        },
        'SIZE_BEFORE' => function($val, $arRec){
            return Compress::getInstance()->getNiceFileSize($arRec["SIZE_BEFORE"]);
        },
        'SIZE_AFTER' => function($val, $arRec){
            return Compress::getInstance()->getNiceFileSize($arRec["SIZE_AFTER"]);
        },
        'FILE_SIZE' => function($val, $arRec){
            return Compress::getInstance()->getNiceFileSize($arRec["FILE_SIZE"]);
        }
    ),
    false
);
$list->SetFooter(array(
    'compress' => GetMessage('IMAGE_COMPRESS_COMPRESS'),
));
$list->Output();
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>