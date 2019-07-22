<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
//CModule::IncludeModule("crm");

class DashBoardClass extends CBitrixComponent{

    public $months = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
    public $error = 'Не заполнен план на текущий месяц!';
    public $vnedrenie_id = '0';
    public $licensed_id = '2';
    public $online_course_id = '3';
    public $not_closed_invoices = ['P','D'];
    public $payed_invoices_stage_id = 'P';
    public $canseled_invoices_stage_id = 'D';
    public $deals_in_work_stages = [25,28,26,'C4:EXECUTING','C4:FINAL_INVOICE'];//стадии Договор-счёт, Первичная оплата,Реализация, Тестирование - отладка
    public $proggers_group = 29;
    public $time_accounting_block_id = 124;

    public $analiticRoleId = 667;
    public $proggerRoleId = 668;
    public $ocenkaRoleId = 669;

//    public function testFunction(){
//        $this->sentAnswer(array('result' => 'ЭТО МЕТОД КЛАССА DashBoardClass'));
//    }

    public function getAnaliticsUsersData($month,$year){
        $error = false;

        $planned = $this->straightQueryNonMassive($month, $year); //!!!!!!!!передаем текущий месяц и год
        if(!$planned) $error = $this->error;
        else{
            $company = array();
            foreach ($planned as $key => $val){
                //$usersData[$key] = $this->getEachAnaliticData($val[0],$month,$year); // $val[0] - id аналитика

                //01.07.2019 - По СЧЕТАМ!!! - Вкладка 1
                $usersData[$key] = $this->getEachAnaliticDataByInvoices($val[0],$month,$year); // $val[1] - план аналитика
                $usersData[$key]['MONTH_PLAN'] = $val[1]; // $val[1] - план аналитика
                $usersData[$key]['PLAN_COMPLETED'] = round($usersData[$key]['MONTH_FACT'] / $val[1] * 100,2); // % выполнения плана

                //    $usersData[$key]['PLAN_COMPLETED']['COLOR'] = $this->getNeededProgressColor($usersData[$key]['PLAN_COMPLETED']['VALUE']); // цвет выполнения плана

                $company['MONTH_PLAN'] += $val[1];
                $company['MONTH_FACT'] += $usersData[$key]['MONTH_FACT'];
                $company['INVOICES_POTENCIAL'] += $usersData[$key]['INVOICES_POTENCIAL'];
                //- Вкладка 1

            }
            $company['COMPANY_COMPLETED'] = round(($company['MONTH_FACT'] / $company['MONTH_PLAN']) * 100,2);
          //  $company['COMPANY_COMPLETED']['COLOR'] = $this->getNeededProgressColor( $company['COMPANY_COMPLETED']['VALUE']);
            $company['DATA'] = $this->months[$month - 1].' '.$year;

            //- Вкладка 2 - неоплаченные счета -> список сделок -> сделка + все счета + статус каждого счета
            $unpayedInvoicesByDeals = $this->getDealsByUnpayedInvoices();
            //- Вкладка 2 - неоплаченные счета -> список сделок -> сделка + все счета + статус каждого счета

            //- Вкладка 3 - Программисты
            $proggers = $this->getEachProggerData($month,$year);
            //- Вкладка 3 - Программисты

            //- Вкладка 4 - Сделки в работе
            $dealsOnRealization = $this->getDealsOnRealization();
            //- Вкладка 4 - Сделки в работе
        }

        $this->sentAnswer(
            [
                'analitics' => $usersData,
                'error' => $error,
                'company' => $company,
                'unpayed_invoices' => $unpayedInvoicesByDeals,
                'proggers' => $proggers,
                'deals_on_realization' => $dealsOnRealization,
                //'test' => $planned
            ]
        );
    }

