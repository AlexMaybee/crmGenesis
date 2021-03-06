<?php
namespace Crmgenesis\Bolvanka;

class Main
{
    const MODULE_ID = 'crmgenesis.bolvanka';

    public function GetPatch($notDocumentRoot=false)
    {
        if($notDocumentRoot)
            return str_ireplace($_SERVER["DOCUMENT_ROOT"],'',dirname(__DIR__));
        else
            return dirname(__DIR__);
    }

    public function isVersionD7()
    {
        return CheckVersion(SM_VERSION, '14.00.00');
    }
}