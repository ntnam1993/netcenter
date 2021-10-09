<?php


namespace App\Exceptions;

use App\Utils\Helper;
use App\Utils\Log;
use Exception;

class CustomException extends Exception
{

    public static function dump($errorMsg)
    {
        Helper::varDump($errorMsg);
    }

    public static function log()
    {
        return new Log();
    }

    public static function dumpAndLog($errorMsg)
    {
        self::log()->errorProcess($errorMsg);
    }

    public static function dumpAndLogWarnning($errorMsg)
    {
        self::log()->logWarnning($errorMsg);
    }
}
