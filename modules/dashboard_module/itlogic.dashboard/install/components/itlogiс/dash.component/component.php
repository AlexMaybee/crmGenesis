<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$months = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
$arResult['NOW_DATE'] = $months[date('n') - 1].' '.date('Y');



$arResult['CUSTOM_DATE'] = 'Сегодня '.date('d.m.Y H:i:s',strtotime('now'));

//!!! Передача данных в шаблон осуществляется только через переменную $arResult

//!!! функции из класса компонента class.php, подключаемого автоматически, доступны через $this

//получение плана и ид аналитиков из виджета прямым запросом
$month = date('n');
$year = date('Y');



$data = $this->straightQueryNonMassive($month, $year); //!!!!!!!!передаем текущий месяц и год

if($data == '') $arResult['ERROR_EMTY_DATA'] = 'НЕ заполнен план на текущий месяц!';
else{



    //ищем первую скобку (в ней id и план каждого аналитика) и обрезаем все до нее - "а:3..."
    $pos = strpos ($data['TARGET_GOAL'],'{');
    $a = substr($data['TARGET_GOAL'],$pos);

    //получившуюся строку чистим от скобок ('{','}') в начале и в конце
    $m2 = rtrim(str_replace(array('{','}'),'',$a),';');
    //раскладываем строку на массив по ";"
    $m3 = explode(';',$m2);

    //ищем ":" и удаляем сначала сткроки и до ":" включительно
    foreach ($m3 as $string){
        $doubleDot = strpos($string,':');
        $lo = substr($string,$doubleDot+1);
        if(strpos($lo,':') > 0) $lo = str_replace('"','',array_pop(explode(':',$lo)));
        //собираем все в массив
        $array_analitics[] = $lo;
    }
    //Это нужный массвив данных, который ме делим по 2 = id аналитика + план на месяц
    //$arResult['test'] = array_chunk($array_analitics,2);
    $analitics_plan_array = array_chunk($array_analitics,2);


    //МАссив для данных по компании
    $arResult['COMPANY'] = array(
        'PLAN' => 0,
        'FACT' => 0,
        'INVOICES_PAYED_SUM' => 0,
        'INVOICES_PAYED_IN_PERCENT' => 0,
        'POTENCIAL_INVOICES_SUM' => 0,
        'POTENCIAL_INVOICES_IN_PERCENT' => 0,
    );

    //считаем оплаченные суммы по счетам в текущем месяце
    $companyInvoicesFilter = [
        'STATUS_ID' => 'P',
       // '>=DATE_STATUS' => date('d.m.Y H:i:s', strtotime('01.'.$month.'.'.$year)),//strtotime('01.'.date('m').'.'.date('Y'))),
        '>=PAY_VOUCHER_DATE' => date('d.m.Y H:i:s', strtotime('01.'.$month.'.'.$year)),//strtotime('01.'.date('m').'.'.date('Y'))),
    ];
    $allPayedInvoicesInThisMonth = $this->getInvoicesByFilter($companyInvoicesFilter,['ID','STATUS_ID','DATE_STATUS','RESPONSIBLE_ID','PRICE','UF_DEAL_ID']);

    foreach ($allPayedInvoicesInThisMonth as $key => $invoice) {

        //проверка направления сделки, если "лицензирование", то умножаем сумму на 0,5
        $curDealData = $this->getDealDataByFilter(['ID' => $invoice['UF_DEAL_ID']],['ID','CATEGORY_ID']);

        if($curDealData[0]['CATEGORY_ID'] == 2) $arResult['COMPANY']['INVOICES_PAYED_SUM'] += ($invoice['PRICE']/2);
        else $arResult['COMPANY']['INVOICES_PAYED_SUM'] += $invoice['PRICE'];
        $arResult['INVOICES_TEST_DEALS'][] = $curDealData[0];

    }
    $arResult['INVOICES_TEST'] = $allPayedInvoicesInThisMonth;



    $potencialInvoicesFilter = ['STATUS_ID' => ['N',1,2]];
    $allPotencialInvoices = $this->getInvoicesByFilter($potencialInvoicesFilter,['ID','STATUS_ID','DATE_STATUS','ORDER_TOPIC','RESPONSIBLE_ID','PRICE','UF_DEAL_ID'/*,'PAY_VOUCHER_DATE'*/]);

    //test 20.06.2019
   // $arResult['POTENCIAL_INVOICES_IN_DEALS'] = '';
    $arResult['POTENCIAL_INVOICES_DEALS'] = [];

    foreach ($allPotencialInvoices as $key => $potencInvoice){
        //проверка направления сделки, если "лицензирование", то умножаем сумму на 0,5
        $curDealData = $this->getDealDataByFilter(['ID' => $potencInvoice['UF_DEAL_ID']],['ID','CATEGORY_ID','TITLE','OPPORTUNITY','ASSIGNED_BY_ID']);


        if($curDealData){

            //$arResult['POTENCIAL_INVOICES_IN_DEALS'][$curDealData[0]['ID']] =[];
            $arResult['POTENCIAL_INVOICES_DEALS'][] = $curDealData[0];


            $allPotencialInvoices[$key]['DEAL_DATA'] = $curDealData[0];

         /*   $responsible_id_data = $this->getUserDataByID($potencInvoice['RESPONSIBLE_ID']);
            $allPotencialInvoices[$key]['RESPONSIBLE_NAME'] = $responsible_id_data['NAME'].' '.$responsible_id_data['LAST_NAME'];
            $allPotencialInvoices[$key]['RESPONSIBLE_IMG_PATH'] = $this->getPhotoPath($responsible_id_data['PERSONAL_PHOTO']);

            if($curDealData[0]['ASSIGNED_BY_ID'] != $potencInvoice['RESPONSIBLE_ID']){
                $assigned_by_id_data = $this->getUserDataByID($curDealData[0]['ASSIGNED_BY_ID']);
                $allPotencialInvoices[$key]['DEAL_DATA']['ASSIGNED_BY_NAME'] = $assigned_by_id_data['NAME'].' '.$assigned_by_id_data['LAST_NAME'];
                $allPotencialInvoices[$key]['DEAL_DATA']['ASSIGNED_BY_IMG_PATH'] = $this->getPhotoPath($assigned_by_id_data['PERSONAL_PHOTO']);
            }
            else{
                $allPotencialInvoices[$key]['DEAL_DATA']['ASSIGNED_BY_IMG_PATH'] = $this->getPhotoPath($responsible_id_data['PERSONAL_PHOTO']);
                $allPotencialInvoices[$key]['DEAL_DATA']['ASSIGNED_BY_NAME'] = $allPotencialInvoices[$key]['RESPONSIBLE_NAME'];
            }*/


            if($curDealData[0]['CATEGORY_ID'] == 2) {
                $arResult['COMPANY']['POTENCIAL_INVOICES_SUM'] += ($potencInvoice['PRICE']/2);

            }
            else $arResult['COMPANY']['POTENCIAL_INVOICES_SUM'] += $potencInvoice['PRICE'];
        }

    }

    //21.06.2019 рассчет оплат по сделкам и выставленным по ним счетам
    foreach ($arResult['POTENCIAL_INVOICES_DEALS'] as $key => $dealData){
        $assigned_by_id_data = $this->getUserDataByID($dealData['ASSIGNED_BY_ID']);
        $arResult['POTENCIAL_INVOICES_DEALS'][$key]['ASSIGNED_BY_NAME'] = $assigned_by_id_data['NAME'].' '.$assigned_by_id_data['LAST_NAME'];
        $arResult['POTENCIAL_INVOICES_DEALS'][$key]['ASSIGNED_BY_IMG_PATH'] = $this->getPhotoPath($assigned_by_id_data['PERSONAL_PHOTO']);

        $dealInvoices = $this->getInvoicesByFilter(['UF_DEAL_ID' => $dealData['ID']],['ID','STATUS_ID','DATE_STATUS','ORDER_TOPIC','RESPONSIBLE_ID','PRICE','UF_DEAL_ID','PAY_VOUCHER_DATE']);

        $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_PAYED'] = 0;
        $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_UNPAYED'] = 0;
        $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_WHOLE_SUM'] = 0;

        if($dealInvoices) {

            foreach ($dealInvoices as $index => $invoice){
                //фото и имя ответственного
                $responsible_id_data = $this->getUserDataByID($invoice['RESPONSIBLE_ID']);
                $dealInvoices[$index]['RESPONSIBLE_NAME'] = $responsible_id_data['NAME'].' '.$responsible_id_data['LAST_NAME'];
                $dealInvoices[$index]['RESPONSIBLE_IMG_PATH'] = $this->getPhotoPath($responsible_id_data['PERSONAL_PHOTO']);

                //расчет оплаченных и нет счетов
                if($invoice['STATUS_ID'] != 'D'){
                    $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_WHOLE_SUM'] += $invoice['PRICE'];
                    if($invoice['STATUS_ID'] == 'P') $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_PAYED'] += $invoice['PRICE'];
                    else $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_UNPAYED'] += $invoice['PRICE'];
                }


                //имя статуса счета
                $dealInvoices[$index]['STATUS_NAME'] = '';
                $refFilter = ['ENTITY_ID' => 'INVOICE_STATUS', 'STATUS_ID' => $invoice['STATUS_ID']];
                $refResult = $this->getReferenceBook($refFilter);
                if($refResult) $dealInvoices[$index]['STATUS_NAME'] = HTMLToTxt($refResult['NAME']); //перевод из HTML в текст
            }
            //выставлено счетов на суммы (кроме отклоненных) / сумму сделки
            $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_BILLED_PERCENT'] = round(($arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_WHOLE_SUM'] / $dealData['OPPORTUNITY']) * 100,2);

            //выставлено счетов на суммы (кроме отклоненных) / сумму сделки
            $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_PAYED_PERCENT'] = round(($arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES_PAYED'] / $dealData['OPPORTUNITY']) * 100,2);

            $arResult['POTENCIAL_INVOICES_DEALS'][$key]['INVOICES'] = $dealInvoices;
        }
    }


    $arResult['POTENCIAL_INVOICES'] = $allPotencialInvoices;



