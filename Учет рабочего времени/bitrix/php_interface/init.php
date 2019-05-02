<?php
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Application;

define("HL_DEAL_PRODUCT_LINK", 3);

CJSCore::Init(array("jquery"));

/*function custom_mail($to,$subject,$body,$headers) {
    $f=fopen($_SERVER["DOCUMENT_ROOT"]."/maillog.txt", "a+");
    fwrite($f, print_r(array('TO' => $to, 'SUBJECT' => $subject, 'BODY' => $body, 'HEADERS' => $headers),1)."\n========\n");
    fclose($f);
    return mail($to,$subject,$body,$headers);
}*/

# init composer autoloader
require_once ($_SERVER["DOCUMENT_ROOT"] . "/local/vendor/autoload.php");

require_once ($_SERVER["DOCUMENT_ROOT"] . "/local/lib/helper/Helper.php");

##########################################################################################################
// sms api
include ('include/smsc_api.php');
##########################################################################################################

include('include/custom_uf.php');


AddEventHandler("main", "OnEpilog", "initData");
/**
 * подключаем после пролога нужные нам стили и скрипты
 */
function initData() {
    $asset = Asset::getInstance();
    $asset->addCss(SITE_TEMPLATE_PATH . "/css/interface.css");
}


//AddEventHandler("tasks", "OnBeforeTaskUpdate", Array("ActivityTask", "OnBeforeTaskUpdateHandler"));
AddEventHandler("tasks", "OnBeforeTaskAdd", Array("ActivityTask", "preventUserAddTask"));
class ActivityTask{

    function OnBeforeTaskUpdateHandler($id, &$arFields, &$arTaskCopy){
        if(isset($arFields['DEADLINE'])){
            $dealID = $arTaskCopy['UF_CRM_TASK'][0];
            $date = $arFields['DEADLINE'];
            $ar[] = $dealID;
            $ar[] = $date;
        }
    }

    function preventUserAddTask ($id, &$arFields) {
        if ($arFields['CREATED_BY'] === 594) {
            $arFields = [];
        }
    }
}

AddEventHandler("timeman", "OnBeforeTMReportDailyAdd", Array("BeforeDayClose", "OnBeforeDayClose"));
class BeforeDayClose{
    function OnBeforeDayClose(&$arFields){
        CModule::IncludeModule('iblock');
        CModule::IncludeModule('timeman');
        ShowMessage("Ошибка! Вы забыли заполнить обязательные поля!");
    }
}

AddEventHandler("timeman", "OnBeforeTMReportDailyUpdate", Array("BeforeDayClose2", "OnBeforeDayClose2"));
class BeforeDayClose2{
    function OnBeforeDayClose2(&$arFields){
        CModule::IncludeModule('iblock');
        CModule::IncludeModule('timeman');
        ShowMessage("Ошибка! Вы забыли заполнить обязательные поля!");
    }
}

//AddEventHandler("crm", "OnBeforeCrmInvoiceUpdate", Array("SetTask", "depandOnStageID"));
/*class SetTask {
	function depandOnStageID(&$arFields){
		CModule::IncludeModule('crm');
        CModule::IncludeModule('tasks');

		$CTask = new CTasks();
		$data = print_r($arFields, true);
		$stagesIDsForSettingTask = [2, 4, 6];
		$dealID = $arFields['UF_DEAL_ID'];
		$invoiceID = $arFields['ID'];
		$dealInfo = CCrmDeal::GetByID($dealID);
		$invoiceInfo = CCrmInvoice::GetByID($invoiceID);
	}
	function checkForWeekend($date){
		if (date("N", strtotime($date)) == 6)
			return date("d.m.Y H:i:s", strtotime($date)+86400*2);
		else if(date("N", strtotime($date)) == 7)
			return date("d.m.Y H:i:s", strtotime($date)+86400);
		else return $date;
	}

}*/

/*
AddEventHandler("crm", "OnAfterCrmDealUpdate", "printInfo");
function printInfo(&$arFields) {
    //AddMessage2Log($arFields);
}*/

