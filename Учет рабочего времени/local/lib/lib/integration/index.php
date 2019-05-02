<?php
define("NOT_CHECK_PERMISSIONS",true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$GLOBALS["USER"]->Authorize(1);
Global $USER;

// // echo "string";
// echo "<pre>";
// print_r($_POST);
if(isset($_POST['user_name']) && !empty($_POST['user_name'])){

    CModule::IncludeModule("crm");
    $CCrmLead = new CCrmLead();

    $title = (isset($_POST['TITLE']) && !empty($_POST['TITLE'])) ? $_POST['TITLE'] : 'Лид с неведомого сайта';
    $name = (isset($_POST['user_name']) && !empty($_POST['user_name'])) ? $_POST['user_name'] : 'Лид с неведомого сайта';
    $phone = (isset($_POST['phone']) && !empty($_POST['phone'])) ? $_POST['phone'] : '';
    $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : '';



    $arLeadFields = [];
    $arLeadFields = array(
        'TITLE' => $title,
        'NAME' => $name,
        'UF_CRM_1463151730' => 255,
        // 'STATUS_ID' => $_POST['STATUS_ID'],
        // 'SOURCE_ID' => $_POST['SOURCE_ID'],
        "FM" => Array(
            "PHONE" => Array(
                "n1" => Array(
                    "VALUE" => $phone,
                    "VALUE_TYPE" => "WORK",
                    )
                ),
            "EMAIL" => Array(
                "n2" => Array(
                    "VALUE" => $email,
                    "VALUE_TYPE" => "WORK",
                    )
                )
            )
        );
    // echo "<pre>";
    // print_r($arLeadFields);
    $leadID = $CCrmLead->Add($arLeadFields);
    echo $leadID;
}
$GLOBALS["USER"]->Logout();