//Это файл для отображения кнопки списания времени в задаче + попап для нее

BX.ready(function() {

    // console.log('Тестовая запись для вывода кнопки!');
    let personButton = new TaskTimeButtonAdd();

});


class TaskTimeButtonAdd{

    constructor(){


        this.url = window.location.href;

        this.isTaskOpened = this.chechUrlIfTaskOpened(this.url);


        if(typeof(this.isTaskOpened[2]) != undefined && this.isTaskOpened[2] > 0){
            this.showButton(this.isTaskOpened[1],this.isTaskOpened[2]); //передаю id текущего польз. (на всяк случай) + id задачи
        }
        else console.log('Не та страница, чтобы показывать кнопку для списания времени!');


      //  console.log('класс TaskTimeButtonAdd, детка!',this.isTaskOpened);
    }

    //функция сверки url, что он соотв. открытой задаче
    chechUrlIfTaskOpened(urlStr){
        var matchMassive;
        if(matchMassive = urlStr.match(/\/company\/personal\/user\/([\d]+)\/tasks\/task\/view\/([\d]+)/i)){
            return matchMassive[1] > 0 ? matchMassive : false; //в массиве 0 - url, 1 - current user id, 2 - task id
        }
        else return false
    }

    showButton(userId,taskId){
      //  console.log(userId,taskId);

        var self = this;

        BX.ajax({
            method: "POST",
            url: '/local/lib/taskTime/ajax/BestClass.php',
            data: {'CURRENT_USER_ID':userId,'TASK_ID':taskId,'action':'GIVE_ME_CURRENT_TASK_DATA'},
            dataType: "json",
            onsuccess: function (data) {

                //console.log(data);

                if(data.result != false){
                    self.addTaskTimeButton(data.result);
                }
                else{
                    console.log(data.message);
                }

            }
        });

    }

    addTaskTimeButton(deal_id){
       // var mdiv = document.getElementsByClassName('task-view-button complete'),
        var mdiv = document.getElementsByClassName('task-view-buttonset'),
            bp, inText, elemTitle, background,
            self = this; //иначе не получится вызвать нужный метод класса

        if(mdiv != null){
            bp = document.createElement('span');
            bp.className = 'task-time-button-add ui-btn';
            bp.innerHTML = 'Учет времени';
            bp.onclick = function () {

                self.getDataForPopupFields(deal_id);

                //self.showPopup(deal_id);
            }
            bp.style.cssText = 'display: inline-block!important;background-color: #ad1236; color: #fff';
           // mdiv[0].before(bp);
            mdiv[0].append(bp);
        }
    }


    getDataForPopupFields(deal_id){
        let self = this; //иначе не получится вызвать нужный метод класса

        BX.ajax({
            method: "POST",
            url: '/local/lib/taskTime/ajax/BestClass.php',
            data: {'DEAL_ID':deal_id,'action':'GIVE_ME_DATA_FOR_TASK_TIME_POPUP'},
            dataType: "json",
            onsuccess: function (data) {

               // console.log(data);

                if(data.errors == false){
                    self.showPopup(data.result);
                }
                else{
                    $.each(data.errors, function (index,error) {
                        console.log(error);
                    });
                }

            }
        });
    }


