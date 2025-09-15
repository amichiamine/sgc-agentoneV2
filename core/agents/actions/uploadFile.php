<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class UploadFile {
    public function execute($fileUpload, $destination = '') {
        if (!isset($fileUpload['tmp_name']) || !is_uploaded_file($fileUpload['tmp_name'])) {
            return ['error' => 'Fichier invalide ou non uploadé'];
        }
        
        $basePath = PathHelper::getBasePath();
        $filename = basename($fileUpload['name']);
        $targetPath = $basePath . '/' . ltrim($destination, '/') . '/' . $filename;
        
        // Si destination non spécifiée, utiliser le nom original dans la racine
        if (empty($destination)) {
            $targetPath = $basePath . '/' . $filename;
        }
        
        // 1. Validation path traversal
        $realTarget = realpath($targetPath);
        $baseReal = realpath($basePath);
        if ($realTarget && strpos($realTarget, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal'];
        }
        
        // 2. Créer le dossier parent si nécessaire
        $dir = dirname($targetPath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        // 3. Déplacer le fichier uploadé
        if (!move_uploaded_file($fileUpload['tmp_name'], $targetPath)) {
            return ['error' => 'Échec du téléchargement'];
        }
        
        // 4. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: uploadFile | FILE: $filename | TO: $targetPath | RESULT: success\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'file' => $targetPath];
    }
}
