<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class CopyDir {
    public function execute($source, $destination) {
        if (empty($destination)) {
            return ['error' => 'Destination manquante'];
        }
        
        $basePath = PathHelper::getBasePath();
        $sourcePath = $basePath . '/' . ltrim($source, '/');
        $destPath = $basePath . '/' . ltrim($destination, '/');
        
        // 1. Validation path traversal
        $realSource = realpath($sourcePath);
        $realDest = realpath($destPath);
        $baseReal = realpath($basePath);
        
        if (!$realSource || strpos($realSource, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal (source)'];
        }
        
        if ($realDest && strpos($realDest, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal (destination)'];
        }
        
        // 2. Vérifier existence source
        if (!is_dir($sourcePath)) {
            return ['error' => 'Dossier source introuvable'];
        }
        
        // 3. Vérifier conflit cible
        if (is_dir($destPath)) {
            return ['error' => 'Le dossier cible existe déjà'];
        }
        
        // 4. Créer le dossier parent si nécessaire
        $destDir = dirname($destPath);
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        
        // 5. Copier récursivement
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), strlen($sourcePath) + 1);
            $targetPath = $destPath . DIRECTORY_SEPARATOR . $relativePath;
            
            if ($item->isDir()) {
                mkdir($targetPath, 0755, true);
            } else {
                copy($item->getPathname(), $targetPath);
            }
        }
        
        // 6. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: copyDir | FROM: $source | TO: $destination | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'from' => $source, 'to' => $destination];
    }
}
