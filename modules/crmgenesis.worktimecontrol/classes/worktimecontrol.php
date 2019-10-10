<?

//CModule::IncludeModule('crm');
class WorkTimeControl{

    //подключение файла js с кнопками и аяксом
    public function addCustomControlToPanel(){
        global $APPLICATION;
        CJSCore::Init(array("jquery2")); //Штатная библиотека
        $APPLICATION->AddHeadScript("/bitrix/js/crmgenesis/workPanelControl/script.js");
        $APPLICATION->SetAdditionalCSS("/bitrix/css/crmgenesis/workPanelControl/workPanel.css");
    }

//    /bitrix/js/crmgenesis/workPanelControl/script.js

    //функция для сортировки
    private function universalMassiveSort($key,$type = false){

        //по убыванию
        if($type == 'rev'){
            return function ($a,$b) use ($key){
                if($a[$key] == $b[$key]) return 0;

                //чтобы по возрастанию, нужно заменить местами -1 и 1
                return ($a[$key] > $b[$key]) ? -1 : 1;
            };
        }

        //по возрастанию
        else{
            return function ($a,$b) use ($key){
                if($a[$key] == $b[$key]) return 0;
                return ($a[$key] > $b[$key]) ? 1 : -1;
            };
        }

    }

    //логирование
    private function logData($data){
        $file = $_SERVER["DOCUMENT_ROOT"].'/myWokrPanelTestLog.log';
        file_put_contents($file, print_r([date('d.m.Y'),$data],true), FILE_APPEND | LOCK_EX);
    }

}
?>