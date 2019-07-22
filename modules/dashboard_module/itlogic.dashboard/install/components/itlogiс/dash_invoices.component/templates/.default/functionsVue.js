

//компоненты

//1 Компонент инфы блока
Vue.component('block-hitle',{
    props:{
        title: '',
        textLink: '',
        status: '',
        photo: '',
        userName: '',
        userPosition: '',
        userHours: '',
        userHoursColor: '',
        photoTitle: '',
        vzyatka: '',
    },
    methods: {
        selectHoursColor: function (percent) { // перенес эту функцию из php
            var resultColor;
            switch (true){
                case (percent < 50):
                    resultColor = '#e00808';
                    break;
                case (50 <= percent && percent < 100):
                    resultColor = '#fd8d02';
                    break;
                case (100 <= percent && percent < 150):
                    resultColor = '#28b305';
                    break;
                case (150 <= percent && percent < 200):
                    resultColor = '#fd8d02';
                    break;
                case (200 <= percent):
                    resultColor = '#e00808';
                    break;
                default:
                    resultColor = '#000';
                    break;
            }
            return resultColor;
        }
    },
    computed: {
        hoursColor: function () {
            return this.selectHoursColor(this.userHours);
        }
    },
    template:
            `<div class="block-title">
                <div class="block-title-flex">
                    <div class="photo" v-if="photo">
                        <img :src="photo" alt="user photo" :title="photoTitle">
                    </div>
                    <div class="info" v-if="title">
                        <div class="user_name" v-if="textLink">
                            <template v-if="status"><a :href="textLink">{{title}}  -  <span class="status">{{status}}</span></a>
                            <span v-if="vzyatka" class="error">НЕ забудь поделиться!</span>
                            </template>
                            <template v-else><a :href="textLink">{{title}}</a>
                            <span v-if="vzyatka" class="error"> - НЕ забудь поделиться!</span>
                            </template>
                        </div>
                        <div class="user_name" v-else>{{title}}</div>
                    </div>
                    <div class="info" v-if="userPosition">
                        <div class="user_name">{{userName}}</div>
                        <div class="user_position">{{userPosition}}</div>
                        <div class="user_hours_worked" :style="{color: hoursColor}"> {{userHours}} hours</div>
                    </div>
                </div>
            </div>`,
});

//1.2 Компонент со списками - для дерева = сделка => счета => прогрессбары, включает в себя другой компонент вместо <slot>
Vue.component('block-hitle-lists',{
    template:
        `<div class="block-list-wrapper">
            <div class="block-title-list">Счета:</div>
            <div class="block-list-elements">
                <slot></slot>
            </div>
        </div>`,
})

//1.3 Включаемый компонент со списком для итерации
Vue.component('block-list-elements',{
    props: {
       /* element: {},*/
        title: '',
        photo: '',
        photoTitle: '',
        link: '',
        status: '',
        elements: '',
        price: '',
    },
   /* data(){
        return {
            object: this.element,
        }
    },*/
    template:
        `<ul class="inline-block">
            <li>
                
                <div class="invoice-info">
                    <div>
                        <img alt="Assigned Photo"
                        :src="photo"
                        :title="photoTitle">
                    </div>
                    <div style="max-width: 40%">
                        <a :href="link" target="_blank">{{title}}</a> 
                    </div>
                    -
                    <div>
                        <span>{{price}}</span>
                    </div>
                    -
                    <div>
                        <span class="status">
                        {{status}}
                        </span>
                    </div>
                </div>
                                 
            </li>
        </ul>`
});