    showPopup(data) {

        var self = this;
        
        var Dialog = new BX.CDialog({
            title: "Заполнение учета рабочего времени",
            head: 'Заполните поля ниже',
            content: `<form method="POST" id="FormAddTaskTimeElem">
            <!--<div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-text" data-cid="DEAL_NAME"><a href="#">сделка</a></div>-->
            <div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-date" data-cid="DATE_TIME"><div class="crm-entity-widget-content-block-title"><span class="crm-entity-widget-content-block-title-text">Дата <span class="required">*</span></span></div><div class="crm-entity-widget-content-block-inner"><span class="fields datetime field-wrap"><span class="fields datetime field-item"><input onclick="BX.calendar({node: this, field: this, bTime: true, bSetFocus: false, bUseSecond: true})" name="DATE_TIME" type="text" tabindex="0" value=""><i class="fields datetime icon" onclick="BX.calendar({node: this.previousSibling, field: this.previousSibling, bTime: true, bSetFocus: false, bUseSecond: true});"></i></span></span></div></div>
            
            <div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-text" data-cid="TIME"><div class="crm-entity-widget-content-block-title"><span class="crm-entity-widget-content-block-title-text">Часы <span class="required">*</span></span></div><div class="crm-entity-widget-content-block-inner"><span class="fields string field-wrap"><span class="fields string field-item"><input size="20" class="fields string" name="TIME" tabindex="0" type="text" value=""></span></span></div></div>
            <div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-select" data-cid="ROLE"><div class="crm-entity-widget-content-block-title"><span class="crm-entity-widget-content-block-title-text">Роль <span class="required">*</span></span></div><div class="crm-entity-widget-content-block-inner"><span class="fields enumeration field-wrap"><span class="fields enumeration enumeration-select field-item"><select name="ROLE" tabindex="0"></select></span></span></div></div>
            <div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-select" data-cid="WORKS_TYPE"><div class="crm-entity-widget-content-block-title"><span class="crm-entity-widget-content-block-title-text">Типовые работы <span class="required">*</span></span></div><div class="crm-entity-widget-content-block-inner"><span class="fields enumeration field-wrap"><span class="fields enumeration enumeration-select field-item"><select name="WORKS_TYPE" tabindex="0"></select></span></span></div></div>
            <div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-text" data-cid="WORKS_DESCRIPTION">
            <div class="crm-entity-widget-content-block-title"><span class="crm-entity-widget-content-block-title-text">Описание работ <span class="required">(лучше заполнить)</span></span>
            </div><div class="crm-entity-widget-content-block-inner"><span class="fields string field-wrap">
            <span class="fields string field-item"><textarea cols="20" rows="2" class="fields string" name="WORKS_DESCRIPTION" tabindex="0" id="WORKS_DESCRIPTION"></textarea></span></span></div></div>

            <input name="DEAL_ID" type="hidden">
            <input name="DEAL_NAME" type="hidden">
            </form>`, // <div style="pointer-events: none;opacity: 0.5;" class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-text" data-cid=BALANCE"><div class="crm-entity-widget-content-block-title"><span class="crm-entity-widget-content-block-title-text">Баланс клиента до проведения текущей операции</span></div><div class="crm-entity-widget-content-block-inner"><span class="fields string field-wrap"><span class="fields string field-item"><input size="20" class="fields string" name="BALANCE" tabindex="0" type="text" value=""></span></span></div></div>
            icon: 'head-block',
            resizable: true,
            draggable: '400',
            width: '500',
            height: '500',
        });

        //кнопки
        Dialog.SetButtons([
            {
                'title': 'Сохранить',
                'id': 'popupSubmitSave',
                'name': 'popupSubmit',
                'action': function(){

                    //Функция валидации Полей!!!
                    self.taskTimePopUpValidate();

                }
            },
            // BX.CDialog.btnClose,
            {
                'title': 'Отмена',
                'id': 'popupCancel',
                'name': 'popupCancel',
                'action':  function () {
                    this.parentWindow.Close();
                    location.reload();
                }
            }
        ]);

        //добавление значений в поля
        if(typeof(data.DEAL_DATA) !== 'undefined'){
            $('#FormAddTaskTimeElem').prepend('<div class="crm-entity-widget-content-block crm-entity-widget-content-block-field-custom-text" data-cid="DEAL_NAME">Сделка: <a href="/crm/deal/details/' + data.DEAL_DATA.ID + '/">' + data.DEAL_DATA.TITLE + '</a>')
            $('input[name="DEAL_ID"]').val(data.DEAL_DATA.ID);
            $('input[name="DEAL_NAME"]').val(data.DEAL_DATA.TITLE);
        }


        if(typeof(data.ROLE_OPTIONS) !== 'undefined'){
            var roles = '<option value="">Не выбрано</option>';
            $.each(data.ROLE_OPTIONS, function (index,role) {
                roles += '<option value="' + role.OPTION_ID + '">' + role.OPTION_VALUE + '</option>';
            });
            if (roles.length > 0) $('select[name="ROLE"]').append(roles);
        }
        if(typeof(data.WORKS_TYPE_OPTIONS) !== 'undefined'){
            var works = '<option value="">Не выбрано</option>';
            $.each(data.WORKS_TYPE_OPTIONS, function (index,work) {
                works += '<option value="' + work.OPTION_ID + '">' + work.OPTION_VALUE + '</option>';
            });
            if (works.length > 0) $('select[name="WORKS_TYPE"]').append(works);
        }
        //текущее время/дата
        var today = self.addCurrentDate();
        $('input[name="DATE_TIME"]').val(today);

        Dialog.Show(); //запуск popup
    }


