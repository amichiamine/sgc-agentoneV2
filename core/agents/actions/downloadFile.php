<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class DownloadFile {
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
        
        // 3. Forcer le téléchargement
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