// записываем диапазон часов в свою HL таблицу
/*AddEventHandler("crm", "OnAfterCrmDealProductRowsSave", "updateHLProductRows");
function updateHLProductRows($ID, $arFields) {
    $request = Application::getInstance()->getContext()->getRequest()->toArray();
    AddMessage2Log($request);
    require_once $_SERVER["DOCUMENT_ROOT"] . "/local/lib/helper/Helper.php";

    $helper = new Itlogic\Help\DealHelper(HL_DEAL_PRODUCT_LINK);

    $res = $GLOBALS["DB"]->Query("SELECT * FROM `b_crm_product_row` WHERE `OWNER_ID` = {$ID} ORDER BY `ID` ASC;", false);

    $fixData = [];
    while ( $ob = $res->Fetch() ) {
        $fixData[] = $ob;
    }

    $helper->checkData($request, $ID, $fixData);// структурируем данные для записи в HL таблицу
    $helper->addDealProducts();// добавляем данные в HL таблицу
    $helper->clearFix($ID, $fixData);// удаляем лишние записи из HL таблицы
}*/

function getBaseEntityEnum($ENTITY_ID = false){ // DEAL_STAGE - статус сделки

    if($ENTITY_ID){

        $res = CCrmStatus::GetList(['SORT' => 'ASC'],['ENTITY_ID' => $ENTITY_ID]);

        while($r = $res->Fetch()){
            $result[$r['STATUS_ID']] = $r['NAME'];
        }

        return $result;
    }else{

        $res = CCrmStatus::GetList(['SORT' => 'ASC'],[]);

        while($r = $res->Fetch()){
            $result[] = $r;
        }

        return $result;
    }
}

function getUserfieldEntityEnum($UF_CRM, $CRM_ENTITY = false){ // $UF_CRM = "UF_CRM_1440245930" // $CRM_ENTITY = "CRM_LEAD"/"CRM_DEAL"

    global $USER_FIELD_MANAGER;

    $id = $USER_FIELD_MANAGER->GetUserFields($CRM_ENTITY)[$UF_CRM]['ID'];
    if($id){

        $res = CUserFieldEnum::GetList(array(), array('USER_FIELD_ID' => $id));

        while($r = $res->Fetch()){
            $result[$r['ID']] = $r['VALUE'];
        }
    }
    return ($result) ? $result : false;
}

/** Выводит требуемую информацию для админов
 * @param $mas
 */
function pre($mas) {
    if ( $GLOBALS["USER"]->IsAdmin() ) {
        echo "<pre>";
        print_r($mas);
        echo "</pre>";
    }
}

$APPLICATION->SetAdditionalCSS('/ajax/style.css');

/*для учета затрат*/

AddEventHandler("main", "OnEpilog", "changeMyContent");

function changeMyContent()
{
    $req_str = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $pattern = '/\/services\/lists\/124\/element\/0\/0\//';


    if(preg_match($pattern,$req_str)) {

        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS("/local/lib/taskTime/css/style.css");
        //   $APPLICATION->AddHeadScript('//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js');
        //$APPLICATION->AddHeadScript('//s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js');
        $APPLICATION->AddHeadScript('/local/lib/taskTime/js/script.js');

        $time = date('d.m.Y H:i:s', strtotime('now'));
       /* $text = array('IF PAge is right','YEYEYEYEYE', $time);
        $file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger.log';
        file_put_contents($file, print_r($text,true), FILE_APPEND | LOCK_EX);
       */
    }
}

