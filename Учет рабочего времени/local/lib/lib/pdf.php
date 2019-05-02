<?
include($_SERVER['DOCUMENT_ROOT'].'/local/lib/pdf.lib.php');
use Bitrix\Main\Loader;
Loader::includeModule("crm"); 
Loader::includeModule("tasks"); 

$baseURL = "http://pdf.itlogic-ua.com/mpdf/";
$documents = [
	309 => "_act_TOV.php",
	310 => "_act_FOP.php",
	311 => "_dogovir_TOV.php",
	312 => "_dogovir_FOP.php",
	313 => "_dodatok_dogovir_TOV.php",
	314 => "_dodatok_dogovir_FOP.php",
	315 => "_dogovir_post_TOV.php",
	316 => "_dogovir_post_FOP.php",
	317 => "_rakhunok_TOV.php",
	318 => "_rakhunok_FOP.php"
];

/************************************* Общая информация *************************************/
$document_id = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$document_info = CCrmInvoice::GetByID($document_id);
$document_type = $document_info['UF_CRM_1464777318'];
$url = $baseURL.$documents[$document_type];

$x = CCrmDeal::GetList();
/************************************** Параметры из сущности счет (документ) *************************************/
$dateBill = explode(".", $document_info['DATE_BILL']);
$dateBill[2] = str_replace(20, "", $dateBill[2]);
$dateBill = implode("-", $dateBill);
$number = $document_info['UF_CRM_1464855506'];
$documentNumber = $dateBill . "-" . $number;

$clientFooterAsign = $document_info['UF_CRM_1464872273'];
$clientEmails = $document_info['UF_CRM_1464872288'];
$clientResponsible = $document_info['UF_CRM_1464872300'] . ", " . $document_info['UF_CRM_1464872323'];

$dealID = $document_info['UF_DEAL_ID'];
$dealInfo = CCrmDeal::GetByID($dealID);
$dealType = $dealInfo['TYPE_ID'];

// для приложения
$appendixResponsible = $document_info['UF_CRM_1464958908'];
$appendixResponsiblePhone = $document_info['UF_CRM_1464958930'];
$appendixResponsibleEmail = $document_info['UF_CRM_1464958948'];
// обрабатываем товары в файле, который генерит pdf
$appendixProducts = CCrmInvoice::GetProductRows($document_id);
$coordination = $document_info['UF_CRM_1464962307'];
$coordinationPrice = $document_info['UF_CRM_1464965803'];
$appendixWorkType = $document_info['UF_CRM_1465128788'];
$appendixFor = $document_info['UF_CRM_1465137095'];
$appendixFor_ = strtolower(substr($appendixFor, 0, 1));
$appendixFor = str_replace(substr($appendixFor, 0, 1), $appendixFor_, $appendixFor);
$appendixFooterAssign = $document_info['UF_CRM_1465139334'];
$appendixWorkResults = $document_info['UF_CRM_1465199212'];
$appendixClienRequirements = $document_info['UF_CRM_1465198745'];

/************************************* информация по компании *************************************/
$companyID = $document_info['UF_COMPANY_ID'];
$companyInfo = CCrmCompany::GetList([], ['ID' => $companyID])->Fetch();
$bankDetails = $companyInfo['BANKING_DETAILS'];
$companyInfoForAdress = CCrmCompany::GetByID($companyID);

$companyType = $companyInfo['COMPANY_TYPE'];
//юридический адрес
$companyInfoForAdress['REG_ADDRESS_POSTAL_CODE'] ? $legalAdress[] = $companyInfoForAdress['REG_ADDRESS_POSTAL_CODE'] 					  : $legalAdress[] = "";
$companyInfoForAdress['REG_ADDRESS_COUNTRY'] 	 ? $legalAdress[] = $companyInfoForAdress['REG_ADDRESS_COUNTRY'] 	 					  : $legalAdress[] = "";
$companyInfoForAdress['REG_ADDRESS_PROVINCE'] 	 ? $legalAdress[] = $companyInfoForAdress['REG_ADDRESS_PROVINCE'] 	 					  : $legalAdress[] = "";
$companyInfoForAdress['REG_ADDRESS_REGION']		 ? $legalAdress[] = $companyInfoForAdress['REG_ADDRESS_REGION'] 	 					  : $legalAdress[] = "";
$companyInfoForAdress['REG_ADDRESS_CITY'] 		 ? $legalAdress[] = $companyInfoForAdress['REG_ADDRESS_CITY'] 		 					  : $legalAdress[] = "";
$companyInfoForAdress['ADDRESS_LEGAL'] 			 ? $legalAdress[] = preg_replace('/ /', '&nbsp;', $companyInfoForAdress['ADDRESS_LEGAL']) : $legalAdress[] = "";
$legalAdress = implode(", ", array_filter($legalAdress));
// фактический адрес
$companyInfoForAdress['ADDRESS_POSTAL_CODE'] ? $factAdress[] = $companyInfoForAdress['ADDRESS_POSTAL_CODE'] 				   : $factAdress[] = "";
$companyInfoForAdress['ADDRESS_COUNTRY'] 	 ? $factAdress[] = $companyInfoForAdress['ADDRESS_COUNTRY'] 					   : $factAdress[] = "";
$companyInfoForAdress['ADDRESS_PROVINCE'] 	 ? $factAdress[] = $companyInfoForAdress['ADDRESS_PROVINCE'] 					   : $factAdress[] = "";
$companyInfoForAdress['ADDRESS_REGION'] 	 ? $factAdress[] = $companyInfoForAdress['ADDRESS_REGION'] 	 					   : $factAdress[] = "";
$companyInfoForAdress['ADDRESS_CITY'] 		 ? $factAdress[] = $companyInfoForAdress['ADDRESS_CITY'] 						   : $factAdress[] = "";
$companyInfoForAdress['ADDRESS'] 			 ? $factAdress[] = preg_replace('/ /', '&nbsp;', $companyInfoForAdress['ADDRESS']) : $factAdress[] = "";
$companyInfoForAdress['ADDRESS_2'] 			 ? $factAdress[] = $companyInfoForAdress['ADDRESS_2'] : $factAdress[] = "";
$factAdress = implode(", ", array_filter($factAdress));
// почтовый адрес
$postAdress = $companyInfo['UF_CRM_1464177002'];
if ($legalAdress) {
		$legalAdress = 'Юридична адреса: '.$legalAdress.'<br>';
	}