//получение плана и ид аналитиков из виджета прямым запросом



//получение массива интеграторов из группы - пока не надо
//$integrators = $this->getUsersFromGroup(28);

//передача в массив данных пользователя с колючем = его ID
    $arResult['INTEGRATORS'] = array();
    foreach ($analitics_plan_array as $integrator){

        //сохраняем id аналитика и его личный план в отдельные переменные
        $analitic_id = $integrator[0];
        $analitic_plan = $integrator[1];

        //получение данных пользователя по его id
        $integrator_data = $this->getUserDataByID($analitic_id);

        $arResult['INTEGRATORS'][$analitic_id] = array(
            'NAME' => $integrator_data['NAME'].' '.$integrator_data['LAST_NAME'],
            'POSITION' => $integrator_data['WORK_POSITION'],
            'PHOTO' => $this->getPhotoPath($integrator_data['PERSONAL_PHOTO']),
            'MONTH_PLAN' => $analitic_plan,
            'DEALS_SUM' => 0,
            'PLAN_COMPLETED' => 0,
            'PLAN_COMPLETED_BY_INVOICES' => 0,
            'PLAN_POTENCIAL_BY_INVOICES' => 0,
            'PLAN_COMPLETED_BY_INVOICES_PERCENT' => 0,
            'PLAN_POTENCIAL_BY_INVOICES_PERCENT' => 0,
            'HOURS_WORKED' => array(
                'VALUE' => 0,
                'COLOR' => 0,
            ),
            'DEALS' => array(),

        );

        //получение сделок пользователя по его id на стадии "WON" - СТАРОЕ!!!
        //дата сортировки с начала месяца
    /*    $arFilter_deals = array(
            'ASSIGNED_BY_ID' => $analitic_id,
            'STAGE_ID' => array('WON',30,'C2:4','C2:WON','C3:FINAL_INVOICE','C3:WON'), //стадии из 3-х направлений - Акты, отзывы, завершено (выграно)
            ">=CLOSEDATE" => date('d.m.Y', strtotime('01.'.date('m').'.'.date('Y'))),
        );
        $deal_data = $this->getDealDataByFilter($arFilter_deals,array('ID','TITLE','OPPORTUNITY','STAGE_ID'));

    */

        //29.01.2019 получение отработанных часов аналитиком за текущий месяц
        //Сортировка по дате Property_585 со спец. приемом
        $listHoursFilter = array(
            "IBLOCK_ID" => 124,
           // '>=DATE_CREATE' => date('d.m.Y H:i:s', strtotime('01.'.date('m').'.'.date('Y'))),
            '>=PROPERTY_585' => date('Y-m-d', strtotime('01.'.date('m').'.'.date('Y'))),
            'PROPERTY_588' => $analitic_id,
            'ACTIVE' => 'Y',
        );
        $listHoursSelect = array('ID','NAME','PROPERTY_588','PROPERTY_586'); //на всяк случай запрашиваю все поля списков
        $listHoursResult = $this->getListElementsByFilter($listHoursFilter,$listHoursSelect);
        foreach ($listHoursResult as $hoursWorked){
            $arResult['INTEGRATORS'][$analitic_id]['HOURS_WORKED']['VALUE'] += $hoursWorked['NAME'];
        }
        //цвет для значения
        $arResult['INTEGRATORS'][$analitic_id]['HOURS_WORKED']['COLOR'] = $this->getNeededStatisticColor($arResult['INTEGRATORS'][$analitic_id]['HOURS_WORKED']['VALUE']);

        //$arResult['INTEGRATORS'][$analitic_id]['HOURS_WORKED'] = $listHoursResult;


    //03.12 обновил фильтр получения сделок

        $arFilter_deals2 = array(
            'ASSIGNED_BY_ID' => $analitic_id,
            'STAGE_ID' => array('WON',30,'C2:4','C2:WON','C3:FINAL_INVOICE','C3:WON'), //стадии из 3-х направлений - Акты, отзывы, завершено (выграно)
            ">=CLOSEDATE" => date('d.m.Y', strtotime('01.'.date('m.Y',strtotime('now')))), //date('m.Y',strtotime('-1 month'))
        );

        $arResult['ALL_DEALS'] = $this->getDealDataByFilter($arFilter_deals2,array('ID','TITLE','STAGE_ID','OPPORTUNITY'));
        $arResult['ALL_DEALS_HISTORY'] = array();
          foreach ($arResult['ALL_DEALS'] as $key => $dealD){

              //проверка, чтобы полсдний переход на одну из искомых стадий был в этом месяце
             $arFilter1 = Array('ENTITY_ID' => $dealD['ID'],'ENTIYY_TYPE_ID' => 2, 'EVENT_TYPE' => 1,'ENTITY_FIELD' => 'STAGE_ID'); // 'EVENT_TEXT_2' => 'Акты' - не ищет!
             $arSelect1 = Array('ID','EVENT_TEXT_1','EVENT_TEXT_2','DATE_CREATE');
             if($this->getDealHistory($arFilter1,$arSelect1,$dealD['STAGE_ID']) != false) $arResult['ALL_DEALS_HISTORY'][$key]=$dealD;
                 //$arResult['ALL_DEALS_HISTORY'][$dealD['ID']] = $this->getDealHistory($arFilter1,$arSelect1,$dealD['STAGE_ID']);
             //$arResult['ALL_DEALS_HISTORY'][$dealD['ID']] = $dealD['TITLE'];

         }

    //03.12 обновил фильтр получения сделок


        if($arResult['ALL_DEALS_HISTORY'] != null) {

            foreach ($arResult['ALL_DEALS_HISTORY'] as $key => $dealData){

                //27.12.2018 По просьбе Эмиля - если направление = "лицензирование", то сумма прибыль = сумма прихода / 2 (для БУС он обещал сделать что-то отдельно, т.к. там коэфф. 0,45
                if(in_array($dealData['STAGE_ID'], array('C2:4','C2:WON'))){ //стадии направления "лицензирование" - акты и выигрыш
                    $dealData['OPPORTUNITY'] = $dealData['OPPORTUNITY'] / 2;
                }


                $arResult['INTEGRATORS'][$analitic_id]['DEALS_SUM'] += $dealData['OPPORTUNITY'];
                $arResult['INTEGRATORS'][$analitic_id]['DEALS'][] = $dealData;

                if($dealData['STAGE_ID'] == 'WON' || $dealData['STAGE_ID'] == 30) {
                    $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['VNEDRENIE']['NAME'] = 'Внедрение';
                    $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['VNEDRENIE']['SUM'] += $dealData['OPPORTUNITY'];
                }
                if($dealData['STAGE_ID'] == 'C2:WON' || $dealData['STAGE_ID'] == 'C2:4') {
                    $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['LICENCED']['NAME'] = 'Лицензирование';
                    $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['LICENCED']['SUM'] += $dealData['OPPORTUNITY'];
                }
                if($dealData['STAGE_ID'] == 'C3:FINAL_INVOICE' || $dealData['STAGE_ID'] == 'C3:WON') {
                    $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['ONLINE_COURSE']['NAME'] = 'Онлайн-курс';
                    $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['ONLINE_COURSE']['SUM'] += $dealData['OPPORTUNITY'];
                }
            }

            if($arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['VNEDRENIE']) {
                $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['VNEDRENIE']['PERCENT_TO_EXISTED_SUM'] = round($arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['VNEDRENIE']['SUM'] / $arResult['INTEGRATORS'][$analitic_id]['DEALS_SUM'] * 100,2);
            }
            if($arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['LICENCED']) {
                $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['LICENCED']['PERCENT_TO_EXISTED_SUM'] = round($arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['LICENCED']['SUM'] / $arResult['INTEGRATORS'][$analitic_id]['DEALS_SUM'] * 100,2);
            }
            if($arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['ONLINE_COURSE']) {
                $arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['ONLINE_COURSE']['PERCENT_TO_EXISTED_SUM'] = round($arResult['INTEGRATORS'][$analitic_id]['CATEGORIES']['ONLINE_COURSE']['SUM'] / $arResult['INTEGRATORS'][$analitic_id]['DEALS_SUM'] * 100,2);
            }

            //получаем сумму кажого аналитика по счетам
            foreach ($allPayedInvoicesInThisMonth as $invoice) {
                if($invoice['RESPONSIBLE_ID'] == $analitic_id) {
                    $curDealData = $this->getDealDataByFilter(['ID' => $invoice['UF_DEAL_ID']],['ID','CATEGORY_ID']);
                    if($curDealData[0]['CATEGORY_ID'] == 2)
                        $arResult['INTEGRATORS'][$analitic_id]['PLAN_COMPLETED_BY_INVOICES'] += ($invoice['PRICE']/2);
                    else $arResult['INTEGRATORS'][$analitic_id]['PLAN_COMPLETED_BY_INVOICES'] += $invoice['PRICE'];

                    //$arResult['INTEGRATORS'][$analitic_id]['PLAN_COMPLETED_BY_INVOICES'] += $invoice['PRICE'];
                }
            }
            $arResult['INTEGRATORS'][$analitic_id]['PLAN_COMPLETED_BY_INVOICES_PERCENT'] += round($arResult['INTEGRATORS'][$analitic_id]['PLAN_COMPLETED_BY_INVOICES'] / $arResult['INTEGRATORS'][$analitic_id]['MONTH_PLAN'] * 100,2);


            //потенциальные счета по каждому аналитику (выставленные, но не оплаченные - стадии 1,2
            foreach ($allPotencialInvoices as $potencInvoice){
                if($potencInvoice['RESPONSIBLE_ID'] == $analitic_id){

                    $curDealData = $this->getDealDataByFilter(['ID' => $potencInvoice['UF_DEAL_ID']],['ID','CATEGORY_ID']);
                    if($curDealData[0]['CATEGORY_ID'] == 2)
                        $arResult['INTEGRATORS'][$analitic_id]['PLAN_POTENCIAL_BY_INVOICES'] += ($potencInvoice['PRICE']/2);
                    else $arResult['INTEGRATORS'][$analitic_id]['PLAN_POTENCIAL_BY_INVOICES'] += $potencInvoice['PRICE'];

                  //  $arResult['INTEGRATORS'][$analitic_id]['PLAN_POTENCIAL_BY_INVOICES'] += $potencInvoice['PRICE'];
                }

            }
            $arResult['INTEGRATORS'][$analitic_id]['PLAN_POTENCIAL_BY_INVOICES_PERCENT'] += round($arResult['INTEGRATORS'][$analitic_id]['PLAN_POTENCIAL_BY_INVOICES'] / $arResult['INTEGRATORS'][$analitic_id]['MONTH_PLAN'] * 100,2);


            $arResult['INTEGRATORS'][$analitic_id]['PLAN_COMPLETED'] += round($arResult['INTEGRATORS'][$analitic_id]['DEALS_SUM'] / $arResult['INTEGRATORS'][$analitic_id]['MONTH_PLAN'] * 100,2);

            //подсчет плана компании и факта выполнения


        }
        $arResult['COMPANY']['PLAN'] += $arResult['INTEGRATORS'][$analitic_id]['MONTH_PLAN'];
        $arResult['COMPANY']['FACT'] += $arResult['INTEGRATORS'][$analitic_id]['DEALS_SUM'];
        $arResult['COMPANY']['COMPLETED'] = round($arResult['COMPANY']['FACT'] / $arResult['COMPANY']['PLAN'] * 100,2);
        $arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'] = round($arResult['COMPANY']['INVOICES_PAYED_SUM'] / $arResult['COMPANY']['PLAN'] * 100,2);
        $arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'] = round($arResult['COMPANY']['POTENCIAL_INVOICES_SUM'] / $arResult['COMPANY']['PLAN'] * 100,2);
    }

}

