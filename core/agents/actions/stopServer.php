<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class StopServer {
    public function execute() {
        $configPath = PathHelper::getCorePath() . '/config/settings.json';
        if (!file_exists($configPath)) {
            return ['error' => 'Fichier settings.json introuvable'];
        }
        
        $settings = json_decode(file_get_contents($configPath), true);
        $port = $settings['port'] ?? 5000;
        $host = $settings['host'] ?? '0.0.0.0';
        
        // Identifier le processus PHP
        $pid = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec("netstat -ano | findstr :$port");
            if (preg_match('/:\d+\s+(\d+)/', $output, $matches)) {
                $pid = $matches[1];
            }
        } else {
            $output = shell_exec("lsof -i :$port -t");
            if (!empty(trim($output))) {
                $pid = trim($output);
            }
        }
        
        if (!$pid) {
            return ['success' => true, 'status' => 'not_running'];
        }
        
        // Arrêter le processus
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("taskkill /PID $pid /F > nul 2>&1");
        } else {
            exec("kill -9 $pid > /dev/null 2>&1");
        }
        
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: stopServer | PORT: $port | PID: $pid | RESULT: stopped\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'status' => 'stopped'];
    }
}
