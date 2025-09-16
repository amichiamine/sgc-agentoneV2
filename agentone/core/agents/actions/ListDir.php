<?php
/**
 * Action ListDir - Listage de dossiers
 */

require_once dirname(dirname(__DIR__)) . '/utils/PathHelper.php';

class ListDir {
    public function execute($dirname = '.') {
        $securePath = PathHelper::securePath($dirname);
        $fullPath = PathHelper::buildPath($securePath);
        
        // Vérifier la sécurité du chemin
        if (!PathHelper::isPathAllowed($fullPath)) {
            return ['success' => false, 'error' => 'Accès refusé : chemin non autorisé'];
        }
        
        if (!is_dir($fullPath)) {
            return ['success' => false, 'error' => "Dossier '$dirname' introuvable"];
        }
        
        $items = [];
        $iterator = new DirectoryIterator($fullPath);
        
        foreach ($iterator as $item) {
            if ($item->isDot()) continue;
            
            $items[] = [
                'name' => $item->getFilename(),
                'type' => $item->isDir() ? 'directory' : 'file',
                'size' => $item->isFile() ? $item->getSize() : 0,
                'modified' => date('Y-m-d H:i:s', $item->getMTime()),
                'permissions' => substr(sprintf('%o', $item->getPerms()), -4)
            ];
        }
        
        // Trier : dossiers d'abord, puis par nom
        usort($items, function($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'directory' ? -1 : 1;
            }
            return strcasecmp($a['name'], $b['name']);
        });
        
        // Logger l'action
        $logEntry = '[' . date('Y-m-d H:i:s') . "] LIST_DIR: $dirname (" . count($items) . " items)\n";
        file_put_contents(PathHelper::getLogsPath() . '/actions.log', $logEntry, FILE_APPEND | LOCK_EX);
        
        return [
            'success' => true,
            'message' => "Contenu du dossier '$dirname' (" . count($items) . " éléments)",
            'data' => [
                'directory' => $dirname,
                'items' => $items,
                'total' => count($items)
            ]
        ];
    }
}
?>