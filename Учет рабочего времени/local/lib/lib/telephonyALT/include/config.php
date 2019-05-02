<?php

session_start();

/**
 * client_id приложения
 */
define('CLIENT_ID', 'local.58590130b74db5.20111453');
/**
 * client_secret приложения
 */
define('CLIENT_SECRET', 'egFOMLRsoGKaLwmyr5D96YVQIKuxpSHABrQ9eNL0eDF3pBu5g2');
/**
 * относительный путь приложения на сервере
 */
define('PATH', '/local/lib/telephony/index.php');
/**
 * полный адрес к приложения
 */
define('REDIRECT_URI', 'https://cp.itlogic.biz' . PATH);
/**
 * scope приложения
 */
define('SCOPE', 'crm,user,telephony,call');

/**
 * протокол, по которому работаем. должен быть https
 */
define('PROTOCOL', "https");

define('PORTAL_NAME', 'cp.itlogic.biz');

/**
 * Производит перенаправление пользователя на заданный адрес
 *
 * @param string $url адрес
 */
function redirect($url) {
    Header("HTTP 302 Found");
    Header("Location: " . $url);
    die();
}

/**
 * Совершает запрос с заданными данными по заданному адресу. В ответ ожидается JSON
 *
 * @param string $method GET|POST
 * @param string $url адрес
 * @param array|null $data POST-данные
 *
 * @return array
 */
function query($method, $url, $data = null) {
    $query_data = "";

    $curlOptions = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => "admin:eq#5G3lR3f",
        CURLOPT_SSL_VERIFYPEER => false
    );

    if ($method == "POST") {
        $curlOptions[CURLOPT_POST] = true;
        $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
    } elseif (!empty($data)) {
        $url .= strpos($url, "?") > 0 ? "&" : "?";
        $url .= http_build_query($data);
    }

    $curl = curl_init($url);
    curl_setopt_array($curl, $curlOptions);
    $result = curl_exec($curl);

    return json_decode($result, 1);
}

/**
 * Вызов метода REST.
 *
 * @param string $domain портал
 * @param string $method вызываемый метод
 * @param array $params параметры вызова метода
 *
 * @return array
 */
function call($domain, $method, $params) {
    return query("POST", PROTOCOL . "://" . $domain . "/rest/" . $method, $params);
}

function pre($val){
    if(!$val){
        echo "<pre>";
        var_dump($val);
        echo "</pre>";
    }else{
        echo "<pre>";
        print_r($val);
        echo "</pre>";
    }
}
