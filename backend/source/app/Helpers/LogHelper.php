<?php

namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use DateTimeZone;

class LogHelper
{
    public static function logToFile(string $filename, string $message, string $level = 'error')
    {
        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '', $filename) ?: 'default';
        
        // Create dated filename
        $date = now('UTC')->format('Y-m-d');
        $fullFilename = "{$filename}-{$date}.log";
        $logPath = storage_path("logs/{$fullFilename}");

        // Get caller file
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callerFile = basename($backtrace[1]['file'] ?? 'unknown_file');

        // Create logger with UTC timezone
        $logger = new Logger($filename);
        
        // Configure handler with UTC timezone
        $handler = new StreamHandler($logPath, Logger::toMonologLevel($level));
        
        // Set formatter with UTC time in format
        $formatter = new LineFormatter(
            "[%datetime% UTC] [%extra.caller_file%] %message%\n", 
            "Y-m-d H:i:s",
            true,
            true
        );
        $handler->setFormatter($formatter);
        
        $logger->pushHandler($handler);
        
        // Add caller file as extra context
        $logger->pushProcessor(function ($record) use ($callerFile) {
            $record['extra']['caller_file'] = $callerFile;
            return $record;
        });

        $logger->log($level, $message);
    }
}