BX.ready(function() {
    let obj = new WorkPanelControl();
});

class WorkPanelControl{

    constructor(){
        let self = this;

        this.ajaxUrl = '/local/modules/crmgenesis.worktimecontrol/ajax.php';

        BX.addCustomEvent("onAjaxSuccess", function(data,config){
            if(data.PLANNER){
                // console.log('DATA:',data);

                self.checkHoursWorked(data);
                self.getDataForPopupFields();
            }
        });
    }

    //НЕ ДОПИСАН до конца, пока существует баг с раб. днем.
    checkHoursWorked(data){
        //здесь аякс-запрос и подсчет по кол-ву часов из элементов за unicodeDate день.
        // console.log('unicodeDate:',data.INFO.DATE_START);

        let self = this, //иначе не получится вызвать нужный метод класса
            cssVal = {};

        BX.ajax({
            method: "POST",
            url: self.ajaxUrl,
            data: {'ACTION':'COUNT_WORKED_HOURS_IN_CUURRENT_DAY','UNICODE_DATE_START':data.INFO.DATE_START,'UNICODE_DATE_FINISH':data.INFO.DATE_FINISH},
            dataType: "json",
            onsuccess: function (data) {

                // console.log(data);

                $('#popup-window-content-timeman_main .my-workday-error').remove();

                if($('#popup-window-content-timeman_main .webform-small-button-decline').length > 0
                    && data.result == false){
                    cssVal = {'pointer-events':'none','opacity':'0.5'};


                    if(data.error != false){
                        $('#popup-window-content-timeman_main .tm-popup-timeman-layout')
                            .after('<div class="my-workday-error">' + data.error + '</div>');
                    }

                }
                else{
                    cssVal = {'pointer-events':'auto','opacity':'1'};
                }

                $('#popup-window-content-timeman_main .tm-webform-button-play').css(cssVal);
                $('#popup-window-content-timeman_main .tm-webform-button-pause').css(cssVal);
                $('#popup-window-content-timeman_main .tm-popup-button-handler').css(cssVal);
                $('#popup-window-content-timeman_main .tm-popup-change-time-link').css(cssVal);

            }
        });

    }

    getDataForPopupFields(){

        let self = this; //иначе не получится вызвать нужный метод класса

        BX.ajax({
            method: "POST",
            url: self.ajaxUrl,
            data: {'ACTION':'GIVE_ME_LIST_FIELDS_AND_VALUES'},
            dataType: "json",
            onsuccess: function (data) {
                if(data.fields.length > 0){
                    self.addCustomTaskTimeTab(data.fields);
                }
                else console.log('Error:',data.error);
            }
        });
    }


