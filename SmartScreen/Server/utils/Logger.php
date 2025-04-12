<?php
class Logger {
    private static $logFile;

    public static function init($logFilePath = null) {
        if ($logFilePath) {
            self::$logFile = $logFilePath;
        } else {
            $defaultPath = getenv('LOG_PATH') ?: __DIR__ . '/../../logs/app.log';
            self::$logFile = $defaultPath;
        }

        if (!file_exists(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0755, true);
        }
    }

    public static function log($message, $level = 'INFO') {
        if (!self::$logFile) {
            self::init();
        }

        $timestamp = date('Y-m-d H:i:s');
        $entry = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }

    public static function error($message) {
        self::log($message, 'ERROR');
    }

    public static function debug($message) {
        self::log($message, 'DEBUG');
    }
}
?>