    //01.07.2019 - Получение даннніх пользователя + счета
    private function getEachAnaliticDataByInvoices($userId,$month,$year){
        $userData = [];

        //подсчет часов (пока аналитиков)
        $hoursWorkedRes = $this->userWorkedHours($userId, $month.'.'.$year);

        $userDataMassive = $this->getUserDataById($userId);
        if($userDataMassive){
            $userData = array(
                'ID' => $userDataMassive['ID'],
                'NAME' => $this->toUpperFirstChar($userDataMassive['LAST_NAME']).' '.$this->toUpperFirstChar($userDataMassive['NAME']),
                'WORK_POSITION' => $this->toUpperFirstChar($userDataMassive['WORK_POSITION']),
                'PHOTO' => $this->getPhotoPath($userDataMassive['PERSONAL_PHOTO']),
                'HOURS_WORKED' => $hoursWorkedRes,
                'MONTH_FACT' => 0,
                'CATEGORIES' => $this->getCategories(),
                'INVOICES_POTENCIAL' => 0,
            );
        }

        $userInvoicesFilterPayedThisMonth = [
            'STATUS_ID' => 'P',
            'RESPONSIBLE_ID' => $userId,
            // '>=DATE_STATUS' => date('d.m.Y H:i:s', strtotime('01.'.$month.'.'.$year)),//strtotime('01.'.date('m').'.'.date('Y'))),
            '>=PAY_VOUCHER_DATE' => date('d.m.Y H:i:s', strtotime('01.'.$month.'.'.$year)),//strtotime('01.'.date('m').'.'.date('Y'))),
        ];
        $userInvoicesResult = $this->getInvoicesByFilter($userInvoicesFilterPayedThisMonth,['ID','STATUS_ID','ORDER_TOPIC','DATE_STATUS','RESPONSIBLE_ID','PRICE','UF_DEAL_ID','PAY_VOUCHER_DATE']);
        if($userInvoicesResult){
         //   $userData['INVOICES_1'] = $userInvoicesResult;

            $payedInvoicesArr = [];

            foreach ($userInvoicesResult as $invoice){
              //  $userData['MONTH_FACT'] += $invoice['PRICE'];
                $payedInvoicesArr[] = $invoice['ID'];
                $userData['INVOICES_IDS'] = $payedInvoicesArr;


                //считаем по направлениям
                $dealsFilter = ['ID' => $invoice['UF_DEAL_ID']];
                $dealsSelect = ['ID','CATEGORY_ID','TITLE','OPPORTUNITY','UF_CRM_1562662560576']; //UF_CRM_1562662560576 - поле с откатом
                $dealsResult = $this->getDealDataByFilter($dealsFilter,$dealsSelect);
                if($dealsResult){
                    foreach ($userData['CATEGORIES'] as $key => $category){

                        ($dealsResult[0]['UF_CRM_1562662560576'])
                            ? $vzyatka = $dealsResult[0]['UF_CRM_1562662560576'] : $vzyatka = 0;

                        if($dealsResult[0]['CATEGORY_ID'] == $category['ID']){
                            if($dealsResult[0]['CATEGORY_ID'] == $this->licensed_id){
                                $userData['CATEGORIES'][$key]['INVOICES_SUM'] += ($invoice['PRICE'] / 2) - $vzyatka;
                                $userData['MONTH_FACT'] += ($invoice['PRICE'] / 2) - $vzyatka;
                            }
                            else{
                                $userData['CATEGORIES'][$key]['INVOICES_SUM'] += ($invoice['PRICE'] - $vzyatka);
                                $userData['MONTH_FACT'] += ($invoice['PRICE'] - $vzyatka);
                            }
                        }
                    }
                }
            }
        }

        $userInvoicesFilterPayedStatus = [
            'STATUS_ID' => 'P',
            'RESPONSIBLE_ID' => $userId,
            '>=DATE_STATUS' => date('d.m.Y H:i:s', strtotime('01.'.$month.'.'.$year)),//strtotime('01.'.date('m').'.'.date('Y'))),
        ];
        $userInvoicesPayedStatusResult = $this->getInvoicesByFilter($userInvoicesFilterPayedStatus,['ID','STATUS_ID','ORDER_TOPIC','DATE_STATUS','RESPONSIBLE_ID','PRICE','UF_DEAL_ID','PAY_VOUCHER_DATE']);
        if($userInvoicesPayedStatusResult){
         //   $userData['INVOICES_2'] = $userInvoicesPayedStatusResult;
            foreach ($userInvoicesResult as $invoice){
               // if(!in_array($invoice['ID'],$userData['INVOICES_IDS'])){
                if(!in_array($invoice['ID'],$payedInvoicesArr)){
                    //$userData['MONTH_FACT'] += $invoice['PRICE'];

                    $dealsFilter = ['ID' => $invoice['UF_DEAL_ID']];
                    $dealsSelect = ['ID','CATEGORY_ID','TITLE','OPPORTUNITY'];
                    $dealsResult = $this->getDealDataByFilter($dealsFilter,$dealsSelect);
                    if($dealsResult){
                        foreach ($userData['CATEGORIES'] as $key => $category){
                            if($dealsResult[0]['CATEGORY_ID'] == $category['ID']){
                                if($dealsResult[0]['CATEGORY_ID'] == $this->licensed_id){
                                    $userData['CATEGORIES'][$key]['INVOICES_SUM'] += $invoice['PRICE'] / 2;
                                    $userData['MONTH_FACT'] += $invoice['PRICE'] / 2;
                                }
                                else{
                                    $userData['CATEGORIES'][$key]['INVOICES_SUM'] += $invoice['PRICE'];
                                    $userData['MONTH_FACT'] += $invoice['PRICE'];
                                }
                            }
                        }

                 //   $userData['DEALS_IDS'][] = $dealsResult;

                    }
                }

            }
        }

        //потенциальніе счета
        $potencialInvoicesFilter = ['!STATUS_ID' => $this->not_closed_invoices, 'RESPONSIBLE_ID' => $userId];
        $potencialInvoicesResult = $this->getInvoicesByFilter($potencialInvoicesFilter,['ID','STATUS_ID',/*'DATE_STATUS','ORDER_TOPIC',*/'RESPONSIBLE_ID','PRICE','UF_DEAL_ID']);
        if($potencialInvoicesResult){
           // $userData['INVOICES_POTENCIAL'] = $potencialInvoicesResult;

            //в зависимости от направления сделки считаем сумму (для лицензирования делим на 2)
            foreach ($potencialInvoicesResult as $invoice){
                $dealsFilter = ['ID' => $invoice['UF_DEAL_ID']];
                $dealsSelect = ['ID','CATEGORY_ID','TITLE','OPPORTUNITY'];
                $dealsResult = $this->getDealDataByFilter($dealsFilter,$dealsSelect);
                if($dealsResult){
                    if($dealsResult[0]['CATEGORY_ID'] == $this->licensed_id)
                        $userData['INVOICES_POTENCIAL'] += $invoice['PRICE'] / 2;
                    else $userData['INVOICES_POTENCIAL'] += $invoice['PRICE'];
                }
            }
        }

        return $userData;
    }

