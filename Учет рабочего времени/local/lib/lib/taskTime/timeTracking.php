<?php

/*для учета затрат*/

//Заполнение полей Дата, id сотрудника при создании элемента юлока 124 (ует времени) из сделки
AddEventHandler("main", "OnEpilog", "changeMyContent");

function changeMyContent()
{
    $req_str = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $pattern = '/\/services\/lists\/124\/element\/0\/0\//';


    if(preg_match($pattern,$req_str)) {

        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS("/local/lib/taskTime/css/style.css");

        $APPLICATION->AddHeadScript('/local/lib/taskTime/js/script.js');

        /*
         $file = $_SERVER['DOCUMENT_ROOT'].'/local/lib/taskTime/testLogger.log';
         file_put_contents($file, print_r($text,true), FILE_APPEND | LOCK_EX);
        */
    }
}

//******************************************
//12.07 Действие = обновление элемента инфоблока; Получаем по ID сделки все часы по ней из инфоблока ID=124
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "getIBdata"); //Обновление элемента блока 124
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "getIBdata"); //Создание элемента блока 124
AddEventHandler("iblock", "OnBeforeIBlockElemenUpdate", "delCommaFromHours"); //Замена запятой в числе на точку - НЕ ПАШЕТ!!! ЗАменить на Афтер!!!
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "delCommaFromHours"); //Замена запятой в числе на точку
AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "Del124Element"); //Удаление элемента


