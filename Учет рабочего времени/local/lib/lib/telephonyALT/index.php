<?php
require("include/config.php");

$error = "";

// clear auth session
if (isset($_REQUEST["clear"]) || $_SERVER["REQUEST_METHOD"] == "POST") {
    unset($_SESSION["query_data"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /*     * ***************** get code ************************************ */
    if (!empty($_POST["portal"])) {
        $domain = $_POST["portal"];
        $params = array(
            "response_type" => "code",
            "client_id" => CLIENT_ID,
            "redirect_uri" => REDIRECT_URI,
        );
        $path = "/oauth/authorize/";

        redirect(PROTOCOL . "://" . $domain . $path . "?" . http_build_query($params));
    }
    /*     * ****************** /get code ********************************** */
}

if (isset($_REQUEST["code"])) {
    /*     * **************** get access_token ***************************** */
    $code = $_REQUEST["code"];
    $domain = $_REQUEST["domain"];
    $member_id = $_REQUEST["member_id"];
    $params = array(
        "grant_type" => "authorization_code",
        "client_id" => CLIENT_ID,
        "client_secret" => CLIENT_SECRET,
        "redirect_uri" => REDIRECT_URI,
        "scope" => SCOPE,
        "code" => $code,
    );
    $path = "/oauth/token/";

    $query_data = query("GET", PROTOCOL . "://" . $domain . $path, $params);

    if (isset($query_data["access_token"])) {
        $_SESSION["query_data"] = $query_data;
        $_SESSION["query_data"]["ts"] = time();

        redirect(PATH);
        die();
    } else {
        $error = "Произошла ошибка авторизации! " . print_r($query_data, 1);
    }
    /*     * ******************** /get access_token ************************ */
} elseif (isset($_REQUEST["refresh"])) {
    /*     * ****************** refresh auth ******************************* */
    $params = array(
        "grant_type" => "refresh_token",
        "client_id" => CLIENT_ID,
        "client_secret" => CLIENT_SECRET,
        "redirect_uri" => REDIRECT_URI,
        "scope" => SCOPE,
        "refresh_token" => $_SESSION["query_data"]["refresh_token"],
    );

    $path = "/oauth/token/";

    $query_data = query("GET", PROTOCOL . "://" . $_SESSION["query_data"]["domain"] . $path, $params);

    if (isset($query_data["access_token"])) {
        $_SESSION["query_data"] = $query_data;
        $_SESSION["query_data"]["ts"] = time();

        redirect(PATH);
        die();
    } else {
        $error = "Произошла ошибка авторизации! " . print_r($query_data);
    }
    /*     * ******************* /refresh auth ***************************** */
}else{
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

pre($_REQUEST);
// echo "string";