    //Вкладка 2 - неоплаченные счета => сделки + все счета + их статусы
    private function getDealsByUnpayedInvoices(){
        $dealsAndItsInvoices = false;

        $notClosedInvoicesFilter = ['!STATUS_ID' => $this->not_closed_invoices];
        $notClosedInvoicesResult = $this->getInvoicesByFilter($notClosedInvoicesFilter,['ID','STATUS_ID',/*'DATE_STATUS','ORDER_TOPIC',*/'RESPONSIBLE_ID','PRICE','UF_DEAL_ID']);
        if($notClosedInvoicesResult){

            //1. получаем ID сделок и запрашиваем по ним уже данные сделки + все ее счета со статусами.
            $dealsFromInvoices = [];
            foreach ($notClosedInvoicesResult as $key => $unpayedInvoice){
                $dealsFromInvoices[] = $unpayedInvoice['UF_DEAL_ID'];
            }

            //2. Запрос данных сделки
            if($dealsFromInvoices){
                foreach ($dealsFromInvoices as $dealId){
                    $dealsFilter = ['ID' => $dealId];
                    $dealsSelect = ['ID','CATEGORY_ID','TITLE','OPPORTUNITY','ASSIGNED_BY_ID','UF_CRM_1562662560576']; // UF_CRM_1562662560576 - сумма отката
                    $dealsResult = $this->getDealDataByFilter($dealsFilter,$dealsSelect);
                    if($dealsResult){
                        foreach ($dealsResult as $deal){
                            $payedInvoices = 0;
                            $potencialInvoicesSum = 0;
                            $assignedPhoto = '';
                            $assignedName = '';
                            $assignedData = $this->getUserDataById($deal['ASSIGNED_BY_ID']);
                            if($assignedData){
                                $assignedPhoto = $this->getPhotoPath($assignedData['PERSONAL_PHOTO']);
                                $assignedName = $assignedData['LAST_NAME'].' '.$assignedData['NAME'];
                            }

                            //3. Все счета по сделке
                            $allInvoicesForDealFilter = ['UF_DEAL_ID' => $dealId];
                            $allInvoicesForDealResult = $this->getInvoicesByFilter($allInvoicesForDealFilter,['ID','STATUS_ID','ORDER_TOPIC','RESPONSIBLE_ID','PRICE','UF_DEAL_ID']);
                            if($allInvoicesForDealResult){
                                foreach ($allInvoicesForDealResult as $key => $oneInvoice){

                                    //округление суммы, т.к. выдает 4 знака после запятой
                                    $allInvoicesForDealResult[$key]['PRICE'] = round($oneInvoice['PRICE'],2);
                                    //переводим символі в символі без quotes ...
                                    $allInvoicesForDealResult[$key]['ORDER_TOPIC'] = HTMLToTxt($oneInvoice['ORDER_TOPIC']);

                                    $allInvoicesForDealResult[$key]['RESPONSIBLE_NAME'] = '';
                                    $allInvoicesForDealResult[$key]['RESPONSIBLE_PHOTO'] = '';
                                    $allInvoicesForDealResult[$key]['STATUS_NAME'] = '';


                                    //4.Ответственный за счет
                                    if($deal['ASSIGNED_BY_ID'] == $oneInvoice['RESPONSIBLE_ID']){
                                        $allInvoicesForDealResult[$key]['RESPONSIBLE_NAME'] = $assignedName;
                                        $allInvoicesForDealResult[$key]['RESPONSIBLE_PHOTO'] = $assignedPhoto;
                                    }
                                    else{
                                        $responsible_id_data = $this->getUserDataByID($oneInvoice['RESPONSIBLE_ID']);
                                        $allInvoicesForDealResult[$key]['RESPONSIBLE_NAME'] = $responsible_id_data['NAME'].' '.$responsible_id_data['LAST_NAME'];
                                        $allInvoicesForDealResult[$key]['RESPONSIBLE_PHOTO'] = $this->getPhotoPath($responsible_id_data['PERSONAL_PHOTO']);
                                    }

                                    //5. Статус счета
                                    $refFilter = ['ENTITY_ID' => 'INVOICE_STATUS', 'STATUS_ID' => $oneInvoice['STATUS_ID']];
                                    $refResult = $this->getReferenceBook($refFilter);
                                    if($refResult) $allInvoicesForDealResult[$key]['STATUS_NAME'] = HTMLToTxt($refResult['NAME']); //перевод из HTML в текст

                                    //6. Сумма неоплаченніх счетов
                                    if(!in_array($oneInvoice['STATUS_ID'],$this->not_closed_invoices)){
                                        if($deal['CATEGORY_ID'] == $this->licensed_id)
                                            $dealsAndItsInvoices['INVOICES_POTENCIAL'] += $oneInvoice['PRICE'] / 2;
                                        else
                                            $dealsAndItsInvoices['INVOICES_POTENCIAL'] += $oneInvoice['PRICE'];
                                    }
                                    if($oneInvoice['STATUS_ID'] == $this->payed_invoices_stage_id)
                                        $payedInvoices += $oneInvoice['PRICE'];
                                    if($oneInvoice['STATUS_ID'] != $this->canseled_invoices_stage_id)
                                        $potencialInvoicesSum += $oneInvoice['PRICE'];
                                }
                            }

                            ($deal['UF_CRM_1562662560576']) ? $vzyatka = $deal['UF_CRM_1562662560576'] : $vzyatka = 0;

                            $dealsAndItsInvoices['DEALS_WITH_INVOICES'][] = [
                                'ID' => $deal['ID'],
                                'TITLE' => HTMLToTxt($deal['TITLE']),
                                'OPPORTUNITY' => round($deal['OPPORTUNITY'],2),
                                'VZYATKA' => $vzyatka, //сумма отката по сделке
                                'ASSIGNED_BY_NAME' => $assignedName,
                                'ASSIGNED_BY_PHOTO' => $assignedPhoto,
                                'INVOICES_PAYED' => $payedInvoices,
                                'INVOICES_POTENCIAL' => $potencialInvoicesSum,
                                'INVOICES' => $allInvoicesForDealResult,

                            ];
                        }
                    }
                }
            }
        }

        return $dealsAndItsInvoices;
    }


