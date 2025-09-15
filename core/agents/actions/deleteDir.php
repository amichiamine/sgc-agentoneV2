<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class DeleteDir {
    public function execute($dirname) {
        $basePath = PathHelper::getBasePath();
        $path = $basePath . '/' . ltrim($dirname, '/');
        
        // 1. Validation path traversal
        $realPath = realpath($path);
        $baseReal = realpath($basePath);
        if (!$realPath || strpos($realPath, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal'];
        }
        
        // 2. Vérifier existence
        if (!is_dir($path)) {
            return ['error' => 'Dossier introuvable'];
        }
        
        // 3. Supprimer récursivement
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        
        if (!rmdir($path)) {
            return ['error' => 'Échec de suppression du dossier'];
        }
        
        // 4. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: deleteDir | DIR: $dirname | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'dir' => $dirname];
    }
}