//получение проггеров

//Проггеры v2 Сделки -> эл. списков (проггеры теперь будут получаться из поля сделок "Проггеры" при помощи array_unique($dealData['UF_CRM_1529753275'],SORT_NUMERIC );


$arFilter_deals2 = array(
    //'STAGE_ID' => array(26,'C2:2'), //стадии "РЕАЛИЗАЦИЯ" из 2-х направлений - в онлайн-курсе ее нет!
    'STAGE_ID' => array(25,28,26,'C4:EXECUTING','C4:FINAL_INVOICE'), //стадии Договор-счёт, Первичная оплата,Реализация, Тестирование - отладка
    '!UF_CRM_1529753275' => '', //берем только те сделки, где есть хоть один проггер
);

//$arResult['PROGGERS_V2']['ALL_DEALS'] = $this->getDealDataByFilter($arFilter_deals2,array('TITLE','ASSIGNED_BY_ID','UF_CRM_1529755279','UF_CRM_1529755353','UF_CRM_1529753275'));
$all_deals = $this->getDealDataByFilter($arFilter_deals2,array('TITLE','ASSIGNED_BY_ID','UF_CRM_1529755279','UF_CRM_1529755353','UF_CRM_1529753275','UF_CRM_1531814668','UF_CRM_1529755307','UF_CRM_1529755333'));

