<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule("crm");

use \Bitrix\Crm\Category;

class DashBoard extends CBitrixComponent{

    public function testFunction(){
        return '<br>ЭТО МЕТОД КЛАССА DashBoard<br>';
    }
    
    //получение сделок специалиста по фильтру и указанным к выдаче полям
    public function getDealDataByFilter($arFilter,$arSelect){

       // $arFilter = Array('ASSIGNED_BY_ID' => $id, 'STAGE_ID' => array('WON',30), '<DATE_MODIFY' => date('d.m.Y')); // OLD
       // $arFilter = Array('ASSIGNED_BY_ID' => $id, 'STAGE_ID' => 'WON', ">=CLOSEDATE" => date('d.m.Y', strtotime('01.10.2018'))); //New test

        //$arSelect = Array('*','UF_*'); //23.10 at 17.19
        $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array()); //получение пользовательских полей сделки по ID
        while($ar_result = $db_list->GetNext()){
            $deals[] = $ar_result;
        }
            return $deals;
    }

    //получение id аналитиков, которые состоят в группе
    public function getUsersFromGroup($group_id){
        $filter = Array("GROUPS_ID"=>$group_id); // ID группы - 28 - аналитики, 29 - проггеры
        $rsUsers = CUser::GetList(($by="ID"), ($order="asc"), $filter);
        while($arItem = $rsUsers->GetNext())
        {
            //убираем пользователей с пустым именем и фамилией из выпадающего списка
            if($arItem['LAST_NAME'] == '' && $arItem['NAME'] == '' ) continue;
            $users[] = $arItem['ID'];
        }
        return $users;
    }

    //получение данных польвателя по id
    public function getUserDataByID($id){
        global $USER;
        $userDataMassive = CUser::GetByID($id);

        //ID Фото сотрудника находится в $userData['PERSONAL_PHOTO']
        return $userData = $userDataMassive->Fetch();
    }

    //получение фото по id
    public function getPhotoPath($photo_id){
       return $photoMassive = CFile::GetPath($photo_id);
    }

    //получение элементов списка (Property_ не отдает)
    public function getListElementsByFilter($arFilter,$arSelect,$arOrder = ''){
        $result = array();
        $resultList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($list = $resultList->Fetch()) {
            $result[] = $list;
        }
        return $result;
    }

    public function countWorkHoursInCurMonth(){
        $schetchik = 0;
        for($i = 1; $i<=date('t');$i++) {
            if(date('N', strtotime($i.'.'.date('m').'.'.date('Y'))) == 6 || date('N', strtotime($i.'.'.date('m').'.'.date('Y'))) == 7 ) continue;
            else $schetchik++;
        }
        return $schetchik * 8;
    }

    public function straightQueryNonMassive($month,$year){
        global $DB;
        // Works! //$str = "SELECT * FROM b_crm_widget_saletarget WHERE PERIOD_MONTH = 10";
       $str = "SELECT * FROM b_crm_widget_saletarget WHERE PERIOD_MONTH = ".$month." AND PERIOD_YEAR = ".$year;

        $res = $DB->Query($str);
        while ($row = $res->Fetch()) {
            $planData = $row;
        }
        return $planData;
    }

    public function getDealHistory($arFilter,$arSelect,$dealStageId){

        $acts_massive = array(30,'C2:4','C3:FINAL_INVOICE');
        $won_massive = array('WON','C2:WON','C3:WON');

        $db_list1 = CCrmEvent::GetList(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array());
        while ($ar_result1 = $db_list1->GetNext()) {
            /*if ($ar_result1['EVENT_TEXT_2'] == 'Акты') {
                $opendate = MakeTimeStamp($ar_result1['DATE_CREATE']);
            }*/


            if ($ar_result1['EVENT_TEXT_2'] == 'Акты' && in_array($dealStageId,$acts_massive)) {

                //$actes[] = strtotime($data['DATE_CREATE']);

                $actes[] = strtotime($ar_result1['DATE_CREATE']);
               // echo 'Стадия Акты: ';
                $result[] = $ar_result1['ID'];
            }

            if ($ar_result1['EVENT_TEXT_2'] == 'Сделка заключена' && in_array($dealStageId,$won_massive)) {

                //$actes[] = strtotime($data['DATE_CREATE']);

                $actes[] = strtotime($ar_result1['DATE_CREATE']);
               // echo 'Стадия Выигрыш: ';
                $result[] = $ar_result1['ID'];
            }

        }

        if(!empty($actes)){
            rsort($actes);
            if(date('n',$actes[0]) == date('n')) {
                //   echo 'Есть редактированные в этом месяце!';
               // $newDealsMassive = $arFilter1;
                return date('d.m.Y H:i:s',$actes[0]);
            }
            return false;

        }else{
            return false;
        }


    }

    //цвета для часов выроботки по сотрудникам
    public function getNeededStatisticColor($value){
        $color = '';
        switch (true){
            case $value < 50:
                $color = '#e00808';
                break;
            case (50 <= $value && $value < 100):
                $color = '#fd8d02';
                break;
            case (100 <= $value && $value < 150):
                $color = '#28b305';
                break;
            case (150 <= $value && $value < 200):
                $color = '#fd8d02';
                break;
            case 200 <= $value:
                $color = '#e00808';
                break;
        }
        return $color;
    }

    //цвета остатка/превышения часов по проектам
    public function getNeededProjectHoursColor($plan,$fact){
        $color = '';
        switch (true){
            case $plan < $fact:
                $color = '#e00808';
                break;
            case ((0 < $fact && $fact < $plan) || ($plan/4 <= $fact && $fact < $plan/2)):
                $color = '#c5c70b';
                break;
            case ($plan/2 <= $fact && $fact < $plan):
                $color = '#fd8d02';
                break;
            case ($fact == $plan):
                $color = '#28b305';
                break;
        }
        return $color;
    }

    public function getInvoicesByFilter($filter,$select){
        $massive = CCrmInvoice::GetList($arOrder = ["DATE_STATUS"=>"ASC"], $filter,false,false,$select,[]);
        while($ar_result = $massive->GetNext()){
            $invoices[] = $ar_result;
        }
        return $invoices;
    }

    //получение значения справочников
    public function getReferenceBook($filter){
        //array('ENTITY_ID' => 'CONTACT_TYPE', 'STATUS_ID' => $ID)

        $db_list = CCrmStatus::GetList([], $filter);
        $result = [];
        if ($ar_result = $db_list->GetNext()) $result = $ar_result;
        return $result;
    }

}