//1.4 Компонент заголовка для сделок в работе
Vue.component('deal-title-component',{
    props: {
        title: '',
        photo: '',
        photoTitle: '',
        link: '',
        timeRest: '',
        plan: '',
        fact: '',
        measureCode: '',
    },
    methods: {
        selectRestHoursColor: function (fact,plan) { // перенес эту функцию из php
            var resultColor;
            switch (true){
                case (plan < fact):
                    resultColor = '#e00808';
                    break;
                case ((0 < fact && fact < plan) || (plan/4 <= fact && fact < plan/2)):
                    resultColor = '#c5c70b';
                    break;
                case (plan/2 <= fact && fact < plan):
                    resultColor = '#fd8d02';
                    break;
                case (fact == plan):
                    resultColor = '#28b305';
                    break;
                default:
                    //resultColor = '#000';
                    resultColor = '#60d052';
                    break;
            }
            return resultColor;
        },
        selectTitleToHours: function (fact,plan) {
            var timeRestTitle;
            switch (true) {
                case (plan < fact):
                    timeRestTitle = 'превышение';
                    break;
                case (fact == plan):
                    timeRestTitle = 'выполнено за';
                    break;
                default:
                    timeRestTitle = 'осталось';
                    break;
            }
            return timeRestTitle;
        }
    },
    computed: {
        hoursColor: function () {
            return this.selectRestHoursColor(this.fact,this.plan);
        },
        timeRemainTitle: function () {
            return this.selectTitleToHours(this.fact,this.plan);
        }
    },
    template:
        `<div class="deal-title">
                <div class="photo">
                    <img alt="Assigne BY Photo"
                    :src="photo" 
                    :title="photoTitle">
                </div>
                <div>
                    <a :href="link">{{title}}</a> -
                </div>
                <div>
                    <span :style="{color: hoursColor}">{{timeRemainTitle}}: {{timeRest + ' ' + measureCode}}  </span> -
                </div>
                <div>
                    <span>факт/план</span>
                    <span>{{fact + ' / ' + plan + ' ' + measureCode}}</span>
                </div>
            </div>`
});

//1.5 Компонент для отображения роли и всех фоток
Vue.component('deal-role-title',{
    props:{
        title: '',
    },
    template:
        `<div class="block-title">
                <div class="block-title-flex">
                    
                    <div class="info">
                        <div class="user_name">{{title}}</div>
                    </div>
                    
                    <div class="photo photo-collection">
                        <slot></slot>
                    </div>
                    
                </div>
        </div>`
});

//2 Компонент прогрессбара
Vue.component('block-rogress',{
    props: {
        percent: '',
        plan: '',
        fact: '',
        colorpg: '', // удалить

    },
    data: function(){
        return{
            startValue: 0,
        }
    },
    watch: {
        percent: function () {
            this.animatePG();
        },
    },
    computed: {
        mypercent: function () {
            return this.startValue.toFixed();
        },
        progressColor: function () {
            return this.selectProgressColor(this.mypercent);
        },
        selectTemplate: function () {
            return this.chooseTemplate(this.percent);
        },

    },
    methods: {
        animatePG: function () {
            var vm = this;
            var id = setInterval(function () {
                if(vm.startValue < Math.round(vm.percent,1)) {
                    // console.log('LEss',vm.startValue,vm.percent);
                    return vm.startValue++;
                }
                else if(vm.startValue >  Math.round(vm.percent,1)){
                    //console.log('Bigger',vm.startValue,vm.percent);
                    return vm.startValue--;
                }
                else clearInterval(id);
            },50)
        },
        selectProgressColor(percent){
            var progressColor;
            switch (true){
                case percent < 30:
                    progressColor = '#ff0a0a';
                    break;
                case (30 <= percent && percent < 50):
                    progressColor = '#f3b507';
                    break;
                case (50 <= percent && percent < 70):
                    progressColor = '#07a7f3';
                    break;
                case (70 <= percent && percent < 99):
                    progressColor = '#39ea46';
                    break;
                case 99 <= percent:
                    progressColor = '#48b919';
                    break;
                default:
                    progressColor = '#000';
                    break;
            }
            return progressColor;
        },
        chooseTemplate(percent){
            var templ;
            switch (true){
                case (0 < percent && percent <= 100):
                    templ = 1;
                    break;
                case (percent > 100):
                    templ = 2;
                    break;
                default:
                    templ = 0;
                    break;
            }
            return templ
        },
    },
    mounted: function () {
        this.animatePG();
    },
    template:
        `<div class="block-progress">

                     <div class="progress-outer" v-if="selectTemplate == 1" :title=" 'Получено ' + fact + 'грн. из ' + plan">
                        <div class="progress-width progress-width-start"  :style="{width: mypercent +'%', backgroundColor: progressColor}">
                        </div>
                        <div class="progress-inner-zero">{{mypercent}} %</div>
                    </div>
                    <div class="progress-outer" v-else-if="selectTemplate == 2" :title=" 'Получено ' + fact + 'грн. из ' + plan">
                        <div class="progress-width progress-width-start"  :style="{width: mypercent  +'%', backgroundColor: progressColor}">
                        </div>
                        <div class="progress-width" 
                        :style="{width: (mypercent - 100)  +'%', backgroundColor: 'orange', borderTopRightRadius: '4px', borderBottomRightRadius: '4px'}">
                        </div>
                        <div class="progress-inner-zero">{{mypercent}} %</div>
                    </div>
                    <div class="progress-outer" style="position: relative" v-else :title=" 'Получено ' + fact + ' грн. из ' + plan">
                        <div class="progress-width progress-width-start" :style="{width: mypercent +'%', backgroundColor: progressColor}">
                        </div>
                        <div class="progress-inner-zero">{{mypercent}} %</div>
                    </div>             

                </div>`
});