//$arResult['TEST']['ALL_DEALS'] = $this->getDealDataByFilter($arFilter_deals2,array('TITLE','ASSIGNED_BY_ID','UF_CRM_1529755279','UF_CRM_1529755353','UF_CRM_1529753275','UF_CRM_1531814668','UF_CRM_1529755307','UF_CRM_1529755333'));

//новый массив для сбора данных
$arResult['PROGGERS_V2'] = array();

$proggers_data_massive = array(
    'ID' => 0,
    'NAME' => '',
    'POSITION' => '',
    'IMAGE_PATH' => '',
    'HOURS_PROGGER_PLAN' => 0,
    'HOURS_PROGGER_FACT' => 0,
    'HOURS_PROGGER_PERCENT' => array(
        'VALUE' => 0,
        'COLOR' => 0,
    ),
    'HOURS_OCENKA_PLAN' => 0,
    'HOURS_OCENKA_FACT' => 0,
    'HOURS_OCENKA_PERCENT' => 0,
    'HOURS_PROGGER_FACT_CUR_MONTH' => array(
        'VALUE' => 0,
        'COLOR' => 0,
    ),
    'DEALS' => array(),
);

foreach ($all_deals as $deal){ //$arResult['PROGGERS_V2']['ALL_DEALS']

    foreach ($deal['UF_CRM_1529753275'] as $progger_id){
        if(!in_array($progger_id,$arResult['PROGGERS_V2'])) {
            $arResult['PROGGERS_V2'][$progger_id] = $proggers_data_massive;//массив проггеров из всех сделок на реализации

            $progger_data = $this->getUserDataByID($progger_id);
            $arResult['PROGGERS_V2'][$progger_id]['NAME'] = $progger_data['LAST_NAME'].' '.$progger_data['NAME'];
            $arResult['PROGGERS_V2'][$progger_id]['POSITION'] = $progger_data['WORK_POSITION'];
            $arResult['PROGGERS_V2'][$progger_id]['IMAGE_PATH'] = $this->getPhotoPath($progger_data['PERSONAL_PHOTO']);
        }
       // echo $progger_id.'<br>';
    }
}



