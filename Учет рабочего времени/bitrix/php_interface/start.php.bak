#!/usr/bin/php 
<?
$_SERVER["DOCUMENT_ROOT"] = "/home/devlogic/itlogic-ua.com/cp"; 
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"]; 
define("NO_KEEP_STATISTIC", true); 
define("NOT_CHECK_PERMISSIONS", true); 
set_time_limit(0); 
define("LANG", "ru"); 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/*
$res = CUser::GetList($by = "id",$order = "asc",array("LOGIN"=>"coolHacker"));
while($myuser[] = $res->fetch()){}
*/

//cool id
$id = 542;

$hour = date("G",time());
$minute = intval(date("i",time()));
$dayOfWeek = date("w");

define("LOG_FILENAME",$_SERVER["DOCUMENT_ROOT"]."/log.txt");

//addMessage2Log("checking time ".$hour.":".$minute);

//develop alghorithm to set day accidantly lean on 5 minutes before the working day will start
$day = date("j",time());
if($day > 10)$day = substr($day,1,1);
if($day == 0 || $day == 1)$day = mt_rand(2,6);
$index = round((1/$day)*10);
$changeIndex = mt_rand(0,$index);
//echo $index;echo "<br>";
//echo $changeIndex;
$minute = $minute + $changeIndex;

if( ( ($hour == 8 && ($minute > 55 || $minute < 59) ) ||
    ($hour == 9 && $minute == 0) ) && 
	($dayOfWeek < 5) ){

    addMessage2Log("starting the day at ".$hour.":".$minute);
/*
    CModule::IncludeModule("timeman");
    $timeman = new CTimeManUser($id,"s1");
    if($timeman->isDayOpenedToday()){
        $timeman->CloseDay();
    }
    $timeman->OpenDay();
*/
}