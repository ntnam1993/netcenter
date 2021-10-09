<?php

namespace App\Utils;

use Carbon\Carbon;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Utils\Helper;

class Log
{
    protected $log;
    protected $logProcess;

    private function logProcess()
    {
        if (!$this->logProcess) {
            $log = new Logger('process');
            $log->setHandlers([
                new StreamHandler(storage_path('logs/process/'.Carbon::now()->format('Y-m-d').'.log'))
            ]);
            return $this->logProcess = $log;
        }
        return $this->logProcess;
    }

    public function infoProcess($dataLog, $s_code, $checkCSV = null)
    {
        if (!is_array($dataLog) || !count($dataLog)) {
            return;
        }
        $logLevel = env('LOG_LEVEL') ? env('LOG_LEVEL') : 1;
        $logStatus = env('LOG_STATUS') ? env('LOG_STATUS') : 'ON';

        if ($logStatus != 'ON') {
            return;
        }

        $log = $this->logProcess();

        self::processWriteLog($log, $logLevel, $dataLog, $s_code, $checkCSV);

        return;
    }

    private static function processWriteLog($log, $logLevel, $dataLog, $s_code, $checkCSV)
    {
        $log->log(Logger::INFO, '-------------------Shop Information-------------------');
        if ($logLevel >= 1) {
            $log->log(Logger::INFO, '-', ['c_code' => (!empty($dataLog['c_code']) ? $dataLog['c_code'] : 'Not Found')]);
            $log->log(Logger::INFO, '-', ['s_code' => (!empty($s_code) ? $s_code : 's_code Not Found')]);
            if ($checkCSV) {
                $log->log(Logger::INFO, $checkCSV);
            }
            if ($logLevel >= 2) {
                $log->log(Logger::INFO, '-', ['type' => (!empty($dataLog['type']) ? $dataLog['type'] : 'Type Shop Not Found')]);
                if ($logLevel >= 3) {
                    $log->log(Logger::INFO, '-', ['goal' => (!empty($dataLog['goal']) ? $dataLog['goal'] : 'Goal Shop Not Found')]);
                    if ($logLevel >= 4) {
                        $log->log(Logger::INFO, '-', ['type_html' => (!empty($dataLog['type_html']) ? serialize($dataLog['type_html']) : 'Build type HTML fail')]);
                    }
                }
            }
        }
    }

    public function errorProcess($message)
    {
        Helper::varDump('ERROR: '.$message);
        $log = $this->logger();
        $log->log(Logger::ERROR, $message);
        return;
    }

    private function logger()
    {
        if (!$this->log) {
            $log = new Logger('lumen');
            $formatter = new LineFormatter(null, null, false, true);
            $logHandle = new StreamHandler(storage_path('logs/lumen.log'));
            $log->pushHandler($logHandle);
            $logHandle->setFormatter($formatter);
            return $this->log = $log;
        }
        return $this->log;
    }

    public function logInfo($message)
    {
        Helper::varDump('INFO:'.$message);
        $this->logger()->log(Logger::INFO, $message);
        return;
    }

    public function logError($message)
    {
        Helper::varDump('ERROR:'.$message);
        $this->logger()->log(Logger::ERROR, $message);
        return;
    }

    public function logWarnning($message)
    {
        Helper::varDump('WARN:'.$message);
        $this->logger()->log(Logger::WARNING, $message);
        return;
    }
}