foreach ($arResult['PROGGERS_V2'] as $prog_id => $prog_massive){

    foreach ($all_deals as $deal){ //$arResult['PROGGERS_V2']['ALL_DEALS']

        //элементы только с фильтром по роли "проггеры" по id проггера
        if(in_array($prog_id,$deal['UF_CRM_1529753275'])){

            //Считаем план проггера по формуле = план_из_поля_сделки_по_проггеру / кол-во_человек_из_поля_проггеров (уникальное, чтобы не повторялись)
            $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PLAN'] += $deal['UF_CRM_1529755279'] / count(array_unique($deal['UF_CRM_1529753275']));

            //получаем списки элемента с привязкой к сделке по каждому проггеру ЗА ВСЕ ВРЕМЯ СДЕЛКИ
            $listFilter = array(
                "IBLOCK_ID" => 124,
                // '>=CREATED_DATE' => '2018.11.01',//date('d.m.Y', strtotime('01.'.date('m').'.'.date('Y'))),
                'PROPERTY_586' => $deal['ID'],
                //   'PROPERTY_587' => 668, //программист
                'PROPERTY_588' => $prog_id,
                'ACTIVE' => 'Y',
            );
            $listSelect = array('ID','NAME','PROPERTY_585','PROPERTY_586','PROPERTY_587','PROPERTY_588','PROPERTY_589'); //на всяк случай запрашиваю все поля списков
            $listResult = $this->getListElementsByFilter($listFilter,$listSelect/*, array('PROPERTY_586' => 'desc')*/);



            $deal_elements = array();
            $prog_hours_fact = 0;


            foreach ($listResult as $list_field){
                $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT'] += $list_field['NAME']; //можно сразу плюсовать к факту проггера

                $deal_elements[] = array('ID' => $list_field['ID'], 'HOURS' => $list_field['NAME']);
                $prog_hours_fact += $list_field['NAME'];

            }


            //получаем списки элемента с привязкой к сделке по каждому проггеру
            $arResult['PROGGERS_V2'][$prog_id]['ID'] = $prog_id;
            $arResult['PROGGERS_V2'][$prog_id]['DEALS'][] = array(
                'ID' => $deal['ID'],
                'TITLE' => $deal['TITLE'],
               // 'ASSIGNED_BY_ID' => $deal['ASSIGNED_BY_ID'],
                'DEAL_PLAN' => $deal['UF_CRM_1529755279'],
                'DEAL_FACT_FROM_FIELD' => $deal['UF_CRM_1529755353'], //Из поля
                'DEAL_FACT_BY_ELEMENTS' => $prog_hours_fact, //То же самое, но из элементов учетов времени
               // 'DEAL_ELEMENTS' => $deal_elements, //можно пересчитать вручную кол-во факт. часов по каждой сделке
            );

           // $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PERCENT']['VALUE'] = round($arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT'] / $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PLAN'] * 100,2);
            $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PERCENT']['VALUE'] = round(($arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PLAN'] - $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT']) / $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PLAN'] * 100,2);
            $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PERCENT']['COLOR'] = $this->getNeededStatisticColor(($arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PLAN'] - $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT']));
        }

        //элементы только с фильтром по роли оценки по id проггера
        if(in_array($prog_id,$deal['UF_CRM_1531814668'])) {
//            $arResult['PROGGERS_V2'][$prog_id]['HOURS_OCENKA_PLAN'] += $deal['UF_CRM_1529755307'] / count(array_unique($deal['UF_CRM_1531814668']));
            $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PLAN'] += $deal['UF_CRM_1529755307'] / count(array_unique($deal['UF_CRM_1531814668']));

            //получаем списки элемента с привязкой к сделке по каждому проггеру ЗА ВСЕ ВРЕМЯ СДЕЛКИ
            $listFilter = array(
                "IBLOCK_ID" => 124,
                // '>=CREATED_DATE' => '2018.11.01',//date('d.m.Y', strtotime('01.'.date('m').'.'.date('Y'))),
                'PROPERTY_586' => $deal['ID'],
                //   'PROPERTY_587' => 668, //программист
                'PROPERTY_588' => $prog_id,
                'ACTIVE' => 'Y',
            );
            $listSelect = array('ID','NAME','PROPERTY_585','PROPERTY_586','PROPERTY_587','PROPERTY_588','PROPERTY_589'); //на всяк случай запрашиваю все поля списков
            $listResult = $this->getListElementsByFilter($listFilter,$listSelect/*, array('PROPERTY_586' => 'desc')*/);

            $deal_elements = array();
            $prog_ocenka_hours_fact = 0;


            foreach ($listResult as $list_field){
                $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT'] += $list_field['NAME']; //можно сразу плюсовать к факту проггера
                $arResult['PROGGERS_V2'][$prog_id]['HOURS_OCENKA_FACT'] += $list_field['NAME']; //а можно и потом посчитать, при рассчете % - это просто для наглядности

                $deal_elements[] = array('ID' => $list_field['ID'], 'HOURS' => $list_field['NAME']);
                $prog_ocenka_hours_fact += $list_field['NAME'];

            }

            //получаем списки элемента с привязкой к сделке по каждому проггеру
            $arResult['PROGGERS_V2'][$prog_id]['DEALS'][] = array(
                'ID' => $deal['ID'],
                'TITLE' => $deal['TITLE'],
                // 'ASSIGNED_BY_ID' => $deal['ASSIGNED_BY_ID'],
                'DEAL_PLAN' => $deal['UF_CRM_1529755307'], //ОЦЕНКА
                'DEAL_FACT_FROM_FIELD' => $deal['UF_CRM_1529755333'], //Из поля //ОЦЕНКА
                'DEAL_FACT_BY_ELEMENTS' => $prog_ocenka_hours_fact, //То же самое, но из элементов учетов времени //ОЦЕНКА
                // 'DEAL_ELEMENTS' => $deal_elements, //можно пересчитать вручную кол-во факт. часов по каждой сделке
            );

            if($arResult['PROGGERS_V2'][$prog_id]['HOURS_OCENKA_PLAN'] == 0) $arResult['PROGGERS_V2'][$prog_id]['HOURS_OCENKA_PERCENT'] = 0;
            else $arResult['PROGGERS_V2'][$prog_id]['HOURS_OCENKA_PERCENT'] = round($arResult['PROGGERS_V2'][$prog_id]['HOURS_OCENKA_FACT'] / $arResult['PROGGERS_V2'][$prog_id]['HOURS_OCENKA_PLAN'] * 100,2);

        }

    }

    //получаем списки элемента с привязкой к сделке по каждому проггеру ЗА ТЕКУЩИЙ МЕСЯЦ
    //фильтр по полю Property_585 со спец. приемом
    $monthHourListFilter = array(
        "IBLOCK_ID" => 124,
       // '>=DATE_CREATE' => date('d.m.Y H:i:s', strtotime('01.'.date('m').'.'.date('Y'))),
        '>=PROPERTY_585' => date('Y-m-d', strtotime('01.'.date('m').'.'.date('Y'))),
        //   'PROPERTY_586' => $deal['ID'],
        //   'PROPERTY_587' => 668, //программист
        'PROPERTY_588' => $prog_id,
        'ACTIVE' => 'Y',
    );
    $monthHourListSelect = array('ID','NAME','PROPERTY_585','PROPERTY_586'); //на всяк случай запрашиваю все поля списков
    $monthHourListResult = $this->getListElementsByFilter($monthHourListFilter,$monthHourListSelect/*, array('PROPERTY_586' => 'desc')*/);

    foreach ($monthHourListResult as $element){
        $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT_CUR_MONTH']['VALUE'] += $element['NAME'];
    }
    $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT_CUR_MONTH']['COLOR'] = $this->getNeededStatisticColor($arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT_CUR_MONTH']['VALUE']);


    //пересчет % выполнения каждой сделки
    foreach ($arResult['PROGGERS_V2'][$prog_id]['DEALS'] as $num => $dealValue){
       /* if($arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT'] == 0) $arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] = 0;
        else $arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] = round($arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['DEAL_FACT_BY_ELEMENTS'] / $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT'] * 100,2);*/

        if($arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['DEAL_PLAN'] == 0) $arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] = 0;
        else $arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] = round($arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['DEAL_FACT_BY_ELEMENTS'] / $arResult['PROGGERS_V2'][$prog_id]['DEALS'][$num]['DEAL_PLAN'] * 100,2);

    }

}

