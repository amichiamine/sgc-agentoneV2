<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class ReadFile {
    public function execute($filename) {
        $basePath = PathHelper::getBasePath();
        $path = $basePath . '/' . ltrim($filename, '/');
        
        // 1. Validation path traversal
        $realPath = realpath($path);
        $baseReal = realpath($basePath);
        if (!$realPath || strpos($realPath, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal'];
        }
        
        // 2. Vérifier existence
        if (!file_exists($path)) {
            return ['error' => 'Fichier introuvable'];
        }
        
        // 3. Lire le contenu
        $content = file_get_contents($path);
        
        // 4. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: readFile | FILE: $filename | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'content' => $content];
    }
}
