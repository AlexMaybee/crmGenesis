<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use Bitrix\Main\Loader; 
Loader::includeModule("crm");


$companyID = $_POST['companyID'];

if ($companyID)
{
	$companyType = CCrmCompany::GetByID($companyID)['COMPANY_TYPE'];
	echo $companyType;
}


exit();