    //Вкладка 3 - Проггеры
    private function getEachProggerData($month,$year){
        $proggersMassive = false;

        $proggers = $this->getUsersFromGroup($this->proggers_group);
        if($proggers) {

            foreach ($proggers as $key => $progger){
                $dealsResult = [];
                $dealsFilter = [
                    'STAGE_ID' => $this->deals_in_work_stages,
                    '!UF_CRM_1529753275' => '', //берем только те сделки, где есть хоть один проггер,

                    //СЛОЖНАЯ ЛОГИКА - РАБОТАЕТ!
                    [
                        'LOGIC' => 'OR',
                        ['UF_CRM_1529753120' => $progger['ID']], //поле Аналитики
                        ['UF_CRM_1529753275' => $progger['ID']], //поле Проггері
                        ['UF_CRM_1531814668' => $progger['ID']], //поле Оценщики
                    ],
                ];
                $dealsSelect = [
                    'TITLE','ASSIGNED_BY_ID',
                    'UF_CRM_1529753120','UF_CRM_1529753275','UF_CRM_1531814668', // поля Аналитики, Проггері, Оценщики = ARRAY
                    'UF_CRM_1529754646','UF_CRM_1529755279','UF_CRM_1529755307'  //поля ПЛАН (как и віше)
                    /*'UF_CRM_1529755279','UF_CRM_1529755353','UF_CRM_1529753275','UF_CRM_1531814668','UF_CRM_1529755307','UF_CRM_1529755333'*/];
                $dealsResult = $this->getDealDataByFilter($dealsFilter,$dealsSelect);

                $asAnaliticPlan = 0;
                $asAnaliticFact = 0;
                $asProggerPlan = 0;
                $asProggerFact = 0;
                $asOcenkaPlan = 0;
                $asOcenkaFact = 0;

                if($dealsResult) {
                    foreach ($dealsResult as $key => $oneDeal){

                        $dealsResult[$key]['DEAL_HOURS_PLAN_WHOLE'] = 0;
                        $dealsResult[$key]['DEAL_HOURS_FACT_WHOLE'] = 0;
                        $dealsResult[$key]['TITLE'] = HTMLToTxt($oneDeal['TITLE']);

                        $dealsResult[$key]['ASSIGNED_BY_PHOTO'] = '';
                        $dealsResult[$key]['ASSIGNED_BY_NAME'] = '';
                        $assignedByIdData =  $this->getUserDataById($oneDeal['ASSIGNED_BY_ID']);
                        if($assignedByIdData) {
                            $dealsResult[$key]['ASSIGNED_BY_NAME'] = $assignedByIdData['LAST_NAME'].' '.$assignedByIdData['NAME'];
                            $dealsResult[$key]['ASSIGNED_BY_PHOTO'] = $this->getPhotoPath($assignedByIdData['PERSONAL_PHOTO']);
                        }

                        //Считаем план проггера в статусе аналитика
                        if(in_array($progger['ID'],$oneDeal['UF_CRM_1529753120'])){
                            $asAnaliticPlan += $oneDeal['UF_CRM_1529754646'] / count(array_unique($oneDeal['UF_CRM_1529753120']));
                           // $dealsResult[$key]['DEAL_HOURS_PLAN_WHOLE'] += $oneDeal['UF_CRM_1529754646'] / count(array_unique($oneDeal['UF_CRM_1529753120']));
                            $dealsResult[$key]['DEAL_HOURS_PLAN_WHOLE'] += $oneDeal['UF_CRM_1529754646'];
                        }

                        //Считаем план проггера в статусе проггера
                        if(in_array($progger['ID'],$oneDeal['UF_CRM_1529753275'])){
                            $asProggerPlan += $oneDeal['UF_CRM_1529755279'] / count(array_unique($oneDeal['UF_CRM_1529753275']));
                          //  $dealsResult[$key]['DEAL_HOURS_PLAN_WHOLE'] += $oneDeal['UF_CRM_1529755279'] / count(array_unique($oneDeal['UF_CRM_1529753275']));
                            $dealsResult[$key]['DEAL_HOURS_PLAN_WHOLE'] += $oneDeal['UF_CRM_1529755279'];
                        }

                        //Считаем план проггера в статусе оценщика
                        if(in_array($progger['ID'],$oneDeal['UF_CRM_1531814668'])){
                            $asOcenkaPlan += $oneDeal['UF_CRM_1529755307'] / count(array_unique($oneDeal['UF_CRM_1531814668']));
                           // $dealsResult[$key]['DEAL_HOURS_PLAN_WHOLE'] += $oneDeal['UF_CRM_1529755307'] / count(array_unique($oneDeal['UF_CRM_1531814668']));
                            $dealsResult[$key]['DEAL_HOURS_PLAN_WHOLE'] += $oneDeal['UF_CRM_1529755307'];
                        }


                        //Считаем факт часов по сделке
                        $listFilter = [
                            "IBLOCK_ID" => $this->time_accounting_block_id,
                            'PROPERTY_586' => $oneDeal['ID'],
                            'PROPERTY_588' => $progger['ID'],
                            'ACTIVE' => 'Y',
                        ];
                        $listSelect = ['ID','NAME','PROPERTY_585','PROPERTY_586','PROPERTY_587','PROPERTY_588','PROPERTY_589']; //на всяк случай запрашиваю все поля списков
                        $listResult = $this->getListElementsByFilter($listFilter,$listSelect);

                        $TestListMassive[] = $listResult;

                        if($listResult){
                            foreach ($listResult as $elem){
                                if($elem['PROPERTY_587_ENUM_ID'] == 667){
                                    $asAnaliticFact += $elem['NAME'];
                                    $dealsResult[$key]['DEAL_HOURS_FACT_WHOLE'] += $elem['NAME'];
                                }
                                if($elem['PROPERTY_587_ENUM_ID'] == 668){
                                    $asProggerFact += $elem['NAME'];
                                    $dealsResult[$key]['DEAL_HOURS_FACT_WHOLE'] += $elem['NAME'];
                                }
                                if($elem['PROPERTY_587_ENUM_ID'] == 669){
                                    $asOcenkaFact += $elem['NAME'];
                                    $dealsResult[$key]['DEAL_HOURS_FACT_WHOLE'] += $elem['NAME'];
                                }
                            }
                        }
                    }
                }

                $proggersMassive[] = [
                    'ID' => $progger['ID'],
                    'NAME' => $progger['LAST_NAME'] .' '.$progger['NAME'],
                    'WORK_POSITION' => $this->toUpperFirstChar($progger['WORK_POSITION']),
                    'PHOTO' => $this->getPhotoPath($progger['PERSONAL_PHOTO']),
                    'HOURS_WORKED' => $this->userWorkedHours($progger['ID'], $month.'.'.$year),
                    'DEALS' => $dealsResult,
                    'ROLES' => [
                        /*'AS_ANALITIC_HOURS' => */[
                            'PLAN' => $asAnaliticPlan,
                            'FACT' => $asAnaliticFact,
                            'TITLE' => 'aka Аналитик',
                        ],
                        /*'AS_PROGGER_HOURS' => */[
                            'PLAN' => $asProggerPlan,
                            'FACT' => $asProggerFact,
                            'TITLE' => 'aka Проггер',
                        ],
                        /*'AS_OCENKA_HOURS' => */[
                            'PLAN' => $asOcenkaPlan,
                            'FACT' => $asOcenkaFact,
                            'TITLE' => 'aka Оценщик',
                        ],
                    ],
                    'HOURS_WHOLE' => [
                        'PLAN' => $asAnaliticPlan + $asProggerPlan + $asOcenkaPlan,
                        'FACT' => $asAnaliticFact + $asProggerFact + $asOcenkaFact,
                    ],
                   // 'LISTS' => $listResult,
                ];
            }
        }

        return $proggersMassive;
    }