    addCustomTaskTimeTab(fieldsData){
        let mdiv = $('#popup-window-content-timeman_main').find('.tm-tabs-box'),
            tab = $('#popup-window-content-timeman_main .task-time-tab'),
            content_tab = $('#popup-window-content-timeman_main .task-time-content'),
            allTabs = $('#popup-window-content-timeman_main .tm-tab'),
            allTabsContent = $('#popup-window-content-timeman_main .tm-tab-content'),
            fieldForForm = '', //переменная с разметкой полей
            required = '', //свойство
            requiredField = '', //красная звездочка - єлемент
            options = '<option value="">Не выбрано</option>', //для множ. значений
            self = this; //иначе не получится вызвать нужный метод класса;

        if(mdiv.length > 0){

            //добавление таба .tm-tabs и контента tm-tabs-content
            if(tab.length == 0){
                mdiv.find('.tm-tabs').append('<span class="tm-tab task-time-tab">Учет времени</span>');
            }
            if(content_tab.length == 0){

                $.each(fieldsData, function (num,field) {
                    // console.log(num,field);
                    if(field.CODE == 'SOTRUDNIK') return;

                    (field.IS_REQUIRED == 'Y') ? required = 'required' : required = '';
                    if(field.IS_REQUIRED == 'Y') {
                        requiredField = '<span class="required">*</span>';
                        required = 'required';
                    }
                    else{
                        requiredField = '';
                        required = '';
                    }

                    //если множ., то выводим в селект
                    if(field.PROPERTY_TYPE == 'L'){
                        $.each(field.VALUES, function (f,value) {
                            options += '<option value="' + value.VALUE + '">' + value.TEXT + '</option>';
                        });

                        fieldForForm += '<div class="tm-popup-task-form">' +
                            '<label for="' + field.CODE + '">' + field.NAME + ' ' + requiredField + '</label>' +
                            '<select ' +
                            'id="' + field.CODE + '" ' +
                            'name="' + field.CODE + '" ' +
                            required + ' ' +
                            'class="tm-popup-task-form-textbox bx-focus">' +
                            options +
                            '</select></div>';
                    }
                    else{

                        //для поля комментарий - текстареа
                        if(field.CODE == 'KOMMENTARIY'){
                            fieldForForm += '<div class="tm-popup-task-form">' +
                                '<label for="' + field.CODE + '">' + field.NAME + ' ' + requiredField + '</label>' +
                                '<textarea ' +
                                'id="' + field.CODE + '" ' +
                                'name="' + field.CODE + '" ' +
                                required + ' ' +
                                'class="tm-popup-task-form-textbox bx-focus"></textarea>' +
                                '</div>';
                        }
                        else{

                            //для даті полле типа даті
                            if(field.USER_TYPE == 'Date'){
                                fieldForForm += '<div class="tm-popup-task-form">' +
                                    '<label for="' + field.CODE + '">' + field.NAME + ' ' + requiredField + '</label>' +
                                    '<input ' +
                                    'id="' + field.CODE + '" ' +
                                    'value="' + self.getcurrentTime() + '" ' +
                                    'type="date" ' +
                                    'name="' + field.CODE + '" ' +
                                    required + ' ' +
                                    'class="tm-popup-task-form-textbox bx-focus">' +
                                    '</div>';
                            }

                            //для остального - тип текст
                            else{
                                fieldForForm += '<div class="tm-popup-task-form">' +
                                    '<label for="' + field.CODE + '">' + field.NAME + ' ' + requiredField + '</label>' +
                                    '<input ' +
                                    'id="' + field.CODE + '" ' +
                                    'type="text" ' +
                                    'name="' + field.CODE + '" ' +
                                    required + ' ' +
                                    'class="tm-popup-task-form-textbox bx-focus">' +
                                    '</div>';
                            }
                        }
                    }
                });


                mdiv.find('.tm-tabs-content').append('<div class="tm-tab-content task-time-content">' +
                    '<div class="tm-popup-report">' +
                    '<form id="taskTimeAtWorkPanel" onsubmit="return false">' +
                    '<div class="tm-popup-report-text">' +

                    '<div class="tm-popup-task-form">' +
                    '<label for="HOURS">Часы</label>' +
                    '<input id="HOURS" name="HOURS" required class="tm-popup-task-form-textbox bx-focus">' +
                    '</div>' +

                    fieldForForm +
                    '<input type="hidden" name="PROEKT_ID">' + // value="2023"
                    '</div>' +
                    '<div class="tm-popup-report-buttons">' +
                    '<span class="task-time-button-panel-add ui-btn" ' +
                    'style="display:inline-block!important;background-color:#ad1236;color:#fff">Save' +
                    '</span></div>' +
                    '</form></div></div>');
            }

            //Работа с дабами и контентом
            $('.tm-tab').click(function () {

                if($(this).hasClass('task-time-tab')){
                    $.each(allTabs, function (index,elem) {
                        if($(elem).hasClass('tm-tab-selected'))
                            $(elem).removeClass('tm-tab-selected');
                    });

                    $.each(allTabsContent, function (index,elem) {
                        if($(elem).hasClass('tm-tab-content-selected'))
                            $(elem).removeClass('tm-tab-content-selected');
                    });
                    $(this).addClass('tm-tab-selected');
                    $('.task-time-content').addClass('tm-tab-content-selected');
                }
                else{
                    $('.task-time-tab').removeClass('tm-tab-selected');
                    $('.task-time-content').removeClass('tm-tab-content-selected');
                }


            });

            //валидация
            $('.task-time-button-panel-add').click(function () {
                self.validateForm();
            });

            //добавляем title
            $('#PROEKT').attr('title','Поиск происходит по словам в названии или полном ID сделки!');

            //поиск сделки по названию
            $('#PROEKT').keyup(function () {
                self.getDealsListByTitle();
            });
        }
    }

