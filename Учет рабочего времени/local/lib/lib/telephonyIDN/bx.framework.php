<?php

define('TOKENS_FILE', __DIR__.'/tokens.txt');
// echo TOKENS_FILE;

// Parameter for forced updating tokens
if(isset($_GET['key']))
{
    if($_GET['key'] == CLIENT_ID){
        if(file_exists(TOKENS_FILE))
        {
            log_write('forced tokens refresh', 'framework_log.txt');

            unlink(TOKENS_FILE);

            bx_auth(3);
        }
    }
}


// Show or no PHP errors
function show_errors($error = FALSE)
{
    if($error)
    {
        ini_set("display_errors",1);
        error_reporting(E_ALL);
    }
    else
    {
        ini_set("display_errors",0);
        error_reporting(0);
    }
}

//Send data via CURL and returns response as json
function sendByCurl ($url, $request) {
	$mr = 5;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url.$request);
	// pre($url.$request);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($curl, CURLOPT_USERPWD, "admin:eq#5G3lR3f");
	// curl_setopt($curl, CURLOPT_USERPWD, "admin:eq#5G3lR3f");
	        if ($mr > 0) {
            $newurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

            $rch = curl_copy_handle($curl);
            curl_setopt($rch, CURLOPT_HEADER, true);
            curl_setopt($rch, CURLOPT_NOBODY, true);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($rch, CURLOPT_USERPWD, "admin:eq#5G3lR3f");
            do {
                curl_setopt($rch, CURLOPT_URL, $newurl);
                $header = curl_exec($rch);
                if (curl_errno($rch)) {
                    $code = 0;
                } else {
                    $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                    if ($code == 301 || $code == 302) {
                        preg_match('/Location:(.*?)\n/', $header, $matches);
                        $newurl = trim(array_pop($matches));
                    } else {
                        $code = 0;
                    }
                }
            } while ($code && --$mr);
            curl_close($rch);
            if (!$mr) {
                if ($maxredirect === null) {
                    trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                } else {
                    $maxredirect = 0;
                }
                return false;
            }
            curl_setopt($curl, CURLOPT_URL, $newurl);
        }
        // curl_exec($curl);
	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

	// curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
	// curl_setopt($curl, CURLOPT_FOLLOWLOCATION, FALSE);
	// curl_setopt($curl, CURLOPT_MAXREDIRS, 20);
	$response = curl_exec($curl);
	$curl_error = curl_error($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if ($curl_error){
		echo $curl_error;
		return FALSE;
	}
	// echo "Request was sent to ". $url;
	// echo "Response: ". $response;
	log_write($response, 'response');
    // die('sendByCurl');
	return $response;
}

// Update tokens in file "tokens.txt"
function TokensUpdate ($filename, $response)
{
	if(isset($response['access_token']) and isset($response['refresh_token']))
	{
		$GLOBALS['ACCESS_TOKEN'] = $response['access_token'];
		$GLOBALS['REFRESH_TOKEN'] = $response['refresh_token'];
		$tokens = $GLOBALS['ACCESS_TOKEN'] .','. $GLOBALS['REFRESH_TOKEN'];
		file_put_contents($filename, $tokens);
		return TRUE;
	}
	else return FALSE;
}

