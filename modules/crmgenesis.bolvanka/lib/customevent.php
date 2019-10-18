<?php

namespace Crmgenesis\Bolvanka;

class customevent{

    public function workWithContact(&$arFields){

        //вызов внутри других методов ни приват ни паблик не срабатывает - зацикливается
//        $this->logData($arFields);

        $bitrixfunctionsObj = new bitrixfunctions;
        $bitrixfunctionsObj->logData($arFields);
    }

}