    validateForm(){

        //удаление ошибок
        $('#taskTimeAtWorkPanel .myPanelError').remove();
        $('#taskTimeAtWorkPanel select, #taskTimeAtWorkPanel input, #taskTimeAtWorkPanel textarea').css('border-color','#c6cdd3');

        let data = $('#taskTimeAtWorkPanel').serializeArray(),
            fields = {}
        self = this;
        $.each(data, function (i,field) {
            fields[field.name] = field.value;
        });

        // console.log("Валидация:", fields);

        //часы - RegExp
        if(fields.HOURS.search(/^[1-9]\d*(\.\d+)?(\,\d+)?$/i) == -1)
            this.showError('#HOURS','Укажите количество отработанных часов!');

        //дата
        if(fields.DATA123.search(/^[\d]{4}-[\d]{2}-[\d]{2}$/i) == -1)
            this.showError('#DATA123','Укажите дату в формате дд.мм.гггг!');

        //сделка
        if(fields.PROEKT_ID.search(/^[\d]+$/i) == -1)
            this.showError('#PROEKT','Выберите сделку!');

        //роль
        if(fields.ROL.search(/^[\d]+$/i) == -1)
            this.showError('#ROL','Выберите роль!');

        //типовые работы
        if(fields.TIPOVYE_RABOTY.search(/^[\d]+$/i) == -1)
            this.showError('#TIPOVYE_RABOTY','Выберите роль!');

        else {
            if($('.myPanelError').length < 1){

                console.log('Ошибок нет, создаем элемент списка учета времени!');
                self.createTaskTimeElement(fields);
            }
        }
    }

    showError(selector,text) {
        $(selector).css('border-color','red');
        $(selector).after('<div class="myPanelError">' + text + '</div>');
    }

    createTaskTimeElement(fields){
        let self = this;

        fields.ACTION = 'CREATE_NEW_TASK_TIME_ELEMENT';
        BX.ajax({
            method: "POST",
            url: self.ajaxUrl,
            data: fields,
            dataType: "json",
            onsuccess: function (data) {

                // console.log(data);

                if(data.result != false){
                    $('#taskTimeAtWorkPanel')[0].reset();
                    $('#DATA123').val(fields.DATA123);
                }
                else{
                    $('#HOURS').closest('.tm-popup-task-form').before('<div class="myPanelError">' + data.error + '</div>');
                }
            }
        });
    }

    //формирование текущей даты
    getcurrentTime(){

        let date = new Date(),
            month,
            day;

        if(date.getMonth() < 9) month = '0' + (date.getMonth()+1);
        else month = date.getMonth()+1;

        if(date.getDate() < 10) day = '0' + date.getDate();
        else day = date.getDate();

        return date.getFullYear() + '-' + month + '-' + day;;
    }

    getDealsListByTitle(){

        let dealTitle = $('#PROEKT').val(),
            self = this,
            dealsList = '';

        //при каждом измменении текста очищаем поле с ID сделки
        $('#taskTimeAtWorkPanel input[name="PROEKT_ID"]').val('');

        BX.ajax({
            method: "POST",
            url: self.ajaxUrl,
            data: {'ACTION':'GIVE_ME_DEALS_LIST_BY_TITLE','TITLE':dealTitle},
            dataType: "json",
            onsuccess: function (data) {

                if(data.result != false){

                    $.each(data.result,function (k,deal) {
                        dealsList += '<li data-d="' + deal.ID + '">' +  deal.TITLE + '</li>';
                    })

                    //если блока нет, то создаем
                    if($('#taskTimeAtWorkPanel .deals-list').length < 1){
                        $('#PROEKT').closest('.tm-popup-task-form')
                            .append('<div class="deals-list"><ul>' + dealsList + '</ul></div>');
                    }
                    //если блок уже есть, то сравниваем уже заполненій html и вновь пришедший, если что, заменяем
                    else {
                        // console.log( $('#taskTimeAtWorkPanel .deals-list ul').html(),dealsList);
                        if($('#taskTimeAtWorkPanel .deals-list ul').html() == dealsList)
                            return;
                        else
                            $('#taskTimeAtWorkPanel .deals-list ul').html(dealsList);
                    }

                    $('#taskTimeAtWorkPanel .deals-list ul li').click(function () {
                        // console.log('Кликнул на:',$(this).attr('data-d'));

                        //передача объекта в функцию, чтобы не передавать 2 параметра
                        self.selectDeal($(this));
                    });
                }
                else{
                    $('#taskTimeAtWorkPanel .deals-list ul').empty();
                    $('#taskTimeAtWorkPanel .deals-list').remove();
                }
            }
        });
    }

    selectDeal(obj){

        //получаю объект и из него нужную инфу
        if($(obj).attr('data-d') > 0){
            $('#taskTimeAtWorkPanel input[name="PROEKT_ID"]').val($(obj).attr('data-d'));
            $('#taskTimeAtWorkPanel input[name="PROEKT"]').val($(obj).text());
            $('#taskTimeAtWorkPanel .deals-list ul').empty();
            $('#taskTimeAtWorkPanel .deals-list').remove();
        }
    }

}