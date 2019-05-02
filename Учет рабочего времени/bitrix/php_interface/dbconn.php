<?
if(!(defined("CHK_EVENT") && CHK_EVENT===true))
    define("BX_CRONTAB_SUPPORT", true);
define("DBPersistent", false);
define("BX_USE_MYSQLI",true);
$DBType = "mysql";
$DBHost = "localhost";
$DBLogin = "bitrix";
$DBPassword = "1408php1216ako";
$DBName = "bitrix";
$DBDebug = true;
$DBDebugToFile = false;

@set_time_limit(60);

define("DELAY_DB_CONNECT", true);
define("CACHED_b_file", 3600);
define("CACHED_b_file_bucket_size", 10);
define("CACHED_b_lang", 3600);
define("CACHED_b_option", 3600);
define("CACHED_b_lang_domain", 3600);
define("CACHED_b_site_template", 3600);
define("CACHED_b_event", 3600);
define("CACHED_b_agent", 3660);
define("CACHED_menu", 3600);

define("BX_UTF", true);
define("BX_FILE_PERMISSIONS", 0664);
define("BX_DIR_PERMISSIONS", 0755);
@umask(~BX_DIR_PERMISSIONS);
define("BX_DISABLE_INDEX_PAGE", true);
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

?>