    taskTimePopUpValidate(){
        var self = this;

        $('#FormAddTaskTimeElem input').css('border-color','#c4c7cc');
        $('#FormAddTaskTimeElem select').css('border-color','#c4c7cc');
        $('#FormAddTaskTimeElem textarea').css('border-color','#c4c7cc');
        $('#FormAddTaskTimeElem .MyPopupError').remove();

        var dataInpt = $('#FormAddTaskTimeElem').serializeArray();
        var fields = {};
        $.each(dataInpt, function () {
            fields[this.name] = this.value;
        });

        //проверка заполнения полей1
        if(fields.DATE_TIME.search(/^[0-3][0-9].[0|1][0-9].(19|20)[0-9]{2}/) == -1){
            $('#FormAddTaskTimeElem input[name="DATE_TIME"]').css('border-color','red');
            $('#FormAddTaskTimeElem input[name="DATE_TIME"]').after('<div class="MyPopupError" style="color:red;font-weight:600;margin:10px 0;">Неверный формат даты!</div>');
        }

        //console.log(fields.DATE_TIME.search(/^[0-3][0-9].[0|1][0-9].(19|20)[0-9]{2}/));
       // console.log(fields);

        if(fields.TIME.search(/^[\d]+/) == -1 || fields.TIME == 0) {
            $('#FormAddTaskTimeElem input[name="TIME"]').css('border-color','red');
            $('#FormAddTaskTimeElem input[name="TIME"]').after('<div class="MyPopupError" style="color:red;font-weight:600;margin:10px 0;">Введите сумму отработанных часов!</div>');
        }
        if(fields.ROLE.length <= 0 ) {
            $('#FormAddTaskTimeElem select[name="ROLE"]').css('border-color','red');
            $('#FormAddTaskTimeElem select[name="ROLE"]').after('<div class="MyPopupError" style="color:red;font-weight:600;margin:10px 0;">Выберите свою роль!</div>');
        }
        if(fields.WORKS_TYPE.length <= 0 ) {
            $('#FormAddTaskTimeElem select[name="WORKS_TYPE"]').css('border-color','red');
            $('#FormAddTaskTimeElem select[name="WORKS_TYPE"]').after('<div class="MyPopupError" style="color:red;font-weight:600;margin:10px 0;">Выберите тип работ!</div>');
        }

       // console.log($('#FormAddTaskTimeElem .MyPopupError').length);

        //если ошщибок нет, создаем элемент списка
        if($('#FormAddTaskTimeElem .MyPopupError').length == 0){
            self.createNewTaskTimeElem(fields);
        }
    }

    createNewTaskTimeElem(fields){
        let self = this;

      //  console.log('createNewTaskTimeElemf',fields);


        BX.ajax({
            method: "POST",
            url: '/local/lib/taskTime/ajax/BestClass.php',
            data: {'FIELDS':fields,'action':'CREATE_NEW_TASK_TIME_ELEM'},
            dataType: "json",
            onsuccess: function (data) {

//                console.log(data);

                $('.bx-core-window.bx-core-adm-dialog .bx-core-adm-dialog-content-wrap-inner').empty();
                $('#popupSubmitSave').hide(); //скрытие кнопки 'Сохранить'

                if(data.result == false){
                    $('.bx-core-window.bx-core-adm-dialog .bx-core-adm-dialog-content-wrap-inner').append('<h2 style="text-align: center; color: red;">' + data.error + '</h2>');

                }
                else {
                    $('.bx-core-window.bx-core-adm-dialog .bx-core-adm-dialog-content-wrap-inner').append('<h2 style="text-align: center; color: #036203;">' + data.result + '</h2>');
                    setTimeout(self.closePopup, 3000);
                }
            }
        });
    }

    addCurrentDate() {
        var date = new Date();
        var day, month, curDate;
        if(date.getDate() < 10) day = '0' + date.getDate();
        else day = date.getDate();
        if(date.getMonth() < 9) month =  '0' + Number(date.getMonth()+1);
        else month = Number(date.getMonth()+1);

        curDate = day + '.'+ month + '.' + date.getFullYear();
        return curDate;
    }

    closePopup(){
        $('#popupCancel').click();
    }

}