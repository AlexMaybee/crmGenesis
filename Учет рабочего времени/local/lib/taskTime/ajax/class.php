<?php

class BestClass{

    const IBLOCK_124 = 124;

    public function getCurUserId(){
//        global $USER;
//        $userId = $USER->GetID();
        $userId = $this->curUserId();
        $answ = array('ID' => $userId);

        $this->sentAnswer($answ);
    }


    //данные задачи
    public function getCurTaskData($data){
        $result = false;
        $message = 'start getCurTaskData function!';

        $taskData = $this->getTaskData($data['TASK_ID']);

        //$this->logData('taskLog.log',$taskData);

        if(count($taskData['UF_CRM_TASK']) > 0){

            if($taskData['STATUS'] != 5){
                //проверка, что в массиве есть элемент привязки именно к сделке, полагаем, что задача не может быть привязана к нескольким сделкам
                $taskParentDeals = [];
                foreach ($taskData['UF_CRM_TASK'] as $item) {
                    if(preg_match('/^D_([\d]+)/',$item,$matches)) $taskParentDeals[] = $matches[1]; //отдаем именно ID без D_
                }

                //если задача прикреплена к сделке и есть ее id, тогда показывать кнопку
                if(count($taskParentDeals) > 0){
                    if(count($taskParentDeals) > 1) $message = 'Задача прикреплена к нескольким сделкам, кнопку добавления персонала не показываем!';
                    else{
                        $result = $taskParentDeals[0];
                        //$result = $taskParentDeals; //возвращаем первый id сделки в массиве
                        $message = 'Получен ID сделки! Кнопку добавления персонала можно показывать!!!';
                    }

                }
            }
            else $message = 'Не показываем кнопку в завершенной задаче!';
        }

        $this->sentAnswer(['result' => $result, 'message' => $message]);
    }

    //данные для отображения в попапе
    public function getNeededFieldsForPopup($data){
        $result = [
            'result' => false,
            'message' => 'Умолчание getNeededFieldsForPopup',
            'errors' => false,
        ];

        //достаем данные сделки
        $filter = [
            'ID' => $data['DEAL_ID'],
        ];
        $select = ['ID','ASSIGNED_BY_ID','TITLE'];
        $dealData = $this->getDealData($filter,$select);

        if(!$dealData) $result['errors'][] = 'Ошибка при получении данных сделки!';
        else{
            $result['result']['DEAL_DATA'] = $dealData;

            //получаем данные для полей select
            $roleOprions = $this->getPropertyOptions(587);
            $worksType = $this->getPropertyOptions(590);
            (!$roleOprions) ? $result['errors'][] = 'Не получены значения поля РОЛЬ!' :  $result['result']['ROLE_OPTIONS'] = $roleOprions;
            (!$worksType) ? $result['errors'][] = 'Не получены значения поля Типовые работы!' :  $result['result']['WORKS_TYPE_OPTIONS'] = $worksType;
        }



        $this->sentAnswer($result);
    }

    //создание элемента учета времени
    public function createNewTaskTimeElem($data){
        $result = [
            'result' => false,
            'error' => false,
        ];

        //замена запятой на точку в поле отработанного времени
        if(strpos($data['TIME'],',')) $data['TIME'] = str_replace(',','.',$data['TIME']);

        //создаем новый элемент
        $newElemFields = [
            'NAME' => $data['TIME'], //название дочернего элемента, у меня это время
            "IBLOCK_ID" => self::IBLOCK_124,
            'PROPERTY_VALUES' => [
                '585' => [
                    'n0' => [
                        'VALUE' => $data['DATE_TIME'], //Дата/Время
                    ],
                ],
                '586' => [
                    'n0' => [
                        'VALUE' => $data['DEAL_ID'], //Сделка, ID
                    ],
                ],
                '587' => $data['ROLE'], //Роль
                '588' => [
                    'n0' => [
                        'VALUE' => $this->curUserId(), //ID специалиста
                    ]
                ],
                '589' => [
                    'n0' => [
                        'VALUE' => $data['WORKS_DESCRIPTION'], //TextArea
                    ],
                ],
                '590' => $data['WORKS_TYPE'], //Тип работ

            ],
        ];

        $createElemRes = $this->createNewListElement($newElemFields);
        if($createElemRes['RESULT']) {
            $result['result'] = 'Создан новый элемент #'.$createElemRes['RESULT'];
            $result['result'] .= "<br>Время в размере ".$data['TIME'].' часов учтено для сделки "'.$data['DEAL_NAME'].'"';
        }
        else $result['error'] = $createElemRes['MESSAGE'];

        $this->sentAnswer($result);
    }

    //ответ в консоль
    private function sentAnswer($answ){
        echo json_encode($answ);
    }

    //пролучение инфы задачи по id
    private function getTaskData($id){
        $rsTask = CTasks::GetByID($id);
        if($ar_result = $rsTask->GetNext()) return $ar_result;
        else return false;
    }

    //достаем данные по ID сделки
    private function getDealData($arFilter,$arSelect){
        $db_list = CCrmDeal::GetListEx(Array("ID" => "ASC"), $arFilter, false, false, $arSelect, array()); //получение пользовательских полей сделки по ID
        if($ar_result = $db_list->GetNext()) return $ar_result;
        return false;
    }

    //получение options для select и др. полей, для вставки в поля
    private function getPropertyOptions($propID){
        $selectList = CIBlockProperty::GetPropertyEnum($propID, Array("SORT"=>"asc"), Array());
        while ($list = $selectList->GetNext()) {
            $options[] = array('OPTION_ID' => $list['ID'], 'OPTION_VALUE' => $list['VALUE']);
        }
        return $options;
    }

    //создание элемента списка для списания денег с баланса
    private function createNewListElement($fields){

        $elem = new CIBlockElement;
        $new_id = $elem->Add($fields);

        if ($new_id) {
            $res = array(
                'RESULT' => $new_id,
                'MESSAGE' => 'Создан новый элемент списка!',
            );
        }
        else {
            $res = array(
                'RESULT' => false,
                'MESSAGE' => $elem->LAST_ERROR,
            );
        }
        return $res;
    }

    private function curUserId(){
        global $USER;
        return $userId = $USER->GetID();
    }

    private function logData($filename,$data){
        $file = $_SERVER['DOCUMENT_ROOT'].'/local/lib/taskTime/'.$filename;
        file_put_contents($file, print_r($data,true), FILE_APPEND | LOCK_EX);
    }

}