    //Сделки в работе (реализации)
    private function getDealsOnRealization(){
        $result = false;

        $dealInRealizFilter = ['STAGE_ID' => 26]; //26 - Реализация stage на внедрении
        $dealInRealizSelect = [
            'ID','TITLE','STAGE_ID','ASSIGNED_BY_ID',/*'OPPORTUNITY',*/
            'UF_CRM_1529754646','UF_CRM_1529755279','UF_CRM_1529755307',
            'UF_CRM_1529753120','UF_CRM_1529753275','UF_CRM_1531814668'
        ];
        $dealInRealizResult = $this->getDealDataByFilter($dealInRealizFilter,$dealInRealizSelect);
        if($dealInRealizResult){

            foreach ($dealInRealizResult as $key => $project){

                //данніе отвтетсвенного
                $assigned_by_data = $this->getUserDataByID($project['ASSIGNED_BY_ID']);
                $project['ASSIGNED_BY_NAME'] = $assigned_by_data['LAST_NAME'].' '.$assigned_by_data['NAME'];
                $project['ASSIGNED_BY_PHOTO'] = $this->getPhotoPath($assigned_by_data['PERSONAL_PHOTO']);
                $project['TITLE'] = HTMLToTxt($project['TITLE']);


                $analitic = [
                    'PLAN' => 0,
                    'FACT' => 0,
                    'EMPLOYEES' => [],
                    'TITLE' => 'Аналитики',
                ];

                //Процент выполнения по прогеру
                $progger = [
                    'PLAN' => 0,
                    'FACT' => 0,
                    'EMPLOYEES' => [],
                    'TITLE' => 'Проггеры',
                ];

                //Процент выполнения по оценке
                $ocenka = [
                    'PLAN' => 0,
                    'FACT' => 0,
                    'EMPLOYEES' => [],
                    'TITLE' => 'Оценка',
                ];


                foreach (array_unique($project['UF_CRM_1529753120']) as $id){

                    //получение картинок пользователей по id
                    $employee_data = $this->getUserDataByID($id);
                    $analitic['EMPLOYEES'][] = [
                        'ID' => $employee_data['ID'],
                        'NAME' => $employee_data['NAME'].' '.$employee_data['LAST_NAME'],
                        'IMAGE_PATH' => $this->getPhotoPath($employee_data['PERSONAL_PHOTO']),
                    ];
                }

                foreach (array_unique($project['UF_CRM_1529753275']) as $id){

                    //получение картинок пользователей по id
                    $employee_data = $this->getUserDataByID($id);
                    $progger['EMPLOYEES'][] = [
                        'ID' => $employee_data['ID'],
                        'NAME' => $employee_data['NAME'].' '.$employee_data['LAST_NAME'],
                        'IMAGE_PATH' => $this->getPhotoPath($employee_data['PERSONAL_PHOTO']),
                    ];
                }

                foreach (array_unique($project['UF_CRM_1531814668']) as $id){

                    //получение картинок пользователей по id
                    $employee_data = $this->getUserDataByID($id);
                    $ocenka['EMPLOYEES'][] = [
                        'ID' => $employee_data['ID'],
                        'NAME' => $employee_data['NAME'].' '.$employee_data['LAST_NAME'],
                        'IMAGE_PATH' => $this->getPhotoPath($employee_data['PERSONAL_PHOTO']),
                    ];
                }


                //получаем все учеты времени по сделке и выбираем аналитика, проггера и оценщика по времени
                $listFilter = [
                    "IBLOCK_ID" => $this->time_accounting_block_id,
                    'PROPERTY_586' => $project['ID'],
                ];
                $listSelect = ['ID','NAME','PROPERTY_586','PROPERTY_587']; //на всяк случай запрашиваю все поля списков
                $listResult = $this->getListElementsByFilter($listFilter,$listSelect);
                if($listResult){
                    foreach ($listResult as $elem){
                        if($elem['PROPERTY_587_ENUM_ID'] == $this->analiticRoleId)
                            $analitic['FACT'] += $elem['NAME'];

                        if($elem['PROPERTY_587_ENUM_ID'] == $this->proggerRoleId)
                            $progger['FACT'] += $elem['NAME'];

                        if($elem['PROPERTY_587_ENUM_ID'] == $this->ocenkaRoleId)
                            $ocenka['FACT'] += $elem['NAME'];
                    }
                }


                if($analitic['FACT'] > 0){
                    $project['UF_CRM_1529754646'] == 0 ? $analitic['PLAN'] = 1 : $analitic['PLAN'] = intval($project['UF_CRM_1529754646']);
                }
                else $analitic['PLAN'] = intval($project['UF_CRM_1529754646']);

                if($progger['FACT'] > 0){
                    $project['UF_CRM_1529755279'] == 0 ? $progger['PLAN'] = 1 : $progger['PLAN'] = intval($project['UF_CRM_1529755279']);
                }
                else $progger['PLAN'] = intval($project['UF_CRM_1529755279']);

                if($ocenka['FACT'] > 0){
                    $project['UF_CRM_1529755307'] == 0 ? $ocenka['PLAN'] = 1 : $ocenka['PLAN'] = intval($project['UF_CRM_1529755307']);
                }
                else $ocenka['PLAN'] = intval($project['UF_CRM_1529755307']);


                $project['AVARGE_DEAL_PLAN'] = $analitic['PLAN'] + $progger['PLAN'] + $ocenka['PLAN'];
                $project['AVARGE_DEAL_FACT'] = $analitic['FACT'] + $progger['FACT'] + $ocenka['FACT'];



                $project['ROLES'] = [$analitic, $progger, $ocenka];

                $result[] = $project;
            }
        }
        return $result;
    }


