<?php
namespace core\agents\actions;

use core\utils\PathHelper;

class ListDir {
    public function execute($dirname) {
        $basePath = PathHelper::getBasePath();
        $path = $basePath . '/' . ltrim($dirname, '/');
        
        // 1. Validation path traversal
        $realPath = realpath($path);
        $baseReal = realpath($basePath);
        if (!$realPath || strpos($realPath, $baseReal) !== 0) {
            return ['error' => 'Accès refusé : path traversal'];
        }
        
        // 2. Vérifier existence
        if (!is_dir($path)) {
            return ['error' => 'Dossier introuvable'];
        }
        
        // 3. Lister
        $items = [];
        $iterator = new \DirectoryIterator($path);
        foreach ($iterator as $item) {
            if ($item->isDot()) continue;
            $items[] = [
                'name' => $item->getFilename(),
                'type' => $item->isDir() ? 'directory' : 'file',
                'size' => $item->isFile() ? $item->getSize() : 0,
                'modified' => $item->getMTime()
            ];
        }
        
        // 4. Log
        file_put_contents(
            PathHelper::getLogsPath() . '/actions.log',
            '[' . date('Y-m-d H:i:s') . "] ACTION: listDir | DIR: $dirname | RESULT: success | count: " . count($items) . "\n",
            FILE_APPEND | LOCK_EX
        );
        
        return ['success' => true, 'items' => $items];
    }
}
