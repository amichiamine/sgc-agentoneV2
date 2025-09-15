<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class SetPort {
    public function execute() {
        $configPath = PathHelper::getCorePath() . '/config/settings.json';
        if (!file_exists($configPath)) {
            return ['error' => 'Fichier settings.json introuvable'];
        }
        
        $settings = json_decode(file_get_contents($configPath), true);
        $newPort = $_POST['port'] ?? null;
        
        if (!$newPort || !is_numeric($newPort) || $newPort < 1 || $newPort > 65535) {
            return ['error' => 'Port invalide'];
        }
        
        $settings['port'] = (int)$newPort;
        
        if (file_put_contents($configPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
            file_put_contents(
                PathHelper::getLogsPath() . '/actions.log',
                '[' . date('Y-m-d H:i:s') . "] ACTION: setPort | OLD: {$settings['port']} | NEW: $newPort | RESULT: updated\n",
                FILE_APPEND | LOCK_EX
            );
            return ['success' => true, 'port' => $newPort];
        }
        
        return ['error' => 'Échec de mise à jour du port'];
    }
}
