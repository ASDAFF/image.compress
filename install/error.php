<?php
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
if(!check_bitrix_sessid()) return;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

Loader::includeModule('main');

CAdminMessage::ShowMessage(array(
    "MESSAGE"=>$GLOBALS['COMPRESS_IMAGE_ERROR'],
    "TYPE"=>"ERROR"
));
echo BeginNote();
echo $GLOBALS['COMPRESS_IMAGE_ERROR_NOTES'];
echo EndNote();

echo '<a href="'.$APPLICATION->GetCurPageParam('STEP=1',['STEP']).'">'.Loc::getMessage('COMPRESS_IMAGE_GOTO_FIRST').'</a><br><br>';
echo '<a href="/bitrix/admin/partner_modules.php">'.Loc::getMessage('COMPRESS_IMAGE_GOTO_MODULES').'</a>';