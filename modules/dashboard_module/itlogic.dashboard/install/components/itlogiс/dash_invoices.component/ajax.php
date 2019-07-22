<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

//require_once $_SERVER['DOCUMENT_ROOT'].'/local/components/itlogic/vueTest.component/class.php';
require_once 'class.php';

if($_POST['ACTION'] == 'GIVE_ME_USERS_DATA'){
    $obj = new DashBoardClass;
    $obj->getAnaliticsUsersData($_POST['MONTH'],$_POST['YEAR']);
}