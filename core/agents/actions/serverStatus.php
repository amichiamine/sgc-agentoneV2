<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class ServerStatus {
    public function execute() {
        $configPath = PathHelper::getCorePath() . '/config/settings.json';
        if (!file_exists($configPath)) {
            return ['error' => 'Fichier settings.json introuvable'];
        }
        
        $settings = json_decode(file_get_contents($configPath), true);
        $port = $settings['port'] ?? 5000;
        $host = $settings['host'] ?? '0.0.0.0';
        
        // Vérifier si le serveur est actif
        $process = shell_exec("ps aux | grep \"php -S $host:$port\" | grep -v grep");
        
        if (!empty(trim($process))) {
            return ['success' => true, 'status' => 'running', 'port' => $port, 'host' => $host];
        }
        
        return ['success' => true, 'status' => 'stopped', 'port' => $port, 'host' => $host];
    }
}