//удаление элемента, лог
function Del124Element($id){

    if($id > 0){

        $arFilter = Array("IBLOCK_ID"=>124, 'ID' => $id); //фильтр по ID блока и ID товара
        $arSelect = Array('ID','IBLOCK_ID','NAME','PROPERTY_586','PROPERTY_587', 'PROPERTY_588'); // PROPERTY_586 - это ID сделки, PROPERTY_587 - Роль, PROPERTY_588 - ID сотрудника

        $elemDatasMassive = getElementDataByFilter($arFilter,$arSelect);

        $elemData = $elemDatasMassive[0];

        //далее по id сделки получаем все элементы и заново пересчитываем все часы и время
        if($elemData && $elemData['IBLOCK_ID'] == 124){

            //Поиск всех элементов списка 124 для перерасчета часов
            $reArFilter = Array("IBLOCK_ID"=>124, 'PROPERTY_586' => $elemData['PROPERTY_586_VALUE']); //фильтр по ID сделки и блока 124
            $reArSelect = Array('ID','NAME','PROPERTY_586','PROPERTY_587', 'PROPERTY_588'); // PROPERTY_586 - это ID сделки, PROPERTY_587 - Роль, PROPERTY_588 - ID сотрудника
            $reElemData = getElementDataByFilter($reArFilter,$reArSelect);

            if($reElemData){

                //Запрос данных сделки для проверки ID аналитиков, проггеров и оценщиков в ее полях-массивах
                $dealFilter = array('ID' => $elemData['PROPERTY_586_VALUE']);
                $dealSelect = array('ID','TITLE','UF_CRM_1529753120', 'UF_CRM_1529753275', 'UF_CRM_1531814668'); //аналитики проекта + проггеры + оценщики
                $dealData = getDealDataByFilter($dealFilter,$dealSelect);

                //если получены данные сделки, то в цикле заполняем поля для последующего обновления этой сделки
                if($dealData){

                    $newDealFields = array(
                        'HOURS_PROGR' => 0,
                        'PROGERS_ID' => array(),
                        'HOURS_ANALIT' => 0,
                        'ANALITICS_ID' => array(),
                        'HOURS_ELSE' => 0,
                        'EVALUATION_ID' => array(),
                        'TOTAL_PROJECT_HOURS' => 0,
                    );

                    foreach ($reElemData as $reElem){
                        if($reElem['ID'] == $elemData['ID']) continue; //Исключаем из подсчета текущий элемент и его часы
                        else{

                            if ($reElem['PROPERTY_587_VALUE'] == 'Аналитик') {
                                $newDealFields['HOURS_ANALIT'] += $reElem['NAME'];//считаем кол-во часов аналитиков

                                if(!in_array($reElem['PROPERTY_588_VALUE'],$dealData['UF_CRM_1529753120']))
                                    array_push($newDealFields['ANALITICS_ID'], $reElem['PROPERTY_588_VALUE']); //массив Id прогеров
                                else $newDealFields['PROGERS_ID'] = $dealData['UF_CRM_1529753120'];
                            }
                            if ($reElem['PROPERTY_587_VALUE'] == 'Программист') {
                                $newDealFields['HOURS_PROGR'] += $reElem['NAME'];//считаем кол-во часов прогеров

                                if(!in_array($reElem['PROPERTY_588_VALUE'],$dealData['UF_CRM_1529753275']))
                                    array_push($newDealFields['PROGERS_ID'], $reElem['PROPERTY_588_VALUE']); //массив Id прогеров
                                else $newDealFields['PROGERS_ID'] = $dealData['UF_CRM_1529753275'];
                            }
                            if ($reElem['PROPERTY_587_VALUE'] == 'Оценка') {
                                $newDealFields['HOURS_ELSE'] += $reElem['NAME'];//считаем кол-во часов прочих(оценка) UF_CRM_1529754546

                                if(!in_array($reElem['PROPERTY_588_VALUE'],$dealData['UF_CRM_1531814668']))
                                    array_push($newDealFields['EVALUATION_ID'], $reElem['PROPERTY_588_VALUE']); //массив Id прогеров
                                else $newDealFields['PROGERS_ID'] = $dealData['UF_CRM_1531814668'];
                            }
                            $newDealFields['TOTAL_PROJECT_HOURS'] += $reElem['NAME'];
                            $newDealFields['DEAL_ID'] = $reElem['PROPERTY_586_VALUE'];

                        }
                    }

                    $crm_fields = array(
                        'UF_CRM_1529755353' => $newDealFields['HOURS_PROGR'],
                        'UF_CRM_1529753275' => $newDealFields['PROGERS_ID'],
                        'UF_CRM_1529754702' => $newDealFields['HOURS_ANALIT'],
                        'UF_CRM_1529753120' => $newDealFields['ANALITICS_ID'],
                        'UF_CRM_1529755333' => $newDealFields['HOURS_ELSE'],
                        'UF_CRM_1531814668' => $newDealFields['EVALUATION_ID'],
                      //  'UF_CRM_1531814429747' => $incomeCurrent, //доходность текущая - рассчет
                       // 'UF_CRM_1531814443487' => $incomePotential, //доходность потенциальная - рассчет
                    );

                    //Обновление полей сделки - массивов проггеров, аналитиков, оценщиков; часов по факту проггеров, аналитиков, оценщиков
                    $dealUpdRes = updateFieldsWithData($elemData['PROPERTY_586_VALUE'], $crm_fields);

                }
            }
        }
    }

    //$file = $_SERVER['DOCUMENT_ROOT'] . '/local/lib/taskTime/DelElemBefore.log';
    //file_put_contents($file, print_r(array($id,$elemData,$reElemData,$newDealFields,$dealUpdRes),true), FILE_APPEND | LOCK_EX);

}

//Заменяет в поле часов запятые на точки, т.к. с запятыми идет некорректный подсчет времени в полях сделки
function delCommaFromHours(&$arFields){

    if($arFields['IBLOCK_ID'] == 124){
        $arFields['NAME'] = str_replace(',','.',$arFields['NAME']);
       // $arFields['SEARCHABLE_CONTENT'] = str_replace(',','.',$arFields['SEARCHABLE_CONTENT']);
        // $file = $_SERVER['DOCUMENT_ROOT'] . '/local/lib/taskTime/TimeLoggerBefore.log';
       //  file_put_contents($file, print_r($arFields,true), FILE_APPEND | LOCK_EX);
    }

}

