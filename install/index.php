<?
/**
 * Copyright (c) 28/11/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
IncludeModuleLangFile(__FILE__);

use Bitrix\Main\ModuleManager,
    Bitrix\Main\EventManager,
    Dev2fun\ImageCompress\ImageCompressTable;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\IO\Directory,
    Bitrix\Main\Config\Option;

Loader::registerAutoLoadClasses(
    "image.compress",
    array(
        'Dev2fun\ImageCompress\ImageCompressTable' => 'classes/general/ImageCompressTable.php',
        'Dev2fun\ImageCompress\AdminList' => 'lib/AdminList.php',
        'Dev2fun\ImageCompress\Check' => 'lib/Check.php',
        'Dev2fun\ImageCompress\Compress' => 'lib/Compress.php',
        "Dev2funImageCompress" => 'include.php',
    )
);

class image_compress extends CModule
{
    var $MODULE_ID = "image.compress";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
	{
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (isset($arModuleVersion) && is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		} else {
			$this->MODULE_VERSION = '0.1.8';
			$this->MODULE_VERSION_DATE = '2017-08-06 02:00:00';
		}
		$this->MODULE_NAME = Loc::getMessage("MODULE_NAME_IMAGECOMPRESS");
		$this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESCRIPTION_IMAGECOMPRESS");

		$this->PARTNER_NAME = "ASDAFF";
		$this->PARTNER_URI = "https://asdaff.github.io/";
    }
 
    function DoInstall() {
        global $APPLICATION;
//        ini_set('display_errors',true);
        if(!check_bitrix_sessid()) return;
        try {
            if($_REQUEST['STEP']==1||!$_REQUEST['IMGCOMPRESS_FIELDS']) {
                $APPLICATION->IncludeAdminFile(
                    Loc::getMessage("MODULE_IMAGE_COMPRESS_STEP1"),
                    __DIR__ . "/step1.php"
                );
            } else {
                $this->saveFields();
                $this->check();
                $this->installFiles();
                $this->installDB();
                $this->registerEvents();
                ModuleManager::registerModule($this->MODULE_ID);
            }
        } catch (Exception $e) {
            $GLOBALS['COMPRESS_IMAGE_ERROR'] = $e->getMessage();
            $GLOBALS['COMPRESS_IMAGE_ERROR_NOTES'] = Loc::getMessage('IMAGE_COMPRESS_ERROR_CHECK_NOFOUND_NOTES');
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage("MODULE_IMAGE_COMPRESS_STEP_ERROR"),
                __DIR__."/error.php"
            );
            return false;
        }
        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("MODULE_IMAGE_COMPRESS_STEP_FINAL"),
            __DIR__."/final.php"
        );
    }
     
    function DoUninstall() {
        global $APPLICATION;
//        ModuleManager::unRegisterModule($this->MODULE_ID);
        if(!check_bitrix_sessid()) return;
        $this->deleteFiles();
        $this->unInstallDB();
        $this->unRegisterEvents();
        ModuleManager::unRegisterModule($this->MODULE_ID);
        $admMsg = new CAdminMessage(false);
        $admMsg->ShowMessage(array(
            "MESSAGE"=>Loc::getMessage('IMAGE_COMPRESS_UNINSTALL_SUCCESS'),
            "TYPE"=>"OK"
        ));
        echo BeginNote();
        echo Loc::getMessage("IMAGE_COMPRESS_UNINSTALL_LAST_MSG");
        EndNote();
    }

    public function saveFields() {
        if($pthJpeg = $_REQUEST['IMGCOMPRESS_FIELDS']['path_to_jpegoptim']) {
            Option::set($this->MODULE_ID,'path_to_jpegoptim',$pthJpeg);
        }
        if($pthPng = $_REQUEST['IMGCOMPRESS_FIELDS']['path_to_optipng']) {
            Option::set($this->MODULE_ID,'path_to_optipng',$pthPng);
        }
    }

    public function check() {
        if(!ImageCompress\Check::isJPEGOptim()) {
            throw new Exception(Loc::getMessage('IMAGE_COMPRESS_ERROR_CHECK_NOFOUND',array('#MODULE#'=>'jpegoptim')));
        }

        if(!ImageCompress\Check::isPNGOptim()) {
            throw new Exception(Loc::getMessage('IMAGE_COMPRESS_ERROR_CHECK_NOFOUND', array('#MODULE#' => 'optipng')));
        }
    }

    public function installDB() {
        ImageCompressTable::getEntity()->createDbTable();
//        Option::set($this->MODULE_ID,'path_to_optipng','/usr/bin');
//        Option::set($this->MODULE_ID,'path_to_jpegoptim','/usr/bin');

        Option::set($this->MODULE_ID,'enable_element','Y');
        Option::set($this->MODULE_ID,'enable_section','Y');
        Option::set($this->MODULE_ID,'enable_resize','Y');
        Option::set($this->MODULE_ID,'enable_save','Y');

        Option::set($this->MODULE_ID,'jpegoptim_compress','80');
        Option::set($this->MODULE_ID,'jpeg_progressive','Y');
        Option::set($this->MODULE_ID,'optipng_compress','5');
        return true;
    }

    public function registerEvents() {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, "Dev2fun\\ImageCompress\\Compress", "CompressImageOnElementEvent");
        $eventManager->registerEventHandler("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, "Dev2fun\\ImageCompress\\Compress", "CompressImageOnElementEvent");

        $eventManager->registerEventHandler("iblock", "OnAfterIBlockSectionAdd", $this->MODULE_ID, "Dev2fun\\ImageCompress\\Compress", "CompressImageOnSectionEvent");
        $eventManager->registerEventHandler("iblock", "OnAfterIBlockSectionUpdate", $this->MODULE_ID, "Dev2fun\\ImageCompress\\Compress", "CompressImageOnSectionEvent");

        $eventManager->registerEventHandler("main", "OnFileSave", $this->MODULE_ID, "Dev2fun\\ImageCompress\\Compress", "CompressImageOnFileEvent");
        $eventManager->registerEventHandler("main", "OnFileDelete", $this->MODULE_ID, "Dev2fun\\ImageCompress\\Compress", "CompressImageOnFileDeleteEvent");

        $eventManager->registerEventHandler("main", "OnAfterResizeImage", $this->MODULE_ID, "Dev2fun\\ImageCompress\\Compress", "CompressImageOnResizeEvent");

        $eventManager->registerEventHandler("main", "OnBuildGlobalMenu", $this->MODULE_ID, "Dev2funImageCompress", "DoBuildGlobalMenu");

        return true;
    }

    public function installFiles() {
        CopyDirFiles(__DIR__."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
        CopyDirFiles(__DIR__."/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
        return true;
    }

    public function unInstallDB() {
        $connection = Application::getInstance()->getConnection();
        $connection->dropTable(ImageCompressTable::getTableName());
        Option::delete($this->MODULE_ID);
        return true;
    }

    public function deleteFiles() {
        DeleteDirFilesEx('/bitrix/admin/image_compress_files.php');
        DeleteDirFilesEx('/bitrix/themes/.default/icons/image.compress');
        DeleteDirFilesEx('/bitrix/themes/.default/image.compress.css');
        return true;
    }

    public function unRegisterEvents() {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler('main','OnFileSave',$this->MODULE_ID);
        $eventManager->unRegisterEventHandler('main','OnFileDelete',$this->MODULE_ID);

        $eventManager->unRegisterEventHandler('main','OnAfterResizeImage',$this->MODULE_ID);

        $eventManager->unRegisterEventHandler('iblock','OnAfterIBlockSectionUpdate',$this->MODULE_ID);
        $eventManager->unRegisterEventHandler('iblock','OnAfterIBlockSectionAdd',$this->MODULE_ID);

        $eventManager->unRegisterEventHandler('iblock','OnAfterIBlockElementUpdate',$this->MODULE_ID);
        $eventManager->unRegisterEventHandler('iblock','OnAfterIBlockElementAdd',$this->MODULE_ID);

        $eventManager->unRegisterEventHandler('main','OnBuildGlobalMenu',$this->MODULE_ID);
        return true;
    }
}