// First authorization in Bitrix
function bx_auth($type=2)
{
    // echo $type;
	if ($type == 3)
	{
		if(file_exists(TOKENS_FILE))
		{
			log_write("bx_auth(3) reading tokens from file", 'framework_log.txt');
			$tokens = file_get_contents(TOKENS_FILE);
			$tokens = trim($tokens);
			if(!empty($tokens))
			{
				log_write("bx_auth(3) tokens ready !", 'framework_log.txt');
				$arTokens = explode(',', $tokens);
				$GLOBALS['REFRESH_TOKEN'] = $arTokens[1];
				$GLOBALS['ACCESS_TOKEN'] = $arTokens[0];
				return TRUE;
			}
		}

		// Checks if "code" was received during the "first authorization"
		if(isset($_GET['code']) or isset($_POST['code']))
		{
			log_write("bx_auth(3) with code", 'framework_log.txt');
			$oa = 'oauth.bitrix.info';
			// Sends this code and accepts the "access token" and "refresh token" in response
			$data = sendByCurl("https://". $oa/*PORTAL_NAME*/ ."/oauth/token/", "?client_id=". CLIENT_ID ."&grant_type=authorization_code&client_secret=". CLIENT_SECRET ."&redirect_uri=". APP_LINK ."&code=". $_GET["code"] ."&scope=". SCOPE);
			log_write($data, 'test');
            $access_array = json_decode($data,TRUE);
			// printr($access_array);
			// Writes accepted tokens to file
			return TokensUpdate(TOKENS_FILE, $access_array);
		}
		else
		{ // If "code" not exists, requires the "first authorization"
			log_write("bx_auth(3) without code", 'framework_log.txt');
			if ( ! headers_sent())
			{
				header("Location:https://".PORTAL_NAME."/oauth/authorize/?client_id=".CLIENT_ID."&response_type=code&redirect_uri=".APP_LINK);
				exit;
			}
			else
			{
				echo "Пожалуйста, кликните по ссылке для авторизации в Битрикс24. ";
				echo "<a href=\"https://". PORTAL_NAME ."/oauth/authorize/?client_id=".CLIENT_ID ."&response_type=code&redirect_uri=".APP_LINK ."\">Клик для авторизации</a></br>";
				exit;

			}
		}
	}

    elseif ($type == 4)
    {
        include_once("net.php");

        $config['cookies'] = COOKIE_CONF;
    	$net = new Net($config);
    	$post['AUTH_FORM'] = "Y";
    	$post['TYPE'] = "AUTH";
    	/* -----> */ $post['backurl'] = "/oauth/authorize/?user_lang=ua&client_id=b24.538a2982889106.40457798&redirect_uri=https%3A%2F%2Filevel.bitrix24.ua%2Foauth%2Fauthorize%2F%3Fauth_service_id%3DBitrix24Net%26oauth_proxy_params%3DcmVkaXJlY3RfdXJpPWh0dHAlM0ElMkYlMkZpbGV2ZWwud3MlMkZjcm0lMkZhcHAucGhwJmNsaWVudF9pZD1iOTllNWRjNDMyNzM4ZTBkZTAzMGI3OTFlYzMzNjFhNiZyZXNwb25zZV90eXBlPWNvZGU%253D&scope=auth%2Cprofile&response_type=code&state=site_id%3Ds1%26backurl%3D%252Foauth%252Fauthorize%252F%253Fcheck_key%253D3fa0356da91793514f8f3ffe68dadce6%2526client_id%253Db99e5dc432738e0de030b791ec3361a6%2526response_type%253Dcode%2526redirect_uri%253Dhttp%25253A%25252F%25252Filevel.ws%25252Fcrm%25252Fapp.php%26mode%3Dpage";
    	$post['USER_LOGIN'] = LOGIN;
    	$post['USER_PASSWORD'] = PASSWORD;
    	$post['USER_REMEMBER'] = "Y";

    	$net -> get("https://". PORTAL_NAME ."");
    	$data = $net -> post("https://www.bitrix24.net/auth/", $post);
    	$data = $net -> get("https://". PORTAL_NAME ."/oauth/authorize/?client_id=". CLIENT_ID ."&response_type=code&redirect_uri=". APP_LINK ."");
    	$data = $net -> get("https://". PORTAL_NAME ."/oauth/authorize/?client_id=". CLIENT_ID ."&response_type=code&redirect_uri=". APP_LINK ."&current_fieldset=SOCSERV");
    	$codeurl = parse_url($net -> result['url']);
		parse_str($codeurl['query'], $query);
    	if (!strlen($query['code'])) return;

    }

    // For second-type applications accept the token and save it into global variable for concatenating to all referers
	elseif ($type == 2)
    {


///////// TODO : FIX SECOND=TYPE AUTH



		if(isset($_REQUEST['AUTH_ID']))
		{
			$GLOBALS['ACCESS_TOKEN'] = $_REQUEST['AUTH_ID'];
		}
    }
}

// Refresh token if needed
function bx_refresh($debug = FALSE) {

	log_write("bx_refresh", 'framework_log.txt');

	// Sends "refresh token" for update "access token"
    $oa = 'oauth.bitrix.info';
    $request = 'https://'. $oa /*PORTAL_NAME*/ .'/oauth/token/?grant_type=refresh_token&client_id='. CLIENT_ID .'&client_secret='. CLIENT_SECRET .'&refresh_token='. $GLOBALS['REFRESH_TOKEN'] .'&scope='. SCOPE .'&redirect_uri='. APP_LINK;
    $login = "admin";
    $password = "eq#5G3lR3f";
    $curl = curl_init($request);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERPWD, [$login => $password]);

    $response = curl_exec($curl);
    $curl_errorno = curl_errno($curl);
    curl_close($curl);

    // Shows response if debug
    echo ($debug) ? $response : null;
    // JSON -> Array
    $access_array = json_clean_decode($response);

    // Cheking if errors was occured
    if(isset($access_array['error']))
    {	// If "refresh token" expires, invoke bx_auth for "first authorization" again
		if(file_exists(TOKENS_FILE))
		{
			unlink(TOKENS_FILE);
			log_write("tokens file deleted", 'framework_log.txt');
			// Try to re-auth
			if( ! bx_auth(3))
			{
				log_write("cannot authorize in Bitrix24", 'framework_log.txt');
				exit;
			}
			log_write("bx_refresh-auth", 'framework_log.txt');
		}
		else
		{	// Try to re-auth
			if( ! bx_auth(3))
			{
				log_write("cannot authorize in Bitrix24", 'framework_log.txt');
				exit;
			}
		}
	}
    else
    {	// If access, update tokens in file
        TokensUpdate(TOKENS_FILE, $access_array);
    }

}

