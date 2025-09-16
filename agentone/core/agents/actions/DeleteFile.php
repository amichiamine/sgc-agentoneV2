<?php
/**
 * Action DeleteFile - Suppression de fichiers
 */

require_once dirname(dirname(__DIR__)) . '/utils/PathHelper.php';

class DeleteFile {
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
        
        if (unlink($fullPath)) {
            // Logger l'action
            $logEntry = '[' . date('Y-m-d H:i:s') . "] DELETE_FILE: $filename\n";
            file_put_contents(PathHelper::getLogsPath() . '/actions.log', $logEntry, FILE_APPEND | LOCK_EX);
            
            return [
                'success' => true,
                'message' => "Fichier '$filename' supprimé avec succès",
                'data' => ['filename' => $filename]
            ];
        } else {
            return ['success' => false, 'error' => "Impossible de supprimer le fichier '$filename'"];
        }
    }
}
?>