    private function userWorkedHours($userId,$date=false){
        if($date === false) $date = date('Y-m-d', strtotime('01.'.date('m').'.'.date('Y')));
        else $date = '01.'.$date;
        $listHoursFilter = array(
            "IBLOCK_ID" => $this->time_accounting_block_id,
            '>=PROPERTY_585' => date('Y-m-d', strtotime($date)),
            'PROPERTY_588' => $userId,
            'ACTIVE' => 'Y',
        );
        $listHoursSelect = array('ID','NAME','PROPERTY_588','PROPERTY_586'); //на всяк случай запрашиваю все поля списков
        $listHoursResult = $this->getListElementsByFilter($listHoursFilter,$listHoursSelect);
//        $result = array(
//            'hours' => 0,
//            'color' => '',
//        );
        $result = 0;
        foreach ($listHoursResult as $hoursWorked){
            $result += $hoursWorked['NAME']; //считаем кол-во часов сотрудника
        }
        //цвет для значения - 01.05 перенесено в vue в компонент
        //$result['color'] = $this->getNeededStatisticColor( $result['hours']);

        return $result;
    }


    //получение элементов списка по фильтру (Property_ не отдает)
    private function getListElementsByFilter($arFilter,$arSelect,$arOrder = ''){
        $result = array();
        $resultList = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($list = $resultList->Fetch()) {
            $result[] = $list;
        }
        return $result;
    }


    //получение фото по id
    private function getPhotoPath($photo_id){
        return $photoMassive = CFile::GetPath($photo_id);
    }


    //цвета для часов выроботки по сотрудникам
    private function getNeededProgressColor($value){
        $color = '';
        switch (true){
            case $value < 30:
                $color = '#ff0a0a';
                break;
            case (30 <= $value && $value < 50):
                $color = '#f3b507';
                break;
            case (50 <= $value && $value < 70):
                $color = '#07a7f3';
                break;
            case (70 <= $value && $value < 99):
                $color = '#39ea46';
                break;
            case 99 <= $value:
                $color = '#48b919';
                break;
        }
        return $color;
    }

