<?php

Class itlogic_dashboard extends CModule
{
    var $MODULE_ID = "itlogic.dashboard";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function itlogic_dashboard()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = "itlogic.dashboard – доска со статистикой и прогрессбарами для отображения картины по выполнению плана и учета рабочего времени";
        //$this->MODULE_DESCRIPTION = "После установки вы сможете пользоваться компонентом dv:date.current";
        $this->MODULE_DESCRIPTION = "После установки вы сможете пользоваться компонентом itlogic:dash.component и получите эксклюзивный доступ к мега-супер-пупер странице, которая это покажет!";
    }

    function InstallFiles()
    {
        //созд папки если нет такой
        $dir = $_SERVER["DOCUMENT_ROOT"]."/local/components";
        if ( !file_exists ( $dir ) ) {
            mkdir ( $dir );
        }

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/components",
            $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/itlogic_dashboard",  $_SERVER["DOCUMENT_ROOT"]."/itlogic_dashboard/", true, true);


        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/local/components/itlogic/dash.component");
        DeleteDirFilesEx("/local/components/itlogic/dash_invoices.component");

        //удаление папки itlogic из компонентов, если в ней пусто после удаления своего компонента
        if(!glob($_SERVER['DOCUMENT_ROOT'].'/local/components/itlogic/*')) DeleteDirFilesEx("/local/components/itlogic");

        //удаление папки со страницей и пунктом левого меню
        DeleteDirFilesEx("/itlogic_dashboard/");

        return true;
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Установка модуля ".$this->MODULE_ID, $DOCUMENT_ROOT."/local/modules/".$this->MODULE_ID."/install/step.php");
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля ".$this->MODULE_ID, $DOCUMENT_ROOT."/local/modules/".$this->MODULE_ID."/install/unstep.php");
    }
}