<?php
// cp.itlogic.biz
// Код приложения: local.58590130b74db5.20111453
// Ключ приложения: egFOMLRsoGKaLwmyr5D96YVQIKuxpSHABrQ9eNL0eDF3pBu5g2
// die('test');
define("NOT_CHECK_PERMISSIONS",true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
Global $USER;
$USER->Authorize(1);

define('PORTAL_NAME',           'cp.itlogic.biz');
define('CLIENT_ID',             'local.58590130b74db5.20111453');
define('CLIENT_SECRET',         'egFOMLRsoGKaLwmyr5D96YVQIKuxpSHABrQ9eNL0eDF3pBu5g2');
define('APP_LINK',              urlencode('https://cp.itlogic.biz/local/lib/telephony/index.php'));
define('SCOPE',                 '');
define('COOKIE_CONF',           __DIR__.'/tmp/');
define('ROOT',                  __DIR__);
define('TEMP_DATA_DIRECTORY',   'temp_data');
define('STEP',                  3);

include(ROOT.'/bx.framework.php');

if(isset($_REQUEST['cron'])){
    unlink(TOKENS_FILE);
}
// echo "string";
show_errors(false);
bx_auth(3);
log_write($_REQUEST, 'data');

// pre(SetEventListener('https://cp.itlogic.biz/local/lib/telephony/index.php', 'onexternalcallstart'));
// pre(GetEventListeners());

if(isset($_REQUEST['event']) && $_REQUEST['event'] == 'ONEXTERNALCALLSTART'){

    //http://46.174.163.39/dial_outgw.php?internal=777&number=380676633900
    $uel2ast = 'http://46.174.163.39/dial_outgw.php';
    $data2ast['number'] = $_REQUEST['data']['PHONE_NUMBER'];
    $us = $USER->GetByID($_REQUEST['data']['USER_ID'])->Fetch();

    $data2ast['internal'] = $us['UF_PHONE_INNER'];

    // $res = query("GET", 'https://'. PORTAL_NAME .'/rest/user.get.'. $format .'?auth='. $GLOBALS['ACCESS_TOKEN'], $arFilter);
    // $u = json_decode($res, 1);
    // log_write($u, 'data');


    $result = query("GET", $uel2ast, $data2ast);
    log_write($result, 'resp');
}

if(isset($_REQUEST['method']) && isset($_REQUEST['data'])){

    $data = $_REQUEST['data'];
    $method = $_REQUEST['method'];
    
    /////////////////////////////////////////////////////////////////////
    if($_REQUEST['method'] == 'itlogic.vox.config'){

        Global $DB;
        $res =  $DB->query('SELECT * FROM b_voximplant_config');
        $item = [];
        while($i = $res->fetch()){

            $item[] = $i;
        }
        echo json_encode($item);    
        exit();
    }
    /////////////////////////////////////////////////////////////////////

    $format = 'json';
    $url = 'https://'. PORTAL_NAME .'/rest/'. $method .'.'. $format .'?auth='. $GLOBALS['ACCESS_TOKEN'];
    $result = query("GET", $url, $data);

    echo $result;

    /////////////////////////////////////////////////////////////////////
    if($_REQUEST['method'] == 'telephony.externalcall.register' && $_REQUEST['data']['TYPE'] == '1'){

        $rd = json_decode($result,1);
        log_write($rd, 'call.out');

        if(!empty($rd['result']['CRM_CREATED_LEAD'])){

            CModule::IncludeModule("crm");
            $CCrmLead = new CCrmLead();
            // $CCrmActivity = new CCrmActivity();
            $arLeadUpdate = [];
            $arLeadUpdate = [
                'TITLE' => $_REQUEST['data']['PHONE_NUMBER'].' - Исходящий'
            ];
            $upd = $CCrmLead->Update(intval($rd['result']['CRM_CREATED_LEAD']), $arLeadUpdate);
            log_write($upd, 'upd');

            // $arAcUpdate = [];
            // $arAcUpdate = [
            //     'TYPE_ID' => 1,
            //     'DIRECTION' => 1
            // ];
            // $upd = $CCrmActivity->Update(intval($rd['result']['CRM_ACTIVITY_ID']), $arAcUpdate);


        }
    }
    /////////////////////////////////////////////////////////////////////

}else{
    echo 'data & method is required';
}

$USER->Logout();
    // echo "string";

    // https://bitrix.tdk.kiev.ua/local/lib/telephony/index.php?data[TYPE]=2&data[USER_ID]=1&data[DURATION]=351&data[CALL_ID]=externalCall.627642bbae47dcfa8839775f302b639c.1482338296&method=telephony.externalcall.finish&data[RECORD_URL]=http%3A%2F%2F195.3.158.43%2Fwav%2F2016-12-21%2F1482332759.41769-1197754236.mp3