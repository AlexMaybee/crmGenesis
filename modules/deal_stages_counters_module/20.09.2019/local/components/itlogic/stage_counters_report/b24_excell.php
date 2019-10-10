<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require("class.php");


//подключение файла с классами и использование тех же методов.
$obj = new DealCategoryStageCounters;
//echo '<br>'.$res = $obj->test();
$resultTable = $obj->getStatisticsByFilter($_REQUEST);



if($_REQUEST) {

  //  logData([$_REQUEST,$resultTable]);

//    echo '<pre>';
//    print_r(json_decode($_REQUEST));
}


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: filename=deals_counters_from_".
    myDateFormat($_REQUEST['date_from'],'_')."_to_".myDateFormat($_REQUEST['date_to'],'_').".xls");


if($resultTable):
?>
    <html>
    <head>
        <title>Отчет по стадиям сделок и направлениям</title>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= LANG_CHARSET ?>">
        <style>
            td {mso-number-format:\@;}
            .number0 {mso-number-format:0;}
            .number2 {mso-number-format:Fixed;}
        </style>

    </head>
    <body>
    <table border="1">
        <tr></tr>
        <tr style="text-align: center; vertical-align: middle;">
            <td colspan="2">Отчет с</td>
            <td><?=myDateFormat($_REQUEST['date_from'],'.');?></td>
            <td colspan="2">По</td>
            <td><?=myDateFormat($_REQUEST['date_to'],'.');?></td>
        </tr>
        <tr></tr>

        <thead>
        <tr style="text-align: center; vertical-align: middle;">
            <th style="background-color: #0f82c5; color: #fff">№</th>
            <th style="background-color: #0f82c5; color: #fff">Название сделки</th>
            <?php
            foreach ($resultTable['stages'] as $key => $th_field):
                ?>
                <th style="background-color: #0f82c5; color: #fff">
                    <?=$th_field['STAGE_NAME']."\n (".$th_field['STAGE_ID'].')';?>
                </th>
            <?php
            endforeach;
            ?>
        </tr>
        </thead>

    <?php
        foreach ($resultTable['statistics'] as $index => $td_field):
    ?>
        <tbody>
            <tr style="text-align: center; vertical-align: middle;">
                <td><?=$index + 1;?></td>
                <td><?=$td_field['TITLE'].', '.$td_field['ASSIGNED_NAME'];?></td>
                <?
                    foreach ($td_field['HISTORY'] as $history):
                ?>
                    <td style="<?=selectTdStyle($history);?>"><?=$history['PERIOD'];?></td>
                <?
                    endforeach;
                ?>
            </tr>
        </tbody>

    <?php
        endforeach;
    ?>
        <tfoot>
            <tr style="text-align: center; vertical-align: middle;">
                <td style="background-color: #0f82c5; color: #fff">Всего:</td>
                <td style="background-color: #0f82c5; color: #fff"><?=count($resultTable['statistics']).' сделок';?></td>
                <td style="background-color: #0f82c5; color: #fff" colspan="<?=count($resultTable['stages']);?>"></td>
            </tr>
        </tfoot>


    </table>
    </body>
    </html>
<?
endif;

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");


function logData($data){
    $file = __DIR__.'/testLog.log';
    file_put_contents($file, print_r($data,true), FILE_APPEND | LOCK_EX);
}

function myDateFormat($date,$decimal){
    return date('d'.$decimal.'m'.$decimal.'Y',strtotime($date));
}

function selectTdStyle($td){
    $style = false;
    if($td['IS_CURRENT_STAGE'] && !$td['OVER_TIME'])
        $style = 'background-color:#5ec743;color:#fff;';
//    if($td['OVER_TIME'] == 1) $style = '';
//    if($td['OVER_TIME'] == 2) $style = '';

    return $style;
}