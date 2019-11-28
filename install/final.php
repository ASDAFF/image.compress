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
    "MESSAGE"=>Loc::getMessage('IMAGE_COMPRESS_INSTALL_SUCCESS'),
    "TYPE"=>"OK"
));
echo BeginNote();
echo Loc::getMessage("IMAGE_COMPRESS_INSTALL_LAST_MSG");
echo EndNote();