function getIBdata(&$arFields){

    if($arFields['IBLOCK_ID'] == 124) {
        //получаем ID сделки из полученных данных $arFields['PROPERTY_VALUES']['586']['39231']['VALUE']
        foreach ($arFields['PROPERTY_VALUES']['586'] as $value) {
            $deal_id = $value['VALUE'];
        }
        //если есть ид сделки, то продолжаем
        if(!$deal_id){
            return false;
        }

        $res = getListDataByID($deal_id); //$arFields['PROPERTY_VALUES']['586']['39074']['VALUE'] // deal ID

        if(!$res) return false;
        else{


            $dealData = getDealDataByID($deal_id);

            if($dealData){

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

                foreach ($res as $k => $field) {

                    //вставляем id пользователя только если его там не было ранее

                    if ($field['ROLE'] == 'Аналитик') {
                        $fields['HOURS_ANALIT'] += $field['HOURS'];//считаем кол-во часов аналитиков

                        if(!in_array($field['EMPLOYEE_ID'],$dealData['UF_CRM_1529753120']))
                            array_push($fields['ANALITICS_ID'], $field['EMPLOYEE_ID']); //массив Id прогеров
                        else $fields['PROGERS_ID'] = $dealData['UF_CRM_1529753120'];
                    }
                    if ($field['ROLE'] == 'Программист') {
                        $fields['HOURS_PROGR'] += $field['HOURS'];//считаем кол-во часов прогеров

                        if(!in_array($field['EMPLOYEE_ID'],$dealData['UF_CRM_1529753275']))
                            array_push($fields['PROGERS_ID'], $field['EMPLOYEE_ID']); //массив Id прогеров
                        else $fields['PROGERS_ID'] = $dealData['UF_CRM_1529753275'];
                    }
                    if ($field['ROLE'] == 'Оценка') {
                        $fields['HOURS_ELSE'] += $field['HOURS'];//считаем кол-во часов прочих(оценка) UF_CRM_1529754546

                        if(!in_array($field['EMPLOYEE_ID'],$dealData['UF_CRM_1531814668']))
                            array_push($fields['EVALUATION_ID'], $field['EMPLOYEE_ID']); //массив Id прогеров
                        else $fields['PROGERS_ID'] = $dealData['UF_CRM_1531814668'];
                    }
                    $fields['TOTAL_PROJECT_HOURS'] += $field['HOURS'];
                    $fields['DEAL_ID'] = $field['DEAL_ID'];
                }



                //кол-во дней просрочки, расчет
                $daysPlannedForProject = $dealData['UF_CRM_1529753439'] - $dealData['UF_CRM_1529753369'] + 1; //дней на проект (финиш - старт) (с учетом текущего дня)
                $daysGoneFromStart = date('d.m.Y', strtotime('now')) - $dealData['UF_CRM_1529753369'] + 1; // прошло дней с момента старта (с учетом текущего дня)

                $daysPlannedForProject = exceptWeekends($dealData['UF_CRM_1529753369'], $dealData['UF_CRM_1529753439']); //дней на проект (финиш - старт) за вычетом сб и вскр.
                $daysGoneFromStart = exceptWeekends($dealData['UF_CRM_1529753369'], date('d.m.Y', strtotime('now'))); // дней с момента старта (с учетом текущего дня) за вычетом сб и вскр.
                $daysPastDue = $daysGoneFromStart - $daysPlannedForProject; //дней просрочки
                if ($daysPastDue < 0) $daysPastDue = 0;


                //затраты, расчет
                $expenses = '';
                $expenses = -1 * ($fields['HOURS_ELSE'] * $dealData['UF_CRM_1531814371410'] + ($fields['HOURS_PROGR'] * $dealData['UF_CRM_1531814346032'] + $fields['HOURS_ANALIT'] * $dealData['UF_CRM_1531814359596']) * 2 + $daysPastDue * $dealData['UF_CRM_1531831344413']);

                //доходность текущая
                $incomeCurrent = '';
                $incomeCurrent = $dealData['UF_CRM_1531814395288'] * 0.95 + $expenses;
                //доходность потенциальная
                $incomePotential = '';
                $incomePotential = ($dealData['UF_CRM_1531814395288'] + $dealData['UF_CRM_1531814407691']) * 0.95 + $expenses;


                $crm_fields = array(
                    'UF_CRM_1529755353' => $fields['HOURS_PROGR'],
                    'UF_CRM_1529753275' => $fields['PROGERS_ID'],
                    'UF_CRM_1529754702' => $fields['HOURS_ANALIT'],
                    'UF_CRM_1529753120' => $fields['ANALITICS_ID'],
                    'UF_CRM_1529755333' => $fields['HOURS_ELSE'],
                    'UF_CRM_1531814668' => $fields['EVALUATION_ID'],
                    'UF_CRM_1531814429747' => $incomeCurrent, //доходность текущая - рассчет
                    'UF_CRM_1531814443487' => $incomePotential, //доходность потенциальная - рассчет
                );

                $dealUpdRes = updateFieldsWithData($deal_id, $crm_fields); //он тупо не хочет брать $deal_id, видимо из-за того, что после уже был запущен новый цикл

            }

        }

        $file = $_SERVER['DOCUMENT_ROOT'] . '/local/lib/taskTime/TimeLogger1782.log';
      //  file_put_contents($file, print_r($arFields,true), FILE_APPEND | LOCK_EX);
      //   file_put_contents($file, print_r($res,true), FILE_APPEND | LOCK_EX);
      //  file_put_contents($file, print_r($dealData,true), FILE_APPEND | LOCK_EX);
       // file_put_contents($file, print_r($fields,true), FILE_APPEND | LOCK_EX);
        /* file_put_contents($file, print_r($crm_fields,true), FILE_APPEND | LOCK_EX);
         file_put_contents($file, print_r($dealUpdRes,true), FILE_APPEND | LOCK_EX);*/
    }
}

