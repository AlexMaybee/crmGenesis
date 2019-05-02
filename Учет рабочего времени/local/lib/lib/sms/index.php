<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$phone = '';
$text = 'TEST выавыаы віаіваіва';

// $phone = '+'.$_POST['phone'];
// $text = $_POST['text'];
$type ='submit_sm.php?';
$login = '&login='.LOGIN;
$passwd = '&passwd='.PASSWORD;
$alphaname = '&alphaname='.NAME;
$destaddr  = '&destaddr='.urlencode($phone);
pre($phone);
$msgtext  = '&msgtext='.urlencode($text);
$msgchrset  = '&msgchrset='.$msgchrset;
$url = 'http://api.smscentre.com.ua/http/'.$type.$login.$passwd.$alphaname.$destaddr.$msgtext.$msgchrset;


$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($curl);
pre($res);
pre(send_sms($phone, $text));