//3 Компонент план/факт (крайний правый)
Vue.component('block-bumbers',{
    props: [
        'plan',
        'fact',
        'measureCode',
    ],
    template:
    `<div class="block-numbers">
        <div class="block-numbers-flex">
            <div class="fact">{{fact + ' / ' + plan + ' ' + measureCode}}</div>
        </div>
    </div>`
});



//остальной код
var users = new Vue({
    el: '#company-user-plan-block',
    data: {
        analitic_error: false,
        page_title: '',
        month: 0,
        year: 0,

        company: '',
        users: '',
        unpayed_invoices: '',
        proggers: '',
        deals_on_realization: '',


        showHiddenAnalitics: false, //Удалить?!

    },
    methods: {
        getUsersData: function () {
            let self = this;
            BX.ajax({
                method: "POST",
                url: '/local/components/itlogic/dash_invoices.component/ajax.php',
                data: {'ACTION':'GIVE_ME_USERS_DATA','MONTH': this.month,'YEAR': this.year},
                dataType: "json",
                onsuccess: function (res) {
                    self.analitic_error = res.error;
                    self.page_title = res.company.DATA;
                    self.company = res.company;
                    self.unpayed_invoices = res.unpayed_invoices;
                    self.users = res.analitics;
                    self.proggers = res.proggers;
                    self.deals_on_realization = res.deals_on_realization;

                    console.log(res);
                    //console.log(self.company);

                }
            });
        },
        setCurDate(){
            var d = new Date;
            this.month = d.getMonth() + 1;
            this.year = d.getFullYear();

        },
        plusOneMonth(){

            if(this.month == 12){
                this.month = 1;
                this.year++;
            }
            else {
                this.month++;
            }
            this.getUsersData();

        },
        minusOneMonth(){

            if(this.month <= 1){
                this.month = 12;
                this.year--;
            }
            else{
                this.month--;
            }

            this.getUsersData();
         //   console.log(this.month + ' ' + this.year)
        },

    },
    computed: {
        prevMonth: function () {
            var mon, yea;
            if(this.month <= 1){
                mon = 12;
                yea = this.year - 1;
            }
            else{
                mon = this.month - 1;
                yea = this.year;
            }
            return 'Назад на ' + mon + '.' + yea;
        },
        nextMonth: function () {
            var mon, yea;
            if(this.month == 12){
                mon = 12;
                yea = this.year + 1;
            }
            else{
                mon = this.month + 1;
                yea = this.year;
            }
            return 'Вперед на ' + mon + '.' + yea;
        },
    },
    mounted: function () {
        this.setCurDate();
        this.getUsersData();
    },

});