    //ответ в консоль
    private function sentAnswer($answ){
        echo json_encode($answ);
    }

    private function straightQueryNonMassive($month,$year){
        global $DB;
        $result = false;

        // Works! //$str = "SELECT * FROM b_crm_widget_saletarget WHERE PERIOD_MONTH = 10";
        $str = "SELECT * FROM b_crm_widget_saletarget WHERE PERIOD_MONTH = ".$month." AND PERIOD_YEAR = ".$year;

        $res = $DB->Query($str);
        while ($row = $res->Fetch()) {
            $planData = $row;
        }

        if($planData){


            //ищем первую скобку (в ней id и план каждого аналитика) и обрезаем все до нее - "а:3..."
            $pos = strpos ($planData['TARGET_GOAL'],'{');
            $a = substr($planData['TARGET_GOAL'],$pos);

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
            $result = array_chunk($array_analitics,2);

        }

        return $result;
    }

    //История сделок
    private function getDealHistory($arFilter,$arSelect,$dealStageId,$month,$year){
        CModule::IncludeModule("crm");
        $acts_massive = array(30,'C2:4','C3:FINAL_INVOICE');
        $won_massive = array('WON','C2:WON','C3:WON');
        $actes = [];
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
        //return $result;
        if(!empty($actes)){
            rsort($actes);

            if(date('n',$actes[0]) == date('n', strtotime('01.'.$month.'.'.$year))) {
                //   echo 'Есть редактированные в этом месяце!';
                // $newDealsMassive = $arFilter1;
                return date('d.m.Y H:i:s',$actes[0]);
            }
            return false;

        }else{
            return false;
        }

    }


