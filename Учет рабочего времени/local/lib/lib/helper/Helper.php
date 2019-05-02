<?php

namespace Itlogic\Help;

use \Bitrix\Highloadblock as HL,
    \Bitrix\Main\Loader;

/** будет помогать при работе с разными задачами.
 * Class Helper
 * @package Itlogic\Help
 */
class Helper {
    protected $entity_data_class;

    function __construct($hl_id) {
        Loader::includeModule("highloadblock");

        $hlblock = HL\HighloadBlockTable::getById($hl_id)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $this->entity_data_class = $entity->getDataClass();
    }

    function get($prop) {
        if ( isset($this->$prop) ) {
            return $this->$prop;
        }
    }
}

class DealHelper extends Helper {
    protected $saveData;

    function __construct($hl_id) {
        parent::__construct($hl_id);
    }

    /** формируем данные для записи в нащу HL таблицу
     * @param array $arData
     * @param $dealId
     * @param array $arFields
     * @return array
     */
    function checkData(array $arData, $dealId, array $arFields) {
        $newData = [];

        foreach ( $arFields as $key => $arItem ) {
            $newData[$key] = [
                "UF_ENTITY_ID"      => $arItem["ID"],
                "UF_ENTITY_SUB_ID"  => $dealId
            ];
            foreach ( ["FROM", "TO"] as $code ) {
                $newData[$key]["UF_QUANTITY_{$code}"] = $arData["ITL_QUANTITY_{$code}_{$key}"];
            }
        }

        $this->saveData = $newData;
    }

    /** добавляем/обновляем данные в нашей HL табличке
     * @param array $data
     */
    function addDealProducts(array $data = []) {
        $addMas = ( !empty($data) ) ? $data : $this->saveData;
        $entity_data_class = $this->entity_data_class;

        foreach ( $addMas as $arFields ) {
            $ob = $entity_data_class::getList([
                "filter" => [
                    "UF_ENTITY_ID" => $arFields["UF_ENTITY_ID"]
                ],
                "select" => ["ID"]
            ])->fetch();

            if ( $ob ) {
                $entity_data_class::update($ob["ID"], $arFields);
            } else {
                $entity_data_class::add($arFields);
            }
        }
    }

    /** Получаем данные по диапазону
     * @param $id
     * @return array
     */
    function getDealRangeInfo($id) {
        $entity_data_class = $this->entity_data_class;
        $result = [];

        if ( intval($id) == 0 ) {
            return $result;
        }

        $dbRes = $entity_data_class::getList([
            "filter" => [
                "UF_ENTITY_SUB_ID" => intval($id)
            ]
        ]);

        while ( $arOb = $dbRes->fetch() ) {
            $result[ $arOb["UF_ENTITY_ID"] ] = $arOb;
        }

        return $result;
    }

    /** Удаляем лишние при обновлении данный
     * @param $id
     * @param array $arFields
     */
    function clearFix($id, array $arFields) {
        $entity_data_class = $this->entity_data_class;
        $ids = [];

        $result = $this->getDealRangeInfo($id);
        foreach ( $arFields as $item ) {
            $ids[] = $item["ID"];
        }

        foreach ( $result as $item ) {
            if ( !in_array($item["UF_ENTITY_ID"], $ids) ) {
                $entity_data_class::delete($item["ID"]);
            }
        }
    }
}