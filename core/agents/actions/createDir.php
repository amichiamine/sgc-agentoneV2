<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class CreateDir {
    public function execute($dirname) {
        $basePath = PathHelper::getBasePath();
        $path = $basePath . '/' . ltrim($dirname, '/');
        
        // 1. Validation path traversal
        $realPath = realpath($path);
        $baseReal = realpath($basePath);
        if (!$realPath || strpos($realPath, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal'];
        }
        
        // 2. Créer le dossier et ses parents
        if (!is_dir($path)) mkdir($path, 0755, true);
        
        // 3. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: createDir | DIR: $dirname | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'dir' => $dirname];
    }
}
