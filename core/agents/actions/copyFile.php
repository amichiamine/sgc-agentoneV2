<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class CopyFile {
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
        if (!file_exists($sourcePath)) {
            return ['error' => 'Fichier source introuvable'];
        }
        
        // 3. Vérifier conflit cible
        if (file_exists($destPath)) {
            return ['error' => 'Le fichier cible existe déjà'];
        }
        
        // 4. Créer le dossier parent si nécessaire
        $destDir = dirname($destPath);
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        
        // 5. Copier
        if (!copy($sourcePath, $destPath)) {
            return ['error' => 'Échec de la copie'];
        }
        
        // 6. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: copyFile | FROM: $source | TO: $destination | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'from' => $source, 'to' => $destination];
    }
}
