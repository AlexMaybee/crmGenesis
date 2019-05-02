<?php
//Код приложения: local.58590130b74db5.20111453
//Ключ приложения: egFOMLRsoGKaLwmyr5D96YVQIKuxpSHABrQ9eNL0eDF3pBu5g2

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

    show_errors(true);
    bx_auth(3);

    $data = $_REQUEST['data'];
    $method = $_REQUEST['method'];
    // $result = bx_call('telephony.externalcall.register', $data,true);
    // $result = bx_call_get('crm.lead.list', ['filter' => array( ">ID" => 5 )],true);
    // $method = 'telephony.externalcall.register';
    $format = 'json';
    $url = 'https://'. PORTAL_NAME .'/rest/'. $method .'.'. $format .'?auth='. $GLOBALS['ACCESS_TOKEN'];
    $result = query("GET", $url, $data);
    echo $result;
    // echo "string";