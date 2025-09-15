<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class ServerLogs {
    public function execute() {
        $logPath = PathHelper::getLogsPath() . '/chat.log';
        if (!file_exists($logPath)) {
            return ['success' => true, 'logs' => []];
        }
        
        $lines = array_slice(array_reverse(file($logPath, FILE_IGNORE_NEW_LINES)), 0, 50);
        $logs = array_map(function($line) {
            return trim($line);
        }, $lines);
        
        return ['success' => true, 'logs' => array_reverse($logs)];
    }
}
