$(document).ready(function () {

   /* $('.projects_progressbar_block').hover(function () {
      $(this).children('.projects_progressbar_hidden').toggle('slow');
    });
*/

    //Показывать при наведении на главый прогрессбар дни месяца и выделять текущий день
    showCurDatesOnCompanyProgress();

    //Перезагрузка страницы каждые 5 мин.
    setInterval(function () {
        location.reload();
    },1000*60*5);

   /* console.log(document.body.scrollTop);
    console.log(document.documentElement.scrollTop);

    var cont = $('.dashboard_container');
    cont.css('height',document.documentElement.scrollHeight - 250);

    cont.scrollTop(000);*/


/*
   //отображение иконки ответственного поверх всех в сделках в работе
    $('.projects_progressbar_employees img').mouseenter(function () {

        var counter = $(this).parent().find('img').length; //кол-во картинок в блоке
        var number = $(this).attr('element-count'); //кол-во картинок в блоке

        if(number == 1) $(this).css('margin-right','20px');
        if(number > 1) $(this).css('margin','0 20px 0 20px');
        console.log(counter);
        console.log(number);
        console.log('Наведение!');
        });
    $('.projects_progressbar_employees img').mouseleave(function () {

        var number = $(this).attr('element-count'); //кол-во картинок в блоке
        if(number == 1) $(this).css('margin-right','0');
        if(number > 1) $(this).css('margin','0 0 0 0px');

        //console.log(counter);
        console.log(number);
        console.log('УБРАЛ!');

    });
    */

});

function showCurDatesOnCompanyProgress() {

    var date = new Date();
    var curDay = date.getDate();
    if(curDay < 10) curDay = '0' + curDay; // это нужно было, т.к. js дает число без ведущего 0
   // console.log(curDay);
    $('.progress-days-absolute').mouseover(function () {
        $('.progress_day_of_month').css({'visibility':'visible','opacity':'1'});
        $('.progress-days-absolute').css({'background-color':'#000'});
        $('.progress_day_of_month:contains('+curDay+')').parent().css({'border':'3px solid #15c4e0'})
    });
    $('.progress-days-absolute').mouseleave(function () {
        $('.progress_day_of_month').css({'visibility':'hidden','opacity':'0.3'});
        $('.progress-days-absolute').css({'background-color':'transparent'});
        $('.progress_day_of_month:contains('+curDay+')').parent().css({'border':'none','border-right':'3px solid #000'})
    });
    
}