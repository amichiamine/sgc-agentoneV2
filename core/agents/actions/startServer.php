<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class StartServer {
    public function execute() {
        $configPath = PathHelper::getCorePath() . '/config/settings.json';
        if (!file_exists($configPath)) {
            return ['error' => 'Fichier settings.json introuvable'];
        }
        
        $settings = json_decode(file_get_contents($configPath), true);
        $port = $settings['port'] ?? 5000;
        $host = $settings['host'] ?? '0.0.0.0';
        
        // Vérifier si le serveur est déjà actif
        $process = shell_exec("ps aux | grep \"php -S $host:$port\" | grep -v grep");
        if (!empty(trim($process))) {
            return ['success' => true, 'status' => 'already_running'];
        }
        
        // Démarrer le serveur
        $command = "cd " . escapeshellarg(PathHelper::getBasePath()) . " && php -S $host:$port > /dev/null 2>&1 &";
        exec($command, $output, $returnCode);
        
        if ($returnCode === 0) {
            file_put_contents(
                PathHelper::getLogsPath() . '/actions.log',
                '[' . date('Y-m-d H:i:s') . "] ACTION: startServer | PORT: $port | HOST: $host | RESULT: started\n",
                FILE_APPEND | LOCK_EX
            );
            return ['success' => true, 'status' => 'started'];
        }
        
        return ['error' => 'Impossible de démarrer le serveur PHP'];
    }
}
