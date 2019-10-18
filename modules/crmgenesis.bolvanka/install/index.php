<?php

//подключение файла с какими-то данными модуля и проверкой на D7
include_once(dirname(__DIR__).'/lib/main.php');

//подключение файла с базовыми функциями
include_once(dirname(__DIR__).'/lib/bitrixfunctions.php');

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;

//Это подключение файла с классом тек. модуля
use \Crmgenesis\Bolvanka\Main;

//подключение файла с базовыми функциями здесь, чтобы вызывать в нужном классе его функции
use \Crmgenesis\Bolvanka\bitrixfunctions;

//Lang-файлы
Loc::loadMessages(__FILE__);

class crmgenesis_bolvanka extends CModule{

    var $MODULE_ID = 'crmgenesis.bolvanka';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';


    public function __construct(){
        $arModuleVersion = [];
        include(__DIR__."/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("CRM_GENESIS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("CRM_GENESIS_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("CRM_GENESIS_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("CRM_GENESIS_PARTNER_URI");
    }

    public function InstallEvents(){
        EventManager::getInstance()->registerEventHandler('crm', 'OnAfterCrmContactAdd', Main::MODULE_ID, '\Crmgenesis\Bolvanka\customevent', 'workWithContact');
        EventManager::getInstance()->registerEventHandler('crm', 'OnAfterCrmContactUpdate', Main::MODULE_ID, '\Crmgenesis\Bolvanka\customevent', 'workWithContact');
        return true;
    }

    public function UnInstallEvents(){
        EventManager::getInstance()->unRegisterEventHandler('crm', 'OnAfterCrmContactAdd', Main::MODULE_ID, '\Crmgenesis\Bolvanka\customevent', 'workWithContact');
        EventManager::getInstance()->unRegisterEventHandler('crm', 'OnAfterCrmContactUpdate', Main::MODULE_ID, '\Crmgenesis\Bolvanka\customevent', 'workWithContact');
        return true;
    }

    public function InstallFiles($arParams = [])
    {
        CopyDirFiles(Main::GetPatch()."/install/bolvanka/", $_SERVER["DOCUMENT_ROOT"]."/bolvanka/", true, true);
        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx("/bolvanka/");

        return true;
    }

    public function DoInstall(){
        global $APPLICATION;
        if(Main::isVersionD7())
        {
            $this->InstallFiles();
            $this->InstallEvents();
            ModuleManager::registerModule(Main::MODULE_ID);
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage("CRM_GENESIS_INSTALL_ERROR_VERSION"));
        }

//        $APPLICATION->IncludeAdminFile(Loc::getMessage("CRM_GENESIS_INSTALL_TITLE"), Main::GetPatch()."/install/step.php");
    }

    public function DoUninstall(){
        ModuleManager::unRegisterModule(Main::MODULE_ID);
        $this->UnInstallEvents();
        $this->UnInstallFiles();
    }

}