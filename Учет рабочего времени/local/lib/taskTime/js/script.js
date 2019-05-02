$(document).ready(function () {
    addCurrentDate('#form_lists_element_add_124 input[name="PROPERTY_585[n0][VALUE]"]'); //Текущая дата
    addPersonDatatoFields();
});

//получаем текущую дату
function addCurrentDate(selector) {
    var date = new Date();
    var day, month, curDate;
    if(date.getDate() < 10) day = '0' + date.getDate();
    else day = date.getDate();
    if(date.getMonth()< 10) month =  '0' + Number(date.getMonth()+1);
    else day = Number(date.getMonth()+1);

    curDate = day + '.'+ month + '.' + date.getFullYear();
    $(selector).val(curDate); //Текущая дата
    //return curDate;
}


/*Добавление ID, имени + фамилии в строку Сотрудник
* Получение ID текущего пользователя:
* res.data().ID - ID пользователя
* res.data().NAME - Имя пользователя
* res.data().LAST_NAME - Фамлия пользователя
*/
function addPersonDatatoFields() {

        var action = 'GiveMeCurUserID';
        BX.ajax({
            method: "POST",
            url: '/local/lib/taskTime/ajax/BestClass.php',
            data: {'action': action},
            dataType: "json",
            onsuccess: function (data) {
                $('input[name="PROPERTY_588[n0][VALUE]"]').val(data.ID);
            }
        });
}

