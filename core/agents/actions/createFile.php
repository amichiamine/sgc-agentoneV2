<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class CreateFile {
    public function execute($filename, $content = null) {
        $basePath = PathHelper::getBasePath();
        $path = $basePath . '/' . ltrim($filename, '/');
        
        // 1. Validation path traversal
        $realPath = realpath($path);
        $baseReal = realpath($basePath);
        if (!$realPath || strpos($realPath, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal'];
        }
        
        // 2. Créer les dossiers parents
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        // 3. Écrire le contenu
        file_put_contents($path, $content ?? '');
        
        // 4. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: createFile | FILE: $filename | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'file' => $filename];
    }
}
