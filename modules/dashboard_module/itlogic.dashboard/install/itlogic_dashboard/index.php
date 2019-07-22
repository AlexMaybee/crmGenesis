<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
use \Bitrix\Main;
use \Bitrix\Main\Loader;
use Bitrix\Main\UserTable;

$APPLICATION->SetTitle("Dashboard проектов");
CJSCore::Init();


echo "<h1 class='test1'>Это страница dashboard'а</h1><br>";


echo '<br><br><hr><br>';

$APPLICATION->IncludeComponent(
    //"itlogic:dash.component",
    "itlogic:dash_invoices.component",
    ".default",
    Array(
    ),
    false
);