<?php

AddEventHandler("main", "OnUserTypeBuildList", array("MyCurledType", "GetUserTypeDescription"));

class MyCurledType extends CUserTypeString
{
   function GetUserTypeDescription()
   {
      return array(
         "USER_TYPE_ID" => "custom_uf",
         "CLASS_NAME" => "MyCurledType",
         "DESCRIPTION" => "СИСЬКИ и ПОПКИ",
         "BASE_TYPE" => "string",
      );
   }
//Этот метод вызывается для показа значений в списке

   function OnBeforeSave($arUserField, $value) {


      foreach ($value as $k => $v) {
         if($v){
            $result = $value;
         }
      }
      if($result){
         return base64_encode(serialize($result));
      }
   }


   function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm) {

      $arTypes = array(
         'text' => 'Строка',
         'date' => 'Дата'
         );

      $result = '
         <tr class="row" style="vertical-align: top;">
            <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
            <td>Название колонки</td>
            <td>Тип колонки</td>
         </tr>';
      foreach ($arUserField['SETTINGS']['ROW_NAME'] as $key => $value) {
         $result .= '
            <tr class="row deletable" style="vertical-align: top;">
               <td><input type="text" name="SETTINGS[ROW_NAME][]" value="'.$value.'" class="fields string"></td>
               <td>
                  <select value="date" name="SETTINGS[ROW_TYPE][]">';
                  foreach ($arTypes as $k => $v) {
                     $selected = ($arUserField['SETTINGS']['ROW_TYPE'][$key] == $k) ? 'selected' : '' ;
                     $result .= '<option '.$selected.' value="'.$k.'">'.$v.'</option>';
                  }
                  $result .= '
                  </select>
               </td>
            </tr>';

      }
      $result .= '
         <tr class="row" style="vertical-align: top;">
            <td>
               <input type="text" name="SETTINGS[ROW_NAME][]" value="" class="fields string">
            </td>
            <td>
               <select value="date" name="SETTINGS[ROW_TYPE][]">
                 <option value="text">Строка</option>
                 <option value="date">Дата</option>
               </select>
            </td>
         </tr>
         <tr>
            <td></td>
            <td><input type="button" value="Добавить" onClick="addRowCustomTable();"><input type="button" value="Удалить" onClick="deleteRowCustomTable();"></td>
         </tr>;
         <script type="text/javascript">

            function addRowCustomTable(){

               var row = $(".row").last().clone().addClass("deletable");
               row.find("input").map(function(i,v){$(v).val("")});
               row.insertAfter($(".row").last());
            }
            function deleteRowCustomTable(){

               var row = $(".deletable").last().remove();
            }
         </script>
         </td>
         </tr>';
      return $result;
   }

   function PrepareSettings($arUserField) {
      foreach ($arUserField['SETTINGS']['ROW_NAME'] as $key => $value) {
         if(!$value){
            unset($arUserField['SETTINGS']['ROW_NAME'][$key]);
            unset($arUserField['SETTINGS']['ROW_TYPE'][$key]);
         }
      }
      return $arUserField['SETTINGS'];
   }
}
