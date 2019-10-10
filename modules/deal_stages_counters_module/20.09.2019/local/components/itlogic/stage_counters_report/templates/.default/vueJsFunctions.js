
//22.04.2019 Пагинация
Vue.component('pagination-template',{
    props:  {
        pages: '',
        current: {
            type: Number,
            default: 0,
        },
    },
    computed: {

        pagesTotal: function () {
            return this.pages
        },
        nextPage: function () {
            return this.current + 1;
        },
        prevPage: function () {
            return this.current - 1;
        },
    },
    methods: {
        changePage: function (page) {
            console.log(page);
            this.$emit('page-changed', page);
        },
        hasPrev: function () {
            return this.current > 0;
        },
        hasNext: function () {
            return this.current < this.pagesTotal;
        },
    },
    template:
        `<div id="pagination-template">
            <div class="pagination">
                <div class="pagination-left">
                <a href="#" @click="changePage(prevPage)">Предыдущая</a>
                </div>
                <div class="pagination-mid">
                    <ul>
                        <li v-for="n in Number(pagesTotal)"><a href="#">{{n}}</a></li>
                    </ul>
                    из {{pagesTotal}}
                </div>
                <div class="pagination-right">
                <a href="#" @click="changePage(nextPage)">Следующая</a>
                </div>
            </div>
        </div>`
});
//22.04.2019 Пагинация



let app = new Vue({
    el: '#filters',
    data: {

        filters: {
            action: '',
            category_id: '',//work
            assigned_by_id: 0,//work
            current_stage_id: '',//work
            only_opened_deals: true,//work
            date_from: '',//work
            date_to: '',
            on_page_num: false,
        },
        list:{
            categories: '',
            users: '',
            stages: '',
            stages_to_table_head: '',
            deals: '',
        },
        errorText: false,

        //22.04 добавление пагинации
        totalDeals: false,
        totatPages: false,
        dealsCountStartFrom: 0, //старт отсчета сделок от
        dealsNumberOnPage: [
            { num: '-' },
            { num: 10 },
            { num: 20 },
            { num: 50 },
            { num: 100 }
        ],

    },
    methods: {

        getCategories: function () {
            let self = this;

            this.filters.action = 'GIVE_ME_CATEGORIES_FOR_SELECT';

            BX.ajax({
                method: "POST",
                url: '/local/components/itlogic/stage_counters_report/ajax.php',
                data: self.filters,
                dataType: "json",
                onsuccess: function (response) {

                    self.list.categories = response;
                    self.filters.category_id = self.list.categories[0].ID; //присваиваем значение selected при загрузке, чтобы можно было сразу запустить загрузку таблицы
                   // console.log(self.list.categories);

                    self.filters.on_page_num = self.dealsNumberOnPage[0].num //присваиваем кол-во сделко на стр по умолч.

                    //Запрос всех стадий для селекта, т.к. уже присвоен ID категории в поле фильтра
                    self.getStagesList(self.filters.category_id);

                    let date = new Date(),
                        month, day;
                    if(date.getMonth() < 10) month = '0' + (date.getMonth()+1);
                    else month = date.getMonth()+1;

                    if(date.getDate() < 10) day = '0' + date.getDate();
                    else day = date.getDate();

                    self.filters.date_from = date.getFullYear() + '-' + month + '-' + day;
                    self.filters.date_to = date.getFullYear() + '-' + month + '-' + day;

                    //Вызываем функцию
                    self.getStatisticsByFilter(); //после присвоения значений фильтрам загружаем данные в таблицу с фильтрами по умолчанию.
                }
            });
        },

        getStatisticsByFilter: function(){
            let self = this;

            this.filters.action = 'GIVE_ME_STATISTICS_BY_CATEGORY_ID';

            BX.ajax({
                method: "POST",
                url: '/local/components/itlogic/stage_counters_report/ajax.php',
                data: self.filters,
                dataType: "json",
                onsuccess: function (response) {

              //      console.log(response);
                  //  console.log(self.filters.category_id,self.filters.assigned_by_id,self.filters.only_opened_deals,self.filters.date_from,self.filters.date_to,self.filters.current_stage_id);

                    //вывод стадий в шапку <th>
                    if(response.stages != false) self.list.stages_to_table_head = response.stages;
                    /*if(response.statistics != false)*/ self.list.deals = response.statistics;
                    self.totalDeals = response.deals_number_whole;
                    if(self.filters.on_page_num == '-' || self.filters.on_page_num == false){
                        self.totatPages = 1;
                    }
                    else self.totatPages = (self.totalDeals / self.filters.on_page_num).toFixed();
                   // console.log('Всего сделок:',self.totalDeals);
                   // console.log('Всего страниц:',self.totatPages);
                   // console.log('Сделок на странице:',self.filters.on_page_num);

                }
            });
        },

        getAssignedList: function () {
            let self = this;

            this.filters.action = 'GIVE_ME_ASSIGNED_LIST_FOR_SELECT';

            BX.ajax({
                method: "POST",
                url: '/local/components/itlogic/stage_counters_report/ajax.php',
                data: self.filters,
                dataType: "json",
                onsuccess: function (response) {
                   // console.log(response)
                    self.list.users = response;
                    self.filters.assigned_by_id = self.list.users[0].ID; //присваиваем значение selected при загрузке, чтобы можно было сразу запустить загрузку таблицы

                }
            });
        },
        getStagesList: function () {

         //   console.log('тест2',this.filters.category_id);

            this.filters.action = 'GIVE_ME_STAGES_LIST_FOR_SELECT';

            let self = this;
            BX.ajax({
                method: "POST",
                url: '/local/components/itlogic/stage_counters_report/ajax.php',
                data: self.filters,
                dataType: "json",
                onsuccess: function (response) {
                  //  console.log(response)
                     self.list.stages = response;
                     self.filters.current_stage_id = self.list.stages[0].ID; //присваиваем значение selected при загрузке, чтобы можно было сразу запустить загрузку таблицы

                }
            });
        },
        reFactorDatas: function (data) {
            return data.split('-').reverse().join('.');
        },
        createExcell: function(){

            //удаление Экшна псле получчения данных в таблице
           delete this.filters.action;

            //по фильтрам снова получаем теми же методами те же данніе на отдельную страницу
            let param = jQuery.param(this.filters);
            //console.log('test',param)
            // /local/components/itlogic/stage_counters_report/b24_excell.php
            window.open("/local/components/itlogic/stage_counters_report/b24_excell.php?" + param, "_blank");

        },

    },

    mounted: function () {
        this.getCategories();
        this.getAssignedList();
        //this.getStagesList(this.filters.category_id); // здесь не срабатывает, т.к. еще не присвоено значение полю filters.category_id

    },
});