<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

require_once ($_SERVER['DOCUMENT_ROOT'].'/local/lib/taskTime/ajax/class.php');

CModule::IncludeModule("CRM");
CModule::IncludeModule("tasks");




$obj = new BestClass();

//для заполнения поля с id сотрудника при создании записи учета времени
if($_POST['action'] == 'GiveMeCurUserID') $obj->getCurUserId();



//получение данных задачи, чтобы отображать кнопку или нет
if($_POST['action'] == 'GIVE_ME_CURRENT_TASK_DATA') $obj->getCurTaskData($_POST);

//получение данных при открытии окна для заполнения полей popup'a
if($_POST['action'] == 'GIVE_ME_DATA_FOR_TASK_TIME_POPUP') $obj->getNeededFieldsForPopup($_POST);

//Создание элемента учета времени
if($_POST['action'] == 'CREATE_NEW_TASK_TIME_ELEM') $obj->createNewTaskTimeElem($_POST['FIELDS']);