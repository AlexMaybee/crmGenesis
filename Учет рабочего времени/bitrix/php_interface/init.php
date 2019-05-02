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




/*для учета затрат + документооборот*/

include_once ($_SERVER['DOCUMENT_ROOT'].'/local/lib/taskTime/timeTracking.php');

/*для учета затрат + документооборот*/
