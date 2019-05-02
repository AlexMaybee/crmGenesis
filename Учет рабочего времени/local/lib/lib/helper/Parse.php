<?
namespace Itlogic\Help;

use Bitrix\Main\DB\Exception;

class CustomParse {
    const EMPTY_FILE = "не указан файл";
    const EMPTY_DIRECTORY = "не указан путь к директории";
    const EMPTY_NEWFILE = "не указано имя нового файл";
    const FILE_NOT_EXIST = "файла не";

    protected $params = [
        "file"          => "",
        "directory"     => "",
        "newFile"       => "",
        "parseLogic"    => NULL
    ];
    protected $handle;
    protected $buffer;

    public $text;

    function __construct($data = []) {
        foreach ( $data as $key => $value ) {

            if ( key_exists($key, $this->params) ) {
                $this->params[$key] = $value;
            }
        }
    }

    function checkData() {
        $error = false;
        foreach ( [] as $code ) {
            if ( empty($this->params[$code]) ) {
                $error_text = "EMPTY_" . strtoupper($code);
                $error = true;
                throw new Exception(self::$error_text);
            }

        }

        if ( !file_exists($this->params["directory"] . $this->params["file"]) ) {
            $error_text = "файла {$this->params["file"]} нет в директории {$this->params["directory"]}";
            $error = true;
            throw new Exception($error_text);
        }

        return !$error;
    }

    function parse() {
        $path = $this->params["directory"] . $this->params["file"];
        $this->handle = fopen($path, "r+");
        $count = 1;
        $this->buffer = "";
        while (!feof($this->handle)) {
            $continue = false;
            $buffer = fgets($this->handle, 8192);

            //echo $count . "<br>";

            $this->text = is_callable($this->params["parseLogic"]);
            if ( is_object($this->params["parseLogic"]) ) {
                $continue = $this->params["parseLogic"]($buffer, $count);
            } else if ( method_exists($this, $this->params["parseLogic"]) ) {
                $method = $this->params["parseLogic"];
                $continue = $this->$method($buffer, $count);
            }

            ++$count;
            if ( $continue !== "skip" ) {
                $this->buffer .= $buffer. PHP_EOL;
            }
            if ( !$continue ) {
                break;
            }
        }

        file_put_contents($this->params["directory"] . $this->params["newFile"], $this->buffer);
        fclose($this->handle);
    }

    function getParam($code) {
        return $this->$code;
    }

    function getBuffer() {
        return $this->buffer;
    }

    function parseWithId (&$buffer) {
        if ( preg_match("/^INSERT INTO/", $buffer) ) {
            preg_match("/VALUES\(([\d].+?),/", $buffer, $matches);
            $id = $matches[1];
            $pos = strlen($buffer) - 2;

            $buffer = substr($buffer, 0, $pos) . " ON DUPLICATE KEY UPDATE ID={$id}" . substr($buffer, $pos);
        }

        return true;
    }


    function parseWithDuplicateKey (&$buffer) {
        if ( preg_match("/^INSERT INTO/", $buffer) ) {
            preg_match("/INSERT INTO .+?\((.+?)\) VALUES/", $buffer, $strKeys);
            preg_match("/VALUES\((.+?)\);$/", $buffer, $strValues);

            if ( empty($strValues[1]) ) {
                $this->parseWithId($buffer);
                return true;
            }

            $keys = explode(",", $strKeys[1]);
            $values = explode(",", $strValues[1]);
            if ( count($keys) != count($values) ) {
                $this->parseWithId($buffer);
                return true;
            }

            $res = [];
            foreach ( $keys as $k => $key ) {
                $res[] = trim($key) . "=" . trim($values[$k]);
            }

            $pos = strlen($buffer) - 2;
            $buffer = substr($buffer, 0, $pos) . " ON DUPLICATE KEY UPDATE " . implode(", ", $res) . substr($buffer, $pos);
        }

        return true;
    }
}
?>