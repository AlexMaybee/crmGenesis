<?php

CModule::IncludeModule("CRM");

class WorkTimeAjax{

    const IBLOCK_124 = 124;

    //проверяет, чтобы кол-во часов было БОЛЬШЕ 5! и выдает ошибку
    public function countWorkedHoursInCurrentDay($post){
        $result = [
            'result' => false,
            'error' => false,
        ];

        $count = 0;

        $elemFilter = [
            'IBLOCK_ID' => self::IBLOCK_124,
            'PROPERTY_585' => date('Y-m-d', $post['UNICODE_DATE_START']),
            'PROPERTY_588' => $this->curUserId(), // ID тек. пользователя
        ];
        $elemSelect = ['ID','NAME','IBLOCK_ID','PROPERTY_*'];

        $elemResult = $this->getListElementsAndPropsByFilter($elemFilter,$elemSelect);
        if($elemResult){
            foreach ($elemResult as $element){
                $count += str_replace(',','.',$element['FIELDS']['NAME']);
            }

            if($count < 5){
                $result['error'] = 'За '.date('d.m.Y', $post['UNICODE_DATE_START'])
                    .' внесено только '.$count.' часов, а должно быть не менее 5!';
            }
            else $result['result'] = true;
        }
        else{
            $result['error'] = 'Чтобы завершить рабочий день, необходимо за '.date('d.m.Y', $post['UNICODE_DATE_START'])
                .' внести в учет времени не менее 5 часов!';
        }

//        $result['count'] = $count;
//        $result['DATE_START'] = date('Y-m-d H:i:s',$post['UNICODE_DATE_START']);
//        $result['DATE_FINISH'] = date('Y-m-d H:i:s',$post['UNICODE_DATE_FINISH']);

        $this->sentAnswer($result);
    }

    public function getListFieldsAndValues(){
        $result = [
            'fields' => [],
            'error' => false,
        ];
        $listFieldsWithValues = []; //массив для сохранения полей

        $listFields = $this->getListFieldsListByFilter(['IBLOCK_ID' => self::IBLOCK_124]);
        if($listFields){

            $filter = ['IBLOCK_ID' => self::IBLOCK_124];
            $listFieldsValues = $this->getListFieldValuesByFilter($filter);
            if($listFieldsValues){
                foreach ($listFields as $key => $field){
                    $listFieldsWithValues[$key] = $field;
                    $listFieldsWithValues[$key]['VALUES'] = [];

                    foreach ($listFieldsValues as $j => $fieldValue){

                        //ID поля == свойству PROPERTY_ID в значениях
                        if($field['ID'] == $fieldValue['PROPERTY_ID'])
                            $listFieldsWithValues[$key]['VALUES'][] = [
                                'VALUE' => $fieldValue['ID'],
                                'TEXT' => $fieldValue['VALUE'],
                            ];
                    }
                }

                $result['fields'] = $listFieldsWithValues;

            }
            else $result['error'] = 'Ошибка при получении значений множественных полей из списка №'.self::IBLOCK_124;
        }
        else $result['error'] = 'Ошибка при получении полей из списка №'.self::IBLOCK_124;

//            $result['FIELDS'] = $listFields;
//            $result['VALUES'] = $listFieldsValues;
//            $result['FIELDS_WITH_VALUES'] = $listFieldsWithValues;

        $this->sentAnswer($result);
    }

    //поиск сделки по имени
    public function getDealsListByTitle($data){
        $result = [
            'result' => false,
            'error' => false,
        ];

        (preg_match('/^[\d]+$/',trim($data['TITLE']))) ? $filter = ['ID' => trim($data['TITLE'])]
            : $filter = ['%TITLE' => $data['TITLE']];

        $dealsList = $this->getDealsListByFilter($filter,['ID','TITLE']);
        if($dealsList) $result['result'] = $dealsList;
        else $result['error'] = 'Произошла ошибка при поиске сделки по названию';

        $this->sentAnswer($result);
    }

    //создание элемента TaskTime
    public function createNewtaksTimeElement($data){
        $result = [
            'result' => false,
            'error' => false,
        ];

        //замена запятой на точку в поле отработанного времени
        if(strpos($data['HOURS'],',')) $data['HOURS'] = str_replace(',','.',$data['HOURS']);

        //создаем новый элемент
        $newElemFields = [
            'NAME' => $data['HOURS'], //название дочернего элемента, у меня это время
            "IBLOCK_ID" => self::IBLOCK_124,
            'PROPERTY_VALUES' => [
                '585' => [
                    'n0' => [
                        'VALUE' => date('d.m.Y', strtotime($data['DATA123'])), //Дата/Время
                    ],
                ],
                '586' => [
                    'n0' => [
                        'VALUE' => $data['PROEKT_ID'], //Сделка, ID
                    ],
                ],
                '587' => $data['ROL'], //Роль
                '588' => [
                    'n0' => [
                        'VALUE' => $this->curUserId(), //ID специалиста
                    ]
                ],
                '589' => [
                    'n0' => [
                        'VALUE' => $data['KOMMENTARIY'], //TextArea
                    ],
                ],
                '590' => $data['TIPOVYE_RABOTY'], //Тип работ

            ],
        ];

        $result['test_fields'] = $newElemFields;

        $createElemRes = $this->createNewListElement($newElemFields);
        if($createElemRes['RESULT']) {
            $result['result'] = true;
        }
        else $result['error'] = $createElemRes['MESSAGE'];


        $this->sentAnswer($result);
    }


    private function sentAnswer($answ){
        echo json_encode($answ);
    }

    private function curUserId(){
        global $USER;
        return $USER->GetID();
    }

    private function getListElementsAndPropsByFilter($arFilter,$arSelect){
        $result = [];
        //пример получения всех свойств (не работает в обычном виде) -  ["ID", "IBLOCK_ID", "NAME","PROPERTY_*"]
        //без запроса в выборке "IBLOCK_ID" не будет работать!!!
        $resultList = CIBlockElement::GetList([], $arFilter, false, false,$arSelect);
        while($ob = $resultList->GetNextElement()){
            $result[] = [
                'FIELDS' => $ob->GetFields(),
                'PROPERTIES' => $ob->GetProperties(),
            ];
        }
        return $result;
    }

    //получение списка полей типа список для СПИСКОВ
    private function getListFieldsListByFilter($filter){
        $result = [];
        $listFieldsMassive = CIBlockProperty::GetList(["SORT"=>"ASC"],$filter);
        while($arRes = $listFieldsMassive->getNext()) $result[] = $arRes;
        return $result;
    }

    //получение списка значений поля типа список для СПИСКОВ
    private function getListFieldValuesByFilter($filter){
        $result = [];
        $fieldValMassive = CIBlockPropertyEnum::GetList(["SORT"=>"ASC", "VALUE"=>"ASC"],$filter);
        while($arRes = $fieldValMassive->getNext()) $result[] = $arRes;
        return $result;
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

    //достаем данные по ID сделки
    private function getDealsListByFilter($arFilter,$arSelect){
        $result = [];
        $db_list = CCrmDeal::GetListEx(["ID" => "DESC"], $arFilter, false, false, $arSelect, []); //получение пользовательских полей сделки по ID
        while($ar_result = $db_list->GetNext()) $result[] = $ar_result;
        return $result;
    }

}