//Проггеры v2 Окончание









/*
$arResult['PROGGERS_V2'][$prog_id]['HOURS_TIME_LEFT'] = $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_PLAN'] / $arResult['PROGGERS_V2'][$prog_id]['HOURS_PROGGER_FACT'];
if($arResult['PROGGERS_V2'][$prog_id]['HOURS_TIME_LEFT'] < 0) $arResult['PROGGERS_V2'][$prog_id]['HOURS_TIME_LEFT'] = 'ДОХУЯ!';
*/



//получение сделок для "Проекты в работе"
$arResult['PROJECTS_IN_WORK'] = array();
$arFilter_projects = array(
    'STAGE_ID' => 26
);
$project_result = $this->getDealDataByFilter($arFilter_projects,array('ID','TITLE','STAGE_ID','ASSIGNED_BY_ID','OPPORTUNITY','UF_*'));
foreach ($project_result as $project){

    //картинка ответственного за сделку
    $assigned_by_data = $this->getUserDataByID($project['ASSIGNED_BY_ID']);
    $project['ASSIGNED_BY_NAME'] = $assigned_by_data['NAME'].' '.$assigned_by_data['LAST_NAME'];
    $project['ASSIGNED_BY_IMG_PATH'] = $this->getPhotoPath($assigned_by_data['PERSONAL_PHOTO']);

    //Процент выполнения по аналитику
    $project['AVARGE_HOURS_ANALITIC_PLAN'] = '';
    $project['AVARGE_HOURS_ANALITIC_FACT'] = '';
    $project['UF_CRM_1529754646'] == 0 ? $project['AVARGE_HOURS_ANALITIC_PLAN'] = 1 : $project['AVARGE_HOURS_ANALITIC_PLAN'] = $project['UF_CRM_1529754646'];
    $project['AVARGE_HOURS_ANALITIC_FACT'] = $project['UF_CRM_1529754702'];
    $project['AVARGE_HOURS_ANALITIC_PERCENT'] = round($project['AVARGE_HOURS_ANALITIC_FACT'] / $project['AVARGE_HOURS_ANALITIC_PLAN']*100,2);

    //Массив id исполнителей работ для получения их ФИО и картинок
    $project['EMPLOYEES'] = array();
    $employees['ANALITICS'] = array();
    foreach ($project['UF_CRM_1529753120'] as $id){
        if(!in_array($id,$project['EMPLOYEES']['ANALITICS'])) {

            //получение картинок пользователей по id
            $employee_data = $this->getUserDataByID($id);
            $project['EMPLOYEES']['ANALITICS'][$id] = array(
                'ID' => $employee_data['ID'],
                'NAME' => $employee_data['NAME'].' '.$employee_data['LAST_NAME'],
                'IMAGE_PATH' => $this->getPhotoPath($employee_data['PERSONAL_PHOTO']),
            );
        }
    }
    $project['EMPLOYEES']['PROGGERS'] = array();
    foreach ($project['UF_CRM_1529753275'] as $id){
        if(!in_array($id,$project['EMPLOYEES']['PROGGERS'])){

            //получение картинок пользователей по id
            $employee_data = $this->getUserDataByID($id);
            $project['EMPLOYEES']['PROGGERS'][$id] = array(
                'ID' => $employee_data['ID'],
                'NAME' => $employee_data['NAME'].' '.$employee_data['LAST_NAME'],
                'IMAGE_PATH' => $this->getPhotoPath($employee_data['PERSONAL_PHOTO']),
            );
        }
    }
    $project['EMPLOYEES']['OCENKA'] = array();
    foreach ($project['UF_CRM_1531814668'] as $id){
        if(!in_array($id,$project['EMPLOYEES']['OCENKA'])) {

            //получение картинок пользователей по id
            $employee_data = $this->getUserDataByID($id);
            $project['EMPLOYEES']['OCENKA'][$id] = array(
                'ID' => $employee_data['ID'],
                'NAME' => $employee_data['NAME'].' '.$employee_data['LAST_NAME'],
                'IMAGE_PATH' => $this->getPhotoPath($employee_data['PERSONAL_PHOTO']),
            );
        }
    }



    //Процент выполнения по прогеру
    $project['AVARGE_HOURS_PROGGER_PLAN'] = '';
    $project['AVARGE_HOURS_PROGGER_FACT'] = '';


    $project['UF_CRM_1529755279'] == 0 ? $project['AVARGE_HOURS_PROGGER_PLAN'] = 1 : $project['AVARGE_HOURS_PROGGER_PLAN'] = $project['UF_CRM_1529755279'];
    $project['AVARGE_HOURS_PROGGER_FACT'] = $project['UF_CRM_1529755353'];
    $project['AVARGE_HOURS_PROGGER_PERCENT'] = round($project['AVARGE_HOURS_PROGGER_FACT'] / $project['AVARGE_HOURS_PROGGER_PLAN']*100,2);

    //Процент выполнения по оценке
    $project['AVARGE_HOURS_OCENKA_PLAN'] = '';
    $project['AVARGE_HOURS_OCENKA_FACT'] = '';
    $project['UF_CRM_1529755307'] == 0 ? $project['AVARGE_HOURS_OCENKA_PLAN'] = 1 : $project['AVARGE_HOURS_OCENKA_PLAN'] = $project['UF_CRM_1529755307'];
    $project['AVARGE_HOURS_OCENKA_FACT'] = $project['UF_CRM_1529755333'];
    $project['AVARGE_HOURS_OCENKA_PERCENT'] = round($project['AVARGE_HOURS_OCENKA_FACT'] / $project['AVARGE_HOURS_OCENKA_PLAN']*100,2);


    //Процент % выполнения ВСЕГО = Сумм. факт / Сумм. план * 100
    $project['AVARGE_HOURS_SUM_PLAN'] = $project['UF_CRM_1529754646'] + $project['UF_CRM_1529755279'] + $project['UF_CRM_1529755307'];
    if($project['AVARGE_HOURS_SUM_PLAN'] == 0) $project['AVARGE_HOURS_SUM_PLAN'] = 1;

    $project['AVARGE_HOURS_SUM_FACT'] = $project['UF_CRM_1529754702'] + $project['UF_CRM_1529755353'] + $project['UF_CRM_1529755333'];
   // if($project['AVARGE_HOURS_SUM_FACT'] == 0) $project['AVARGE_HOURS_SUM_FACT'] = 1;

    //План - факт для вывода остатка вермени
    $project['AVARGE_HOURS_PLAN_MINUS_FACT']['VALUE'] = $project['AVARGE_HOURS_SUM_PLAN'] - $project['AVARGE_HOURS_SUM_FACT'];
    $project['AVARGE_HOURS_PLAN_MINUS_FACT']['COLOR'] = $this->getNeededProjectHoursColor($project['AVARGE_HOURS_SUM_PLAN'],$project['AVARGE_HOURS_SUM_FACT']);

    $project['AVARGE_HOURS_PERCENT'] = round(($project['AVARGE_HOURS_SUM_FACT']) / ($project['AVARGE_HOURS_SUM_PLAN']) * 100,2);

    /*$project['AVARGE_HOURS_PERCENT'] > 100 ? $project['AVARGE_HOURS_PERCENT'] = 'НЕВЕРНО!' : $project['AVARGE_HOURS_PERCENT'] .= '%';*/

    $arResult['PROJECTS_IN_WORK'][] = $project;
}
//$arResult['PROJECTS_IN_WORK'] = $project_result;


//подключение шаблона Все нужно получать до него!
$this->IncludeComponentTemplate();


?>