//******************************************
//12.07 Действие = обновление элемента инфоблока; Получаем по ID сделки все часы по ней из инфоблока ID=124
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "getIBdata"); //Обновление элемента
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "getIBdata"); //Создание элемента
//AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "getIBdata"); //Удаление элемента
function getIBdata(&$arFields){

    //получаем ID сделки из полученных данных $arFields['PROPERTY_VALUES']['586']['39231']['VALUE']
    foreach ($arFields['PROPERTY_VALUES']['586'] as $value){
        $deal_id = $value;
    }

    $res = getListDataByID($deal_id); //$arFields['PROPERTY_VALUES']['586']['39074']['VALUE'] // deal ID

    /*рассчет значений*/
    $fields = array(
        'HOURS_PROGR' => 0,
        'PROGERS_ID' => array(),
        'HOURS_ANALIT' => 0,
        'ANALITICS_ID' => array(),
        'HOURS_ELSE' => 0,
        'EVALUATION_ID' => array(),
        'TOTAL_PROJECT_HOURS' => 0,
    );

    foreach ($res as $k => $field){

        if($field['ROLE'] == 'Программист'){
            $fields['HOURS_PROGR'] += $field['HOURS'];//считаем кол-во часов прогеров
            array_push($fields['PROGERS_ID'],$field['EMPLOYEE_ID']); //массив Id прогеров
        }
        if($field['ROLE'] == 'Аналитик'){
            $fields['HOURS_ANALIT'] += $field['HOURS'];//считаем кол-во часов аналитиков
            array_push($fields['ANALITICS_ID'],$field['EMPLOYEE_ID']); //массив Id прогеров
        }
        if($field['ROLE'] == 'Оценка'){
            $fields['HOURS_ELSE'] += $field['HOURS'];//считаем кол-во часов прочих(оценка) UF_CRM_1529754546
            array_push($fields['EVALUATION_ID'],$field['EMPLOYEE_ID']); //массив Id прогеров
        }
        $fields['TOTAL_PROJECT_HOURS'] += $field['HOURS'];
        $fields['DEAL_ID'] = $field['DEAL_ID'];
    }

    $dealData = getDealDataByID($fields['DEAL_ID']);

   //кол-во дней просрочки, расчет
   // $daysPlannedForProject = $dealData['UF_CRM_1529753439'] - $dealData['UF_CRM_1529753369'] + 1; //дней на проект (финиш - старт) (с учетом текущего дня)
   // $daysGoneFromStart = date('d.m.Y', strtotime('now')) - $dealData['UF_CRM_1529753369'] + 1; // прошло дней с момента старта (с учетом текущего дня)

    $daysPlannedForProject = exceptWeekends($dealData['UF_CRM_1529753369'],$dealData['UF_CRM_1529753439']); //дней на проект (финиш - старт) за вычетом сб и вскр.
    $daysGoneFromStart = exceptWeekends($dealData['UF_CRM_1529753369'],date('d.m.Y', strtotime('now'))); // дней с момента старта (с учетом текущего дня) за вычетом сб и вскр.
    $daysPastDue = $daysGoneFromStart - $daysPlannedForProject; //дней просрочки
    if($daysPastDue <0) $daysPastDue = 0;


    //затраты, расчет
    $expenses = -1 * ($fields['HOURS_ELSE'] * $dealData['UF_CRM_1531814371410'] + ($fields['HOURS_PROGR'] * $dealData['UF_CRM_1531814346032'] + $fields['HOURS_ANALIT'] * $dealData['UF_CRM_1531814359596']) * 2 + $daysPastDue * $dealData['UF_CRM_1531831344413']);

    //доходность текущая
    $incomeCurrent =  $dealData['UF_CRM_1531814395288'] * 0.95 + $expenses;
    //доходность потенциальная
    $incomePotential = ($dealData['UF_CRM_1531814395288'] + $dealData['UF_CRM_1531814407691']) * 0.95 + $expenses;

    $crm_fields = array(
        'UF_CRM_1529755353' =>  $fields['HOURS_PROGR'],
        'UF_CRM_1529753275' => $fields['PROGERS_ID'],
        'UF_CRM_1529754702' => $fields['HOURS_ANALIT'],
        'UF_CRM_1529753120' => $fields['ANALITICS_ID'],
        'UF_CRM_1529755333' => $fields['HOURS_ELSE'],
        'UF_CRM_1531814668' => $fields['EVALUATION_ID'],
        'UF_CRM_1531814429747' => $incomeCurrent, //доходность текущая - рассчет
        'UF_CRM_1531814443487' => $incomePotential, //доходность потенциальная - рассчет
    );
    updateFieldsWithData($fields['DEAL_ID'],$crm_fields); //он тупо не хочет брать $deal_id, видимо из-за того, что после уже был запущен новый цикл

    $file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger2.log';
   /* file_put_contents($file, print_r($fields,true), FILE_APPEND | LOCK_EX);
    file_put_contents($file, print_r($crm_fields,true), FILE_APPEND | LOCK_EX);*/
    //file_put_contents($file, $daysPlannedForProject, FILE_APPEND | LOCK_EX);
    //file_put_contents($file, $daysGoneFromStart1, FILE_APPEND | LOCK_EX);
   // file_put_contents($file, $incomePotential, FILE_APPEND | LOCK_EX);
}