function http_post ($url, $data)
{
    $data_url = http_build_query ($data);
    $data_len = strlen ($data_url);

    return array ('content'=>file_get_contents ($url, FALSE, stream_context_create (array ('https'=>array ('method'=>'POST'
            , 'header'=>"Connection: close\r\nContent-Length: $data_len\r\n"
            , 'content'=>$data_url
            ))))
        , 'headers'=>$http_response_header
        );
}

function bx_call_get($method, $array = array(), $debug = FALSE, $format = 'json') {

    $agent = 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6';

    // Creates request link
    $request = 'https://'. PORTAL_NAME .'/rest/'. $method .'.'. $format .'?auth='. $GLOBALS['ACCESS_TOKEN'];
    echo $request;
    echo "</br>";
    // Converts array to POST query
    if (!is_array($array))
        $query = $array;
    else
        $query = http_build_query($array);

    // Sends query to server
    $curl = curl_init($request);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
    // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 20);
    curl_setopt($curl, CURLOPT_USERAGENT, $agent);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
    curl_setopt($curl, CURLOPT_FAILONERROR, 0);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                                'Content-Type: application/json',
                                                'Transfer-Encoding: chunked',
                                                'Connection: keep-alive',
                                                'Content-Length:'.strlen($query)
                                            ));
    $response = curl_exec($curl);
    $curl_errorno = curl_errno($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Debuggin data
    if ($debug)
    {
        echo '<br/>Request: '.  json_clean_encode($array) .'<br/>';
        echo '<br/>Response: ';
        printr (json_clean_decode($response));
        echo '<br/>HTTP_CODE: '.  $status .'<br/><br/>';

        $resp = json_clean_decode($response);

        if (isset($resp['error']))
        {
            echo '<br/>Ошибка: '. $resp['error'];
            echo '<br/>Описание: '. $resp['error_description'];
        }
    }

    // JSON -> Array
    $response = json_clean_decode($response);
    // Getting result
    // If current token is expired, refresh it and invoke bx_call again
    $result = array();
    if(isset($response['error']))
    {
        if ($response['error'] == 'expired_token')
        {
            log_write("bx_call - expired_token", 'framework_log.txt');
            bx_refresh();
            $result = bx_call($method, $array, $debug, $format);
        }
        if($response['error'] == 'invalid_token')
        {
            log_write("bx_call - invalid_token", 'framework_log.txt');
            bx_refresh();
            $result = bx_call($method, $array, $debug, $format);
        }
    }

    if (isset($response['result']))
    {
        log_write("bx_call - access", 'framework_log.txt');
        $result = $response;
    }

    return $result;
}
// Calls Bitrix Framework method
function bx_call($method, $array = array(), $debug = FALSE, $format = 'json') {

	$agent = 'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1090.0 Safari/536.6';

	// Creates request link
	$request = 'https://'. PORTAL_NAME .'/rest/'. $method .'.'. $format .'?auth='. $GLOBALS['ACCESS_TOKEN'];
	// echo $request;
	// echo "</br>";
	// Converts array to POST query
	if (!is_array($array))
        $query = $array;
    else
        $query = http_build_query($array);

	// Sends query to server
	$curl = curl_init($request);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
	// curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_MAXREDIRS, 20);
	curl_setopt($curl, CURLOPT_USERAGENT, $agent);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
	curl_setopt($curl, CURLOPT_FAILONERROR, 0);
	// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
												'Content-Type: application/json',
												'Transfer-Encoding: chunked',
												'Connection: keep-alive',
												'Content-Length:'.strlen($query)
											));
	$response = curl_exec($curl);
	$curl_errorno = curl_errno($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	// Debuggin data
	if ($debug)
	{
		echo '<br/>Request: '.  json_clean_encode($array) .'<br/>';
		echo '<br/>Response: ';
		printr (json_clean_decode($response));
		echo '<br/>HTTP_CODE: '.  $status .'<br/><br/>';

		$resp = json_clean_decode($response);

		if (isset($resp['error']))
		{
			echo '<br/>Ошибка: '. $resp['error'];
			echo '<br/>Описание: '. $resp['error_description'];
		}
	}

	// JSON -> Array
	$response = json_clean_decode($response);
	// Getting result
	// If current token is expired, refresh it and invoke bx_call again
	$result = array();
	if(isset($response['error']))
	{
		if ($response['error'] == 'expired_token')
		{
			log_write("bx_call - expired_token", 'framework_log.txt');
			bx_refresh();
			$result = bx_call($method, $array, $debug, $format);
		}
		if($response['error'] == 'invalid_token')
		{
			log_write("bx_call - invalid_token", 'framework_log.txt');
			bx_refresh();
			$result = bx_call($method, $array, $debug, $format);
		}
	}

	if (isset($response['result']))
	{
		log_write("bx_call - access", 'framework_log.txt');
		$result = $response;
	}

	return $result;
}

