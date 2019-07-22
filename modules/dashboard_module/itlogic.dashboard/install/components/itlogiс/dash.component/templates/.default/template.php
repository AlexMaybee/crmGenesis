<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->SetAdditionalCss("https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css");
$APPLICATION->SetAdditionalCss("https://fonts.googleapis.com/css?family=Indie+Flower");
$APPLICATION->AddHeadScript('https://code.jquery.com/jquery-3.3.1.slim.min.js');
$APPLICATION->AddHeadScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js');
$APPLICATION->AddHeadScript('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');





//echo $arResult['CUSTOM_DATE'];
?>
<div class="dashboard_container">

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="allByDeals-tab" data-toggle="tab" href="#allByDeals" role="tab" aria-controls="allByDeals" aria-selected="true">Основной, по сделкам</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="invoices-tab" data-toggle="tab" href="#invoices" role="tab" aria-controls="invoices" aria-selected="false">По счетам</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="allByDeals" role="tabpanel" aria-labelledby="allByDeals-tab">
            <? include_once (__DIR__.'/includes/company_and_integrators_and_proggers.php')?>
            <? include_once (__DIR__.'/includes/deals_project_in_work.php')?>
        </div>

        <div class="tab-pane fade show " id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
            <? include_once (__DIR__.'/includes/info_by_invoices.php')?>
        </div>

    </div>



</div>




<?
/*echo '<pre>';
print_r($arResult['test']);
echo '</pre>';*/




//echo '<pre>';
//print_r($arResult['integr']);
//echo '</pre>';

echo '<pre>';
//print_r($arResult['INTEGRATORS']);
//print_r($arResult['COMPANY']);
//print_r($arResult['ERROR']);
//print_r($arResult['PROGGERS']); // это старая версия рассчета часов проггера, закомменнчена в component.php

//print_r($arResult['PROGGERS_V2']);

//print_r($arResult['TEST']['ALL_DEALS']);
//print_r($arResult['TEST']['LIST_ELEMENTS']);

//print_r($arResult['PROJECTS_IN_WORK']);
//print_r($arResult['ALL_DEALS']);
//print_r($arResult['ALL_DEALS_HISTORY']);
//print_r($arResult['INVOICES_TEST_DEALS']);
//print_r($arResult['INVOICES_TEST']);
//print_r($arResult['POTENCIAL_INVOICES']);
//print_r($arResult['POTENCIAL_INVOICES_IN_DEALS']);
//print_r($arResult['POTENCIAL_INVOICES_DEALS']);
echo '</pre>';


//echo 'Фото: '.$photoMassive
?>


