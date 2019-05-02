<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json');

CModule::IncludeModule("CRM");



class BestClass{

    public function getCurUserId(){
        global $USER;
        $userId = $USER->GetID();
        $answ = array('ID' => $userId);

        $this->sentAnswer($answ);
    }

    //ответ в консоль
    private function sentAnswer($answ){
        echo json_encode($answ);
    }

}

$obj = new BestClass();

if($_POST['action'] == 'GiveMeCurUserID') $obj->getCurUserId();