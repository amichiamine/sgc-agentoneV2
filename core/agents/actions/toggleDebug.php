<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class ToggleDebug {
    public function execute() {
        $configPath = PathHelper::getCorePath() . '/config/settings.json';
        if (!file_exists($configPath)) {
            return ['error' => 'Fichier settings.json introuvable'];
        }
        
        $settings = json_decode(file_get_contents($configPath), true);
        $settings['debug'] = !$settings['debug'] ?? false;
        
        if (file_put_contents($configPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
            file_put_contents(
                PathHelper::getLogsPath() . '/actions.log',
                '[' . date('Y-m-d H:i:s') . "] ACTION: toggleDebug | NEW: " . ($settings['debug'] ? 'true' : 'false') . " | RESULT: updated\n",
                FILE_APPEND | LOCK_EX
            );
            return ['success' => true, 'debug' => $settings['debug']];
        }
        
        return ['error' => 'Échec de mise à jour du mode debug'];
    }
}
