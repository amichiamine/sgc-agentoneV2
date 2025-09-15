<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class RenameDir {
    public function execute($oldName, $newName) {
        if (empty($newName)) {
            return ['error' => 'Nouveau nom manquant'];
        }
        
        $basePath = PathHelper::getBasePath();
        $oldPath = $basePath . '/' . ltrim($oldName, '/');
        $newPath = $basePath . '/' . ltrim($newName, '/');
        
        // 1. Validation path traversal
        $realOld = realpath($oldPath);
        $realNew = realpath($newPath);
        $baseReal = realpath($basePath);
        
        if (!$realOld || strpos($realOld, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal (source)'];
        }
        
        if ($realNew && strpos($realNew, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal (cible)'];
        }
        
        // 2. Vérifier existence source
        if (!is_dir($oldPath)) {
            return ['error' => 'Dossier source introuvable'];
        }
        
        // 3. Vérifier conflit cible
        if (is_dir($newPath)) {
            return ['error' => 'Le dossier cible existe déjà'];
        }
        
        // 4. Renommer
        if (!rename($oldPath, $newPath)) {
            return ['error' => 'Échec du renommage'];
        }
        
        // 5. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: renameDir | FROM: $oldName | TO: $newName | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'old' => $oldName, 'new' => $newName];
    }
}