//получаем данные списка часов + ид + др.
function getListDataByID($id){

    $IBLOCK_ID = 124; //Iblock ID из админки
    $arSelect = Array('ID','NAME','PROPERTY_586','PROPERTY_587', 'PROPERTY_588'); // PROPERTY_586 - это ID сделки, PROPERTY_587 -
    $arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, 'PROPERTY_586_VALUE' => $id); //фильтр по ID блока и ID товара
    $res = CIBlockElement::GetList(Array('ID' => 'ASC'), $arFilter, false, false, $arSelect);
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
    $deal = new CCrmDeal;//true - проверять права на доступ
    $res = $deal->Update($id,$fields);

    /*$file = $_SERVER['DOCUMENT_ROOT'].'/local/TimeLogger2.log';
    file_put_contents($file, print_r($fields,true), FILE_APPEND | LOCK_EX);
   file_put_contents($file, $id, FILE_APPEND | LOCK_EX);*/

    return $res;
}

//получаем нужные для просчета поля сделки по ID
function getDealDataByID($deal_ID){

    $arFilter = Array('ID' => $deal_ID);

    // UF_CRM_1531814346032 - Ставка прогера; UF_CRM_1531814359596 - ставка аналитика; UF_CRM_1531814371410 - ставка оценки, UF_CRM_1531814395288 - оплаченная сумма,
    // UF_CRM_1531814407691 - НЕ оплаченная сумма, UF_CRM_1529753369 - дата старта, UF_CRM_1529753439 - дата окончания;
    // UF_CRM_1531814359596 - ставка аналитика, UF_CRM_1531814346032 - ставка прогера, UF_CRM_1531814371410 - ставка по оценке, UF_CRM_1531831344413 - ставка просрочки
    $arSelect = Array('ID','UF_CRM_1531814346032','UF_CRM_1531814359596','UF_CRM_1531814371410','UF_CRM_1531814395288','UF_CRM_1531814407691',
        'UF_CRM_1529753369','UF_CRM_1529753439','UF_CRM_1531814359596','UF_CRM_1531814346032','UF_CRM_1531814371410', 'UF_CRM_1531831344413',
        'UF_CRM_1529753120', 'UF_CRM_1529753275', 'UF_CRM_1531814668'); //аналитики проекта + проггеры + оценщики
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



/**********Документооборот***/
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "addCompanyIdOrContactIdToListElem");
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "updateCompanyIdOrContactIdToListElem");
function addCompanyIdOrContactIdToListElem(&$arFields){


    if($arFields['IBLOCK_ID'] == 125 && $arFields['PROPERTY_VALUES']['592']['n0']['VALUE'] == ''){

        //file_put_contents($file, $arFields['PROPERTY_VALUES']['593']['n0']['VALUE'], FILE_APPEND | LOCK_EX);

        //Берем данные сделки по id
        $dealData = CCrmDeal::GetByID($arFields['PROPERTY_VALUES']['593']['n0']['VALUE']);
        if($dealData['COMPANY_ID'] != '') {
            $arFields['PROPERTY_VALUES']['592']['n0']['VALUE'] = 'CO_'.$dealData['COMPANY_ID'];
        }
        if($dealData['COMPANY_ID'] == 0 && $dealData['CONTACT_ID'] != '') {
            $arFields['PROPERTY_VALUES']['592']['n0']['VALUE'] = 'C_'.$dealData['CONTACT_ID'];
        }

        /*$file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger3.log';
        file_put_contents($file, print_r($dealData,true), FILE_APPEND | LOCK_EX);*/

        return true;

    }

}

