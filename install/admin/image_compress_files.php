<?
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$path = \Bitrix\Main\Loader::getLocal('modules/image.compress/admin/image_compress_files.php');
if(file_exists($path)) {
    include $path;
} else {
    ShowMessage('image_compress_files.php not found!');
}
?>