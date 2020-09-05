<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
$currentLevel = 'TRACE';
$logPath = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'logs']);
date_default_timezone_set('Europe/Moscow');

class Logger {
    private static $logLevels = ['TRACE' => 0, 'DEBUG' => 1, 'INFO' => 2, 'ERROR' => 3, 'NONE' => 4];

    private function log($data, $loglevel)
    {
        global $currentLevel;
        global $logPath;
        if (!is_dir($logPath)) {
            if (@!mkdir($logPath)) {
                echo "<h1>Permission denied, can't create logs directory '$logPath'</h1>";
                return;
            }
        }
        $filename = $logPath.DIRECTORY_SEPARATOR."log".date("Y-m-d").".log";
        if (Logger::$logLevels[$loglevel] >= Logger::$logLevels[$currentLevel]) {
            $function = @$data['function'] ?? __FUNCTION__;
            $line = @$data['line'] ?? 'unknouwn line';
            $message = @$data['message'] ?? print_r($data, true);
            $descr = $data['descr'] ?? 'descr';
            $m = print_r($message, true);   //transform arrays and objects into strings
            $l = print_r($loglevel, true);
            $formattedMessage = date("Y-m-d H:i:s") . "\t{$l}\t{$function}:{$line}\t{$descr}:{$m}\n";
            file_put_contents($filename, $formattedMessage, FILE_APPEND);
        }
    }

    public static function Tlog($message)
    {
        Logger::log($message, 'TRACE');
    }

    public static function Dlog($message)
    {
        Logger::log($message, 'DEBUG');
    }

    public static function Ilog($message)
    {
        Logger::log($message, 'INFO');
    }

    public static function Elog($message)
    {
        Logger::log($message, 'ERROR');
    }
}