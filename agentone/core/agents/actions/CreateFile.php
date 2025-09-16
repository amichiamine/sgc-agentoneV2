<?php
/**
 * Action CreateFile - Création de fichiers
 */

require_once dirname(dirname(__DIR__)) . '/utils/PathHelper.php';

class CreateFile {
    public function execute($filename, $content = '') {
        if (empty($filename)) {
            return ['success' => false, 'error' => 'Nom de fichier manquant'];
        }
        
        $securePath = PathHelper::securePath($filename);
        $fullPath = PathHelper::buildPath($securePath);
        
        // Vérifier la sécurité du chemin
        if (!PathHelper::isPathAllowed($fullPath)) {
            return ['success' => false, 'error' => 'Accès refusé : chemin non autorisé'];
        }
        
        // Créer le dossier parent si nécessaire
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Créer le fichier
        if (file_put_contents($fullPath, $content) !== false) {
            // Logger l'action
            $logEntry = '[' . date('Y-m-d H:i:s') . "] CREATE_FILE: $filename\n";
            file_put_contents(PathHelper::getLogsPath() . '/actions.log', $logEntry, FILE_APPEND | LOCK_EX);
            
            return [
                'success' => true,
                'message' => "Fichier '$filename' créé avec succès",
                'data' => [
                    'filename' => $filename,
                    'size' => strlen($content),
                    'path' => $fullPath
                ]
            ];
        } else {
            return ['success' => false, 'error' => "Impossible de créer le fichier '$filename'"];
        }
    }
}
?>