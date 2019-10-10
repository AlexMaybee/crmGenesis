<?php
class crmgenesis_worktimecontrol extends CModule
{

    var $MODULE_ID = "crmgenesis.worktimecontrol";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME = 'CRM GENESIS';
    var $PARTNER_URI = 'https://crmgenesis.com/';

    public function crmgenesis_worktimecontrol(){
        $arModuleVersion = [];

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = 'Модуль контроля учета рабочего времени';
        $this->MODULE_DESCRIPTION = "После установки сотрудник не сможет завершить рабочий день, пока не внесет 5 часов учета + в панели рабочего времени
        появится кнопка списания времени + попап";
    }

    public function InstallFiles(){

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/js",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/css",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/", true, true);
        return true;
    }

    public function UnInstallFiles(){

        DeleteDirFilesEx("/bitrix/js/crmgenesis/workPanelControl");
        DeleteDirFilesEx("/bitrix/css/crmgenesis/workPanelControl");

        //удаление папки itlogic из компонентов, если в ней пусто после удаления своего компонента
        if(!glob($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/crmgenesis/*')) DeleteDirFilesEx("/bitrix/js/crmgenesis");
        if(!glob($_SERVER['DOCUMENT_ROOT'].'/bitrix/css/crmgenesis/*')) DeleteDirFilesEx("/bitrix/css/crmgenesis");

        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION;
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);

        //привязка js-файла при загрузке страницы
        RegisterModuleDependences("main", "OnBeforeProlog", $this->MODULE_ID, "WorkTimeControl", "addCustomControlToPanel");

        //привязка функции на событие создания сделки для компании
//        RegisterModuleDependences("crm", "OnBeforeCrmDealAdd", $this->MODULE_ID, "Numerator", "createNewDealCodeForClient");

    }

    public function DoUninstall()
    {
        global $APPLICATION;
        $this->UnInstallFiles();

        //отвязка функции от события создания компании
        UnRegisterModuleDependences("main", "OnBeforeProlog", $this->MODULE_ID, "WorkTimeControl", "addCustomControlToPanel");

        //отвязка функции от события создания сделки
//        UnRegisterModuleDependences("crm", "OnBeforeCrmDealAdd", $this->MODULE_ID, "Numerator", "createNewDealCodeForClient");

        UnRegisterModule($this->MODULE_ID);

    }


}