if ($factAdress) {	
	$factAdress  = 'Фактична адреса: '.$factAdress.'<br>';
}
if ($postAdress) {
	$postAdress = 'Поштова адреса: '.$postAdress.'<br>';
}
// все адреса
$clientAdress = $legalAdress.$factAdress.$postAdress;

$rsPhone = CCrmFieldMulti::GetList([],['ENTITY_ID'=>'COMPANY', 'TYPE_ID'=>'PHONE', 'ELEMENT_ID'=>$companyID]);
while ($arPhone = $rsPhone->Fetch()){
	$clientPhone = $arPhone['VALUE'];
}

$inn = $companyInfo['UF_CRM_1463392444'];

if ($companyType == 9) //физ лицо
{
	$client = $document_info['UF_CRM_1464872259'];
	$clientCertificate = $companyInfo['UF_CRM_1464082282'];
	$clientCertificateForHeader = " який діє на підставі свідоцтва про державну реєстрацію № ".$clientCertificate;
	// орг. форма
	switch ($companyInfo['UF_CRM_1464080793']) {
		case '282':
			$orgForm = 'Фізична особа-підприємець';
			$orgFormShort = 'ФОП';
			break;
		case '283':
			$orgForm = 'Суб\'єкт підприємницької діяльності';
			$orgFormShort = 'СПД';
			break;
		
		default:
			$orgForm = "";
			break;
	}
	
}
elseif ($companyType == 8) 
{
	$client = "«".$companyInfo['TITLE']."»";
	$clientCertificate = $document_info['UF_CRM_1464875577'];;
	$clientCertificateForHeader = "в особі ".$clientCertificate.", що діє на підставі статуту";
	$edrpou = $companyInfo['UF_CRM_1463392382'];
	// орг. форма
	switch ($companyInfo['UF_CRM_1464080671']) {
		case '275':
			$orgForm = "Товариство з обмеженою відповідальністю";
			$orgFormShort = 'ТОВ';
			break;
		case '276':
			$orgForm = "Приватне акціонерне товариство";
			$orgFormShort = 'ПрАТ';
			break;
		case '277':
			$orgForm = "Публічне акціонерне товариство";
			$orgFormShort = 'ПАТ';
			break;
		case '278':
			$orgForm = "Відкрите акціонерне товариство";
			$orgFormShort = 'ВАТ';
			break;
		case '279':
			$orgForm = "Закрите акціонерне товариство";
			$orgFormShort = 'ЗАТ';
			break;
		case '280':
			$orgForm = "Приватне підприємство";
			$orgFormShort = 'ПП';
			break;
		case '281':
			$orgForm = "Державне підприємство";
			$orgFormShort = 'ДП';
			break;
		default:
			$orgForm = "";
			break;
	}
}				


/************************************* Передаем параметры *************************************/
$postParams = [
	'companyType' => $companyType,
	'documentNumber' => $documentNumber,
	'clientFooterAsign'	   => $clientFooterAsign,
	'orgForm'			   => $orgForm,
	'orgFormShort'		   => $orgFormShort,
	'client'			   => $client,
	'clientCertificate'	   => $clientCertificate,
	'clientCertificateFH'  => $clientCertificateForHeader,
	'clientAdress'		   => $clientAdress,
	'clientPhone'		   => $clientPhone,
	'stateRegSert'		   => $stateRegSert,
	'inn'				   => $inn,
	'edrpou'			   => $edrpou,
	'bankDetails'		   => $bankDetails,
	'clientEmails'		   => $clientEmails,
	'clientResponsible'	   => $clientResponsible,
	'appendixResponsible'  => $appendixResponsible,
	'appendixResponsiblePhone' => $appendixResponsiblePhone,
	'appendixResponsibleEmail' => $appendixResponsibleEmail,
	'appendixProducts'		=> $appendixProducts, 
	'coordination'			=> $coordination,
	'coordinationPrice'		=> $coordinationPrice,
	'dealType'				=> $dealType,
	'appendixWorkType'		=> $appendixWorkType,
	'appendixFor'			=> $appendixFor,
	'appendixFooterAssign'	=> $appendixFooterAssign,
	'appendixWorkResults'	=> $appendixWorkResults,
	'appendixClienRequirements' => $appendixClienRequirements

];


// pre($appendixClienRequirements);
cURLstart($url, $postParams);

// while ($xx = $x->Fetch())
// {
// 	pre($xx);
// }