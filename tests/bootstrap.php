<?php

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if(!file_exists($autoloadPath)) {
    throw new \Exception('vendor/autoload.php is not found. Did you run `composer install`?');
}
require_once($autoloadPath);

global $argv;

$confKey = '--bitrix-dir';
$key = array_search($confKey, $argv);
$bitrixRootDir = null;
if($key !== false) {
    $bitrixRootDir = $argv[$key + 1] ?? null;
}

if($bitrixRootDir === null && !empty($_ENV['BITRIX_DIR'])) {
    $bitrixRootDir = $_ENV['BITRIX_DIR'];
}

if(empty($bitrixRootDir)) {
    throw new \Exception('Pass "-- --bitrix-dir=/path/to/bitrix-project" parameter to the end of ./phpunit call or set BITRIX_DIR env variable');
}

$_SERVER["DOCUMENT_ROOT"] = $bitrixRootDir;

define("LANGUAGE_ID", "pa");
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("LOG_FILENAME", 'php://stderr');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// Альтернативный способ вывода ошибок типа "DB query error.":
// $GLOBALS["DB"]->debug = true;

// Заменяем вывод фатальных ошибок Битрикса на STDERR - чтобы не было "молчаливого" поведения

class PhpunitFileExceptionHandlerLog extends Bitrix\Main\Diag\FileExceptionHandlerLog {
    public function write($exception, $logType)
    {
        $text = Bitrix\Main\Diag\ExceptionHandlerFormatter::format($exception, false, $this->level);
        $msg = date("Y-m-d H:i:s")." - Host: ".$_SERVER["HTTP_HOST"]." - ".static::logTypeToString($logType)." - ".$text."\n";
        fwrite(STDERR, $msg);
    }
}

$handler = new PhpunitFileExceptionHandlerLog;

$bitrixExceptionHandler = \Bitrix\Main\Application::getInstance()->getExceptionHandler();

$reflection = new \ReflectionClass($bitrixExceptionHandler);
$property = $reflection->getProperty('handlerLog');
$property->setAccessible(true);
$property->setValue($bitrixExceptionHandler, $handler);