// Use this function to get a list with more than 50 positions
function bx_call_list ($method, $array = array(), $debug = FALSE, $format = 'json')
{
	$more = TRUE;
	$resultTotal = array();
	while($more == TRUE)
	{
		$result = bx_call($method, $array, $debug, $format);
		if (is_array ($result))
		{
			if (array_key_exists("next", $result))
			{
				$array["start"] = $result["next"];
			}
			else $more = FALSE;
			if(isset($result['result']))
			{
				foreach ($result['result'] as $item)
				{
					$resultTotal['result'][] = $item;
				}
			}
		}
	}

	return $resultTotal;
}

// JSON -> Array
function json_clean_decode($json, $assoc = TRUE, $depth = 512, $options = 0) {

    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)#", '', $json);

    if(version_compare(phpversion(), '5.4.0', '>=')) {
        $array = json_decode($json, $assoc, $depth, $options);
    }
    elseif(version_compare(phpversion(), '5.3.0', '>=')) {
        $array = json_decode($json, $assoc, $depth);
    }
    else {
        $array = json_decode($json, $assoc);
    }

    return $array;
}

// Array -> JSON
function json_clean_encode($array, $options = 0, $depth = 512) {

    if(version_compare(phpversion(), '5.5.0', '>=')) {
        $json = html_entity_decode(json_encode($array, $depth, $options));
    }
    elseif(version_compare(phpversion(), '5.3.0', '>=')) {
        $json = html_entity_decode(json_encode($array, $options));
    }
    else {
        $json = html_entity_decode(json_encode($array));
    }

    return $json;
}

// Prints complex-type data
function printr($a) {
    echo "<pre>", htmlspecialchars(print_r($a, TRUE)), "</pre>";
}

// Log app actions (DEPRECATED)
function LogWrite ($fileName, $string, $maxSize = 5000)
{
	$date = date("Y.m.d H:i:s", time());
	$string = $date." ".$string."\n";
	if(file_exists($fileName) && filesize($fileName) < $maxSize)
	{
		file_put_contents($fileName, $string, FILE_APPEND);
	}
	else
	{
		file_put_contents($fileName, $string);
	}
}

// Log app actions
function log_write ($data, $file_name = 'log.txt', $days_ago=5)
{
	$today_file_name = date("Y-m-d", time());
	$time = date("H:i:s", time());

	if(is_array($data) || is_object($data))
	{
		$data = print_r($data, TRUE);
	}

	$string = $time." ".$data."\n";

	if( ! is_dir('log'))
	{
		if( ! mkdir('log')) return;
	}
	if( ! is_dir('log/'.$file_name))
	{
		if( ! mkdir('log/'.$file_name)) return;
	}

	file_put_contents('log/'.$file_name.'/'.$today_file_name, $string, FILE_APPEND);

	$ar_log_files = scandir('log/'.$file_name);

	if(count($ar_log_files) > $days_ago+2)	// scandir returns array of files in directory including '.' and '..' which don't must be counted
	{
		foreach($ar_log_files as $log_file)
		{
			if((strtotime($log_file) < (time()-$days_ago*24*3600)) && (file_exists('log/'.$file_name.'/'.$log_file)))
			{
				unlink('log/'.$file_name.'/'.$log_file);
			}
		}
	}
}

