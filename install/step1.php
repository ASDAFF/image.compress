<?
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
if(!check_bitrix_sessid()) return;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option;

IncludeModuleLangFile(__FILE__);

Loader::includeModule('main');
$moduleName = 'image.compress';

Loader::registerAutoLoadClasses(
    $moduleName,
    array(
        'ImageCompress\ImageCompressTable' => 'classes/general/ImageCompressTable.php',
        'ImageCompress\AdminList' => 'lib/AdminList.php',
        'ImageCompress\Check' => 'lib/Check.php',
        'ImageCompress\Compress' => 'lib/Compress.php',
        "ImageCompress" => 'include.php'
    )
);
echo BeginNote();
echo Loc::getMessage('MODULE_IMAGE_COMPRESS_STEP1_NOTES');
echo EndNote();
?>
<form action="<?= $APPLICATION->GetCurPageParam('STEP=2',array('STEP'))?>" method="post">
    <?= bitrix_sessid_post()?>
    <table width="400" border="0" class="table">
        <tr>
            <td>
                <label for="path_to_jpegoptim"><?= Loc::getMessage('COMPRESS_REFERENCES_PATH_JPEGOPTI')?>:</label>
            </td>
            <td>
                <input type="text" name="IMGCOMPRESS_FIELDS[path_to_jpegoptim]" value="<?=Option::get($moduleName, "path_to_jpegoptim", '/usr/bin');?>">
            </td>
        </tr>
        <tr>
            <td>
                <label for="path_to_optipng"><?= Loc::getMessage('COMPRESS_REFERENCES_PATH_PNGOPTI')?>:</label>
            </td>
            <td>
                <input type="text" name="IMGCOMPRESS_FIELDS[path_to_optipng]" value="<?=Option::get($moduleName, "path_to_optipng", '/usr/bin');?>">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" name="save" value="<?= Loc::getMessage('COMPRESS_REFERENCES_GOTO_INSTALL')?>">
            </td>
        </tr>
    </table>
</form>
