<?php
/**
 * Action ReadFile - Lecture de fichiers
 */

require_once dirname(dirname(__DIR__)) . '/utils/PathHelper.php';

class ReadFile {
    public function execute($filename) {
        if (empty($filename)) {
            return ['success' => false, 'error' => 'Nom de fichier manquant'];
        }
        
        $securePath = PathHelper::securePath($filename);
        $fullPath = PathHelper::buildPath($securePath);
        
        // Vérifier la sécurité du chemin
        if (!PathHelper::isPathAllowed($fullPath)) {
            return ['success' => false, 'error' => 'Accès refusé : chemin non autorisé'];
        }
        
        if (!file_exists($fullPath)) {
            return ['success' => false, 'error' => "Fichier '$filename' introuvable"];
        }
        
        $content = file_get_contents($fullPath);
        
        // Logger l'action
        $logEntry = '[' . date('Y-m-d H:i:s') . "] READ_FILE: $filename\n";
        file_put_contents(PathHelper::getLogsPath() . '/actions.log', $logEntry, FILE_APPEND | LOCK_EX);
        
        return [
            'success' => true,
            'message' => "Contenu du fichier '$filename'",
            'data' => [
                'filename' => $filename,
                'content' => $content,
                'size' => filesize($fullPath),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath))
            ]
        ];
    }
}
?>