//получаем данные списка часов + ид + др.
function getListDataByID($id){

    $IBLOCK_ID = 124; //Iblock ID из админки
    $arSelect = Array('ID','NAME','PROPERTY_586','PROPERTY_587', 'PROPERTY_588'); // PROPERTY_586 - это ID сделки, PROPERTY_587 -
    $arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, 'PROPERTY_586_VALUE' => $id); //фильтр по ID блока и ID товара
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFieldsRes = $ob->GetFields();

        //Здесь же получать цену часа и умножать по каждому сотруднику на часы.
        $data[] = array("ID" => $arFieldsRes['ID'], "HOURS" => $arFieldsRes['NAME'], 'ROLE' => $arFieldsRes['PROPERTY_587_VALUE'], 'EMPLOYEE_ID' => $arFieldsRes['PROPERTY_588_VALUE'],'DEAL_ID' => $arFieldsRes['PROPERTY_586_VALUE']);
      //  $data = array('ID' => $arFields['ID'], 'NAME' => $arFields['NAME'],'SIZE' => $arFields['PROPERTY_116_VALUE']);
    }
    return $data;
}

//добавляем данные в поля сделки
function updateFieldsWithData($id,$fields){
    $entity = new CCrmDeal(false);//true - проверять права на доступ
    $entity->update($id, $fields);

   /* $file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger2.log';
    file_put_contents($file, print_r($fields,true), FILE_APPEND | LOCK_EX);
   file_put_contents($file, $id, FILE_APPEND | LOCK_EX);*/
}

//получаем нужные для просчета поля сделки по ID
function getDealDataByID($deal_ID){

    $arFilter = Array('ID' => $deal_ID);

    // UF_CRM_1531814346032 - Ставка прогера; UF_CRM_1531814359596 - ставка аналитика; UF_CRM_1531814371410 - ставка оценки, UF_CRM_1531814395288 - оплаченная сумма,
    // UF_CRM_1531814407691 - НЕ оплаченная сумма, UF_CRM_1529753369 - дата старта, UF_CRM_1529753439 - дата окончания;
    // UF_CRM_1531814359596 - ставка аналитика, UF_CRM_1531814346032 - ставка прогера, UF_CRM_1531814371410 - ставка по оценке, UF_CRM_1531831344413 - ставка просрочки
    $arSelect = Array('ID','UF_CRM_1531814346032','UF_CRM_1531814359596','UF_CRM_1531814371410','UF_CRM_1531814395288','UF_CRM_1531814407691',
        'UF_CRM_1529753369','UF_CRM_1529753439','UF_CRM_1531814359596','UF_CRM_1531814346032','UF_CRM_1531814371410', 'UF_CRM_1531831344413');
    $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array()); //получение пользовательских полей сделки по ID
    if($ar_result = $db_list->GetNext()) {

      /*  $file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger2.log';
        file_put_contents($file, print_r($ar_result,true), FILE_APPEND | LOCK_EX);*/

        return $ar_result;
    }

}

//вычитаем из промежутка старта-финиша субботы и вскр.
function exceptWeekends($start,$finish){
  /*  $file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger2.log';
    file_put_contents($file, $finish, FILE_APPEND | LOCK_EX);*/

    $start = strtotime($start);
    $finish = strtotime($finish);
    $count = 0;
    for($i = $start; $i <= $finish;){ //текущий день включительно, если что убрать =
        if(date('w', $i) != 0 && date('w', $i) != 6) $count = $count + 1;
        $i = $i + (3600 * 24);
    }
    return $count;
}