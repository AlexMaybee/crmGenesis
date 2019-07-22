<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->SetAdditionalCss("https://fonts.googleapis.com/css?family=Indie+Flower");

//Vue.js
$APPLICATION->AddHeadScript('/local/components/itlogic/dash_invoices.component/templates/.default/vue.js');
//\Bitrix\Main\UI\Extension::load("ui.vue");
?>


<section>


    <div id="company-user-plan-block">

        <h1 class="title">Показатели по сотрудникам, счетам и отработанным часам на <span class="date">{{page_title}}</span></h1>

        <ul class="tabs mb_30">
            <li class="tab active-tab">Главный<input type="radio" value="1" name="radiotabs" checked></li>
            <li class="tab">Счета<input type="radio" value="2" name="radiotabs"></li>
            <li class="tab">Программисты<input type="radio" value="3" name="radiotabs"></li>
            <li class="tab">Сделки в работе<input type="radio" value="4" name="radiotabs"></li>
        </ul>


        <div class="arrows_wrapper">
            <span class="previous_month" @click="minusOneMonth">{{prevMonth}}</span>
            <span class="next_month" @click="plusOneMonth">{{nextMonth}}</span>
        </div>


        <div class="error" v-if="analitic_error">{{analitic_error}}</div>
        <div v-else>


            <?//Вкладка № 1?>
            <div class="tab-content active-tab-content" id="tab_1">
                <? include_once (__DIR__.'/includes/mainTab1.php')?>
            </div>
            <?//Вкладка № 1?>

            <?//Вкладка № 2?>
            <div class="tab-content" id="tab_2">
                <? include_once (__DIR__.'/includes/invoicesTab2.php')?>
            </div>
            <?//Вкладка № 2?>


            <?//Вкладка № 3?>
            <div class="tab-content" id="tab_3">
                <? include_once (__DIR__.'/includes/proggersTab3.php')?>
            </div>
            <?//Вкладка № 3?>

            <?//Вкладка № 4?>
            <div class="tab-content" id="tab_4">
                <? include_once (__DIR__.'/includes/dealsInWorkTab4.php')?>
            </div>
            <?//Вкладка № 4?>

        </div>

    </div>

</section>

<!-- Vue.js functions-->
<script src="https://cdn.jsdelivr.net/npm/tween.js@16.3.4"></script>
<script src="/local/components/itlogic/dash_invoices.component/templates/.default/functionsVue.js"></script>