// Show all event handlers hanged on Bitrix by this app
function GetEventListeners ($printResp=TRUE)
{
	$curl = curl_init('https://'.PORTAL_NAME.'/rest/event.get?auth='.$GLOBALS['ACCESS_TOKEN']);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($curl);
	$curl_errorno = curl_errno($curl);
	curl_close($curl);
	if($response)
	{
		$arResponse = json_decode($response, TRUE);
		if(isset($arResponse['error']) and ($arResponse['error'] == 'invalid_token' or $arResponse['error'] == 'expired_token'))
		{
			bx_refresh();
			GetEventListeners();
		}
		if($printResp)
		{
			printr($arResponse);
		}
		return TRUE;
	}
	return FALSE;
}

// Sets the handler for event in Bitrix24
function SetEventListener ($handlerDirectory, $event, $printResp=FALSE)
{
	$handlerDirectory = urlencode($handlerDirectory);
	$curl = curl_init('https://'.PORTAL_NAME.'/rest/event.bind.json?auth='.$GLOBALS['ACCESS_TOKEN'].'&event='.$event.'&handler='.$handlerDirectory);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($curl);
	curl_close($curl);
	if($response)
	{
		$arResponse = json_decode($response, TRUE);
		if(isset($arResponse['error']) and ($arResponse['error'] == 'invalid_token' or $arResponse['error'] == 'expired_token'))
		{
			bx_refresh();
			SetEventListener($handlerDirectory, $event);
		}
		if($printResp)
		{
			printr($arResponse);
		}
		return TRUE;
	}
	return FALSE;
}

// Deletes the handler for event
function DeleteEventListener($handlerDirectory, $event, $printResp=FALSE)
{
	$handlerDirectory = urlencode($handlerDirectory);
	$curl = curl_init('https://'.PORTAL_NAME.'/rest/event.unbind.json?auth='.$GLOBALS['ACCESS_TOKEN'].'&event='.$event.'&handler='.$handlerDirectory);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($curl);
	curl_close($curl);
	if($response)
	{
		$arResponse = json_decode($response, TRUE);
		if(isset($arResponse['error']) and ($arResponse['error'] == 'invalid_token' or $arResponse['error'] == 'expired_token'))
		{
			bx_refresh();
			DeleteEventListener($handlerDirectory, $event);
		}
		if($printResp)
		{
			printr($arResponse);
		}
		return TRUE;
	}
	return FALSE;
}

function curl_exec_follow(/*resource*/ $ch, /*int*/ &$maxredirect = null) {
    $mr = $maxredirect === null ? 5 : intval($maxredirect);
    if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    } else {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        if ($mr > 0) {
            $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            $rch = curl_copy_handle($ch);
            curl_setopt($rch, CURLOPT_HEADER, true);
            curl_setopt($rch, CURLOPT_NOBODY, true);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
            do {
                curl_setopt($rch, CURLOPT_URL, $newurl);
                $header = curl_exec($rch);
                if (curl_errno($rch)) {
                    $code = 0;
                } else {
                    $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                    if ($code == 301 || $code == 302) {
                        preg_match('/Location:(.*?)\n/', $header, $matches);
                        $newurl = trim(array_pop($matches));
                    } else {
                        $code = 0;
                    }
                }
            } while ($code && --$mr);
            curl_close($rch);
            if (!$mr) {
                if ($maxredirect === null) {
                    trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                } else {
                    $maxredirect = 0;
                }
                return false;
            }
            curl_setopt($ch, CURLOPT_URL, $newurl);
        }
    }
    return curl_exec($ch);
}

function pre($val){
    if(!$val){
        echo "<pre>";
        var_dump($val);
        echo "</pre>";
    }else{
        echo "<pre>";
        print_r($val);
        echo "</pre>";
    }
}

function query($method, $url, $data = null) {
    $query_data = "";

    $curlOptions = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => "admin:eq#5G3lR3f"
    );

    if ($method == "POST") {
        $curlOptions[CURLOPT_POST] = true;
        $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
    } elseif (!empty($data)) {
        $url .= strpos($url, "?") > 0 ? "&" : "?";
        $url .= http_build_query($data);
    }
    $curl = curl_init($url);
    curl_setopt_array($curl, $curlOptions);
    $result = curl_exec($curl);
    $response = json_decode($result, 1);
    if(isset($response['error']))
    {
        if ($response['error'] == 'expired_token')
        {
            log_write("bx_call - expired_token", 'framework_log.txt');
            bx_refresh();
            $result = bx_call($method, $array, $debug, $format);
        }
        if($response['error'] == 'invalid_token')
        {
            log_write("bx_call - invalid_token", 'framework_log.txt');
            bx_refresh();
            $result = bx_call($method, $array, $debug, $format);
        }
    }

    return $result;//json_decode($result, 1);
}