function updateCompanyIdOrContactIdToListElem(&$arFields){
    /*$file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger3.log';
    file_put_contents($file, print_r($arFields,true), FILE_APPEND | LOCK_EX);*/

    if($arFields['IBLOCK_ID'] == 125 /*&& $arFields['PROPERTY_VALUES']['592']['n0']['VALUE'] == ''*/){
        $arFields['PROPERTY_VALUES']['592']['n0']['VALUE'];
        //file_put_contents($file, $arFields['PROPERTY_VALUES']['593']['n0']['VALUE'], FILE_APPEND | LOCK_EX);

        foreach ($arFields['PROPERTY_VALUES']['593'] as $number => $dealId){
            $ID = $dealId['VALUE'];
            $key = $number;
        }

        //Берем данные сделки по id
        //$dealData = CCrmDeal::GetByID($arFields['PROPERTY_VALUES']['593']['43585']['VALUE']);
        $dealData = CCrmDeal::GetByID($ID);
        if($dealData['COMPANY_ID'] != '') {
            $arFields['PROPERTY_VALUES']['592'][$key]['VALUE'] = 'CO_'.$dealData['COMPANY_ID'];
        }
        if($dealData['COMPANY_ID'] == 0 && $dealData['CONTACT_ID'] != '') {
            $arFields['PROPERTY_VALUES']['592'][$key]['VALUE'] = 'C_'.$dealData['CONTACT_ID'];
        }

        /*  $file = $_SERVER['DOCUMENT_ROOT'].'/local/testLogger3.log';
         file_put_contents($file, print_r($arFields,true), FILE_APPEND | LOCK_EX);*/

        return true;

    }

}

function getElementDataByFilter($arFilter,$arSelect){

    $resultList = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, $arSelect);
    while ($list = $resultList->Fetch()) {
        $result[] = $list;
    }
    return $result;
}

function getDealDataByFilter($arFilter,$arSelect){
    //достаем данные по ID сделки

    $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array()); //получение пользовательских полей сделки по ID
    while ($dealsList = $db_list->Fetch()) {
        $result[] = $dealsList;
    }
    if($result) return $result;
    return false;

}

?>