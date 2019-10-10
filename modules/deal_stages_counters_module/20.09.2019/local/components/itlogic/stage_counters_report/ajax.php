<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');


//CModule::IncludeModule("crm");

require_once 'class.php';

$obj = new DealCategoryStageCounters;

//1. запрос для селекта с категориями
if($_POST['action'] === 'GIVE_ME_CATEGORIES_FOR_SELECT') $obj->getCategoriesForSelect();

//2. Запрос инфы по фильтрам (направеление, дата начала, дата конца)
if($_POST['action'] === 'GIVE_ME_STATISTICS_BY_CATEGORY_ID') $obj->getStatisticsByFilter($_POST);

//3. запрос для селекта с ответственными
if($_POST['action'] === 'GIVE_ME_ASSIGNED_LIST_FOR_SELECT') $obj->getAssignedForSelect();

//4. запрос для селекта со стадиями
if($_POST['action'] === 'GIVE_ME_STAGES_LIST_FOR_SELECT') $obj->getStagesByCategoryId($_POST['category_id']);
