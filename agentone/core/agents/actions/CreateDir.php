<?php
/**
 * Action CreateDir - Création de dossiers
 */

require_once dirname(dirname(__DIR__)) . '/utils/PathHelper.php';

class CreateDir {
    public function execute($dirname) {
        if (empty($dirname)) {
            return ['success' => false, 'error' => 'Nom de dossier manquant'];
        }
        
        $securePath = PathHelper::securePath($dirname);
        $fullPath = PathHelper::buildPath($securePath);
        
        // Vérifier la sécurité du chemin
        if (!PathHelper::isPathAllowed(dirname($fullPath))) {
            return ['success' => false, 'error' => 'Accès refusé : chemin non autorisé'];
        }
        
        if (is_dir($fullPath)) {
            return ['success' => false, 'error' => "Le dossier '$dirname' existe déjà"];
        }
        
        if (mkdir($fullPath, 0755, true)) {
            // Logger l'action
            $logEntry = '[' . date('Y-m-d H:i:s') . "] CREATE_DIR: $dirname\n";
            file_put_contents(PathHelper::getLogsPath() . '/actions.log', $logEntry, FILE_APPEND | LOCK_EX);
            
            return [
                'success' => true,
                'message' => "Dossier '$dirname' créé avec succès",
                'data' => ['directory' => $dirname]
            ];
        } else {
            return ['success' => false, 'error' => "Impossible de créer le dossier '$dirname'"];
        }
    }
}
?>