    //получение сделок специалиста по фильтру и указанным к выдаче полям
    private function getDealDataByFilter($arFilter,$arSelect){
        $deals = [];
        $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array()); //получение пользовательских полей сделки по ID
        while($ar_result = $db_list->GetNext()){
            $deals[] = $ar_result;
        }
        return $deals;
    }


    //01.07.2019 - Данніе пользователя по ID
    private function getUserDataById($userId){
        $result = false;
        $userDataMassive = CUser::GetByID($userId);
        if($res = $userDataMassive->Fetch())
            $result = $res;
        return $result;
    }

    private function getInvoicesByFilter($filter,$select){
        $massive = CCrmInvoice::GetList($arOrder = ["DATE_STATUS"=>"ASC"], $filter,false,false,$select,[]);
        while($ar_result = $massive->GetNext()){
            $invoices[] = $ar_result;
        }
        return $invoices;
    }

    private function getCategories(){
        $catIds = \Bitrix\Crm\Category\DealCategory::getAllIDs();
        $massive = [];
        foreach ($catIds as $catId){
            $massive[] = [
                'ID' => $catId,
                'NAME' => $this->getCategoryNameById($catId),
                'INVOICES_SUM' => 0,
            //    'STAGES' => $this->getCategoryStages($catId),
            ];
        }
        return $massive;
    }

    private function getCategoryNameById($category_id){
        return $name = \Bitrix\Crm\Category\DealCategory::getName($category_id);
    }

    //Возвр. первый символ
    private function toUpperFirstChar($string){
        $char = mb_strtoupper(substr($string,0,2), "utf-8"); // это первый символ
        $string[0] = $char[0];
        $string[1] = $char[1];
        return $string;
    }


    //Получение пользовтелей из группы
    private function getUsersFromGroup($group_id){
        $filter = ['GROUPS_ID' => $group_id]; // ID группы - 28 - аналитики, 29 - проггеры
        $rsUsers = CUser::GetList(($by="ID"), ($order="ASC"), $filter);
        while($arItem = $rsUsers->GetNext())
        {
            //убираем пользователей с пустым именем и фамилией из выпадающего списка
            if($arItem['LAST_NAME'] == '' && $arItem['NAME'] == '' ) continue;
            $users[] = $arItem;
        }
        return $users;
    }

    //получение значения справочников
    private function getReferenceBook($filter){
        //array('ENTITY_ID' => 'CONTACT_TYPE', 'STATUS_ID' => $ID)

        $db_list = CCrmStatus::GetList([], $filter);
        $result = [];
        if ($ar_result = $db_list->GetNext()) $result = $ar_result;
        return $result;
    }


    //На удаление
    /*private function getEachAnaliticData($userId,$month,$year){
       // global $USER;

        //подсчет часов (пока аналитиков)
        $hoursWorkedRes = $this->userWorkedHours($userId, $month.'.'.$year);

        $userDataMassive = CUser::GetByID($userId);
        $res = $userDataMassive->Fetch();
        $userData = array(
            'ID' => $res['ID'],
            'NAME' => $res['LAST_NAME'].' '.$res['NAME'],
            'WORK_POSITION' => $res['WORK_POSITION'],
            'PHOTO' => $this->getPhotoPath($res['PERSONAL_PHOTO']),
            'HOURS_WORKED' => $hoursWorkedRes,
           // 'HOURS_WORKED_COLOR' => $hoursWorkedRes['color'],
            'MONTH_FACT' => 0,
           // 'DEALS_ID' => [],
           // 'DEALS_HISTORY' => [],
        );

        //Факт сумма
        $deals_filter = array(
            'ASSIGNED_BY_ID' => $userId,
            'STAGE_ID' => array('WON',30,'C2:4','C2:WON','C3:FINAL_INVOICE','C3:WON'), //стадии из 3-х направлений - Акты, отзывы, завершено (выграно)
          //  ">=CLOSEDATE" => date('d.m.Y', strtotime('01.'.date('m.Y',strtotime('now')))), //date('m.Y',strtotime('-1 month'))
            ">=CLOSEDATE" => '01.'.$month.'.'.$year, //date('m.Y',strtotime('-1 month'))
        );
        $deals_select = array('ID','TITLE','STAGE_ID','OPPORTUNITY');

        $analiticDeals = $this->getDealDataByFilter($deals_filter,$deals_select);
        //$userData['DEALS_ID'] = $analiticDeals;

        $analiticAllDealsHistory = array();
        foreach ($analiticDeals as $key => $dealD){

            //проверка, чтобы полсдний переход на одну из искомых стадий был в этом месяце
            $historyFilter = Array('ENTITY_ID' => $dealD['ID'],'ENTIYY_TYPE_ID' => 2, 'EVENT_TYPE' => 1,'ENTITY_FIELD' => 'STAGE_ID'); // 'EVENT_TEXT_2' => 'Акты' - не ищет!
            $historySelect = Array('ID','EVENT_TEXT_1','EVENT_TEXT_2','DATE_CREATE');
            $historyResult = $this->getDealHistory($historyFilter,$historySelect,$dealD['STAGE_ID'],$month,$year);

            //$userData['DEALS_HISTORY'][] = $this->getDealHistory($historyFilter,$historySelect,$dealD['STAGE_ID'],$month,$year);

            if($historyResult != false) $analiticAllDealsHistory[$key] = $dealD;

        }

        if($analiticAllDealsHistory != null) {

            foreach ($analiticAllDealsHistory as $key => $dealData){

                //27.12.2018 По просьбе Эмиля - если направление = "лицензирование", то сумма прибыль = сумма прихода / 2 (для БУС он обещал сделать что-то отдельно, т.к. там коэфф. 0,45
                if(in_array($dealData['STAGE_ID'], array('C2:4','C2:WON'))){ //стадии направления "лицензирование" - акты и выигрыш
                    $dealData['OPPORTUNITY'] = $dealData['OPPORTUNITY'] / 2;
                }


                $userData['MONTH_FACT'] += $dealData['OPPORTUNITY'];


                if($dealData['STAGE_ID'] == 'WON' || $dealData['STAGE_ID'] == 30) {
                    $userData['CATEGORIES']['VNEDRENIE']['NAME'] = 'Внедрение';
                    $userData['CATEGORIES']['VNEDRENIE']['SUM'] += $dealData['OPPORTUNITY'];
                }
                if($dealData['STAGE_ID'] == 'C2:WON' || $dealData['STAGE_ID'] == 'C2:4') {
                    $userData['CATEGORIES']['LICENCED']['NAME'] = 'Лицензирование';
                    $userData['CATEGORIES']['LICENCED']['SUM'] += $dealData['OPPORTUNITY'];
                }
                if($dealData['STAGE_ID'] == 'C3:FINAL_INVOICE' || $dealData['STAGE_ID'] == 'C3:WON') {
                    $userData['CATEGORIES']['ONLINE_COURSE']['NAME'] = 'Онлайн-курс';
                    $userData['CATEGORIES']['ONLINE_COURSE']['SUM'] += $dealData['OPPORTUNITY'];
                }
            }

            if($userData['CATEGORIES']['VNEDRENIE']) {
                $userData['CATEGORIES']['VNEDRENIE']['PERCENT_TO_EXISTED_SUM'] = round($userData['CATEGORIES']['VNEDRENIE']['SUM'] / $userData['MONTH_FACT'] * 100,2);
            }
            if($userData['CATEGORIES']['LICENCED']) {
                $userData['CATEGORIES']['LICENCED']['PERCENT_TO_EXISTED_SUM'] = round($userData['CATEGORIES']['LICENCED']['SUM'] / $userData['MONTH_FACT'] * 100,2);
            }
            if($userData['CATEGORIES']['ONLINE_COURSE']) {
                $userData['CATEGORIES']['ONLINE_COURSE']['PERCENT_TO_EXISTED_SUM'] = round($userData['CATEGORIES']['ONLINE_COURSE']['SUM'] / $userData['MONTH_FACT'] * 100,2);
            }
            //$arResult['INTEGRATORS'][$analitic_id]['DEALS'][] = $deal_data;
            //$arResult['INTEGRATORS'][$analitic_id]['DEALS_SUM'] += $deal_data['OPPORTUNITY'];



            //подсчет плана компании и факта выполнения


        }




       // $userData['HISTORY'] = $analiticAllDealsHistory;

        return $userData;
    }*/

    //цвета для часов выроботки по сотрудникам - перенес в компонент vue 01.05.2019
//    private function getNeededStatisticColor($value){
//        $color = '';
//        switch (true){
//            case $value < 50:
//                $color = '#e00808';
//                break;
//            case (50 <= $value && $value < 100):
//                $color = '#fd8d02';
//                break;
//            case (100 <= $value && $value < 150):
//                $color = '#28b305';
//                break;
//            case (150 <= $value && $value < 200):
//                $color = '#fd8d02';
//                break;
//            case 200 <= $value:
//                $color = '#e00808';
//                break;
//        }
//        return $color;
//    }



}