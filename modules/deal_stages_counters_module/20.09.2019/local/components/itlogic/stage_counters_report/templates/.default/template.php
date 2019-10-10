<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//Vue.js plugIn
$APPLICATION->AddHeadScript('/local/components/itlogic/stage_counters_report/templates/.default/vue.js');
//$APPLICATION->AddHeadScript('/local/components/itlogic/stage_counters_report/templates/.default/vue.min.js');
//\Bitrix\Main\UI\Extension::load("ui.vue"); // doesn't work here!
?>
    <section class="custom-stage-counters" id="filters">

        <h2 class="report-title">Отчет по счетчикам стадий сделок с
            <span class="date-span">{{reFactorDatas(filters.date_from)}} </span>
            по
            <span class="date-span">{{reFactorDatas(filters.date_to)}}</span>
        </h2>



    <div class="counter-filters" >

        <table>
            <tr>
                <td>
                    <label for="deal_category">Выберите направление:</label>
                </td>
                <td>
                    <select name='deal_category' v-model="filters.category_id" @change="getStatisticsByFilter()">
                        <option v-for="category in list.categories" v-bind:value="category.ID">{{category.NAME}}</option>
                    </select>
                </td>
                <td>
                    <label for="deal_category">Выберите ответственного:</label>
                </td>
                <td>
                    <select name='deal_category' v-model="filters.assigned_by_id" @change="getStatisticsByFilter()">
                        <option v-for="assigned in list.users" v-bind:value="assigned.ID">{{assigned.NAME}}</option>
                    </select>
                </td>

                <td>
                    <button class="shoot-excell" v-if="list.deals.length > 0" @click="createExcell()">Создать excell</button>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="deal_category">Выберите текущую стадию:</label>
                </td>
                <td>
                    <select name='deal_category' v-model="filters.current_stage_id" @change="getStatisticsByFilter()">
                        <option v-for="stage in list.stages" v-bind:value="stage.ID">{{stage.NAME}}</option>
                    </select>
                </td>
                <td>
                    <label for="only_opened">Только сделки в работе</label>
                </td>
                <td>
                    <input name="only_opened" v-model="filters.only_opened_deals" type="checkbox" @change="getStatisticsByFilter()" id="only_opened">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="date_from">Дата с:</label>
                </td>
                <td>
                    <input name="date_from" v-model="filters.date_from" type="date" @change="getStatisticsByFilter()">
                </td>
                <td>
                    <label for="date_to">Дата по:</label>
                </td>
                <td>
                    <input name="date_to" v-model="filters.date_to" type="date" @change="getStatisticsByFilter()">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="deals_number">Сделок на странице:</label>
                </td>
                <td>
                    <select name='deals_number' v-model="filters.on_page_num" @change="getStatisticsByFilter()">
                        <option v-for="deals in dealsNumberOnPage" v-bind:value="deals.num">{{deals.num}}</option>
                    </select>
                </td>
            </tr>
        </table>

    </div>


        <pagination-template
                :pages="totatPages"></pagination-template>


    <div class="table-wrapper-x-scroll">
        <table class="custom-table">
            <thead>
            <tr>
                <th>№</th>
                <th>Название сделки</th>
                <th v-for="value in list.stages_to_table_head">{{value.STAGE_NAME}} ({{value.STAGE_ID}})</th>
            </tr>
            </thead>
            <tbody>

            <tr v-if="totalDeals === 0">
                <td v-bind:colspan="2 + list.stages_to_table_head.length" class="zero-deals">{{totalDeals}} сделок по текущему фильтру!</td>
            </tr>
            <template v-else>
                <tr v-for="(deal,key) in list.deals" v-bind:title="deal.ASSIGNED_NAME">
                    <td>{{key + 1}}</td>
                    <td class="table-deal-name"><a v-bind:href="deal.HREF">{{deal.TITLE}}</a></td>
                    <td v-for="stage in deal.HISTORY"
                        v-bind:class="{ currentStage: stage.IS_CURRENT_STAGE && !stage.OVER_TIME, deal_overtime_10: stage.OVER_TIME === 1,deal_overtime_30: stage.OVER_TIME === 2}">
                        <!--{{stage.NAME}} - -->{{stage.PERIOD}}
                    </td>

                </tr>
                <tr class='whole-statistics'><td>Всего:</td><td >{{totalDeals}} сделок</td><td v-bind:colspan="list.stages_to_table_head.length"></td></tr>
            </template>

            </tbody>
        </table>
    </div>

</section>


    <script src="/local/components/itlogic/stage_counters_report/templates/.default/vueJsFunctions.js"></script>
<?
//echo '<pre>';
//print_r($arResult);
//echo '</pre>';