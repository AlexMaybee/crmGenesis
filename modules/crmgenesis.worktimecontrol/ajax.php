<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

require_once ('classes/class.php');

$obj = new WorkTimeAjax;

if($_POST['ACTION'] == 'COUNT_WORKED_HOURS_IN_CUURRENT_DAY') $obj->countWorkedHoursInCurrentDay($_POST);

if($_POST['ACTION'] == 'GIVE_ME_LIST_FIELDS_AND_VALUES') $obj->getListFieldsAndValues();

if($_POST['ACTION'] == 'GIVE_ME_DEALS_LIST_BY_TITLE') $obj->getDealsListByTitle($_POST);

if($_POST['ACTION'] == 'CREATE_NEW_TASK_TIME_ELEMENT') $obj->createNewtaksTimeElement($_POST);
