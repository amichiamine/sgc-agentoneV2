<?php
/**
 * Interpréteur de commandes - Traite les commandes en langage naturel
 */

class Interpreter {
    private $basePath;
    private $logsPath;
    
    public function __construct() {
        $this->basePath = dirname(dirname(__DIR__));
        $this->logsPath = $this->basePath . '/core/logs';
        
        // Créer le dossier logs s'il n'existe pas
        if (!is_dir($this->logsPath)) {
            mkdir($this->logsPath, 0755, true);
        }
    }
    
    /**
     * Interpréter une commande
     */
    public function interpret($prompt) {
        $prompt = trim($prompt);
        
        // Log de la commande
        $this->logAction("COMMAND: $prompt");
        
        // Parser la commande
        if (strpos($prompt, ':') !== false) {
            list($actionAndTarget, $content) = explode(':', $prompt, 2);
            $actionAndTarget = trim($actionAndTarget);
            $content = trim($content);
        } else {
            $actionAndTarget = $prompt;
            $content = '';
        }
        
        // Séparer action et cible
        $parts = explode(' ', $actionAndTarget, 2);
        $action = strtolower(trim($parts[0]));
        $target = isset($parts[1]) ? trim($parts[1]) : '';
        
        try {
            switch ($action) {
                case 'createfile':
                    return $this->createFile($target, $content);
                    
                case 'readfile':
                    return $this->readFile($target);
                    
                case 'listdir':
                    return $this->listDirectory($target ?: '.');
                    
                case 'createdir':
                    return $this->createDirectory($target);
                    
                case 'deletefile':
                    return $this->deleteFile($target);
                    
                case 'startserver':
                    return $this->startServer();
                    
                case 'stopserver':
                    return $this->stopServer();
                    
                case 'serverstatus':
                    return $this->getServerStatus();
                    
                case 'help':
                    return $this->showHelp();
                    
                default:
                    return [
                        'success' => false,
                        'error' => "Commande inconnue : $action. Tapez 'help' pour voir les commandes disponibles."
                    ];
            }
        } catch (Exception $e) {
            $this->logAction("ERROR: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur lors de l\'exécution : ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Créer un fichier
     */
    private function createFile($filename, $content) {
        if (empty($filename)) {
            return ['success' => false, 'error' => 'Nom de fichier manquant'];
        }
        
        $filename = $this->securePath($filename);
        $filepath = $this->basePath . '/' . $filename;
        $dir = dirname($filepath);
        
        // Créer le dossier parent si nécessaire
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (file_put_contents($filepath, $content) !== false) {
            $this->logAction("CREATE_FILE: $filename");
            return [
                'success' => true,
                'message' => "Fichier '$filename' créé avec succès",
                'data' => [
                    'filename' => $filename,
                    'size' => strlen($content)
                ]
            ];
        } else {
            return ['success' => false, 'error' => "Impossible de créer le fichier '$filename'"];
        }
    }
    
    /**
     * Lire un fichier
     */
    private function readFile($filename) {
        if (empty($filename)) {
            return ['success' => false, 'error' => 'Nom de fichier manquant'];
        }
        
        $filename = $this->securePath($filename);
        $filepath = $this->basePath . '/' . $filename;
        
        if (!file_exists($filepath)) {
            return ['success' => false, 'error' => "Fichier '$filename' introuvable"];
        }
        
        $content = file_get_contents($filepath);
        $this->logAction("READ_FILE: $filename");
        
        return [
            'success' => true,
            'message' => "Contenu du fichier '$filename'",
            'data' => [
                'filename' => $filename,
                'content' => $content,
                'size' => filesize($filepath)
            ]
        ];
    }
    
    /**
     * Lister un dossier
     */
    private function listDirectory($dirname) {
        $dirname = $this->securePath($dirname);
        $dirpath = $this->basePath . '/' . $dirname;
        
        if (!is_dir($dirpath)) {
            return ['success' => false, 'error' => "Dossier '$dirname' introuvable"];
        }
        
        $items = [];
        $iterator = new DirectoryIterator($dirpath);
        
        foreach ($iterator as $item) {
            if ($item->isDot()) continue;
            
            $items[] = [
                'name' => $item->getFilename(),
                'type' => $item->isDir() ? 'directory' : 'file',
                'size' => $item->isFile() ? $item->getSize() : 0,
                'modified' => date('Y-m-d H:i:s', $item->getMTime())
            ];
        }
        
        $this->logAction("LIST_DIR: $dirname (" . count($items) . " items)");
        
        return [
            'success' => true,
            'message' => "Contenu du dossier '$dirname' (" . count($items) . " éléments)",
            'data' => [
                'directory' => $dirname,
                'items' => $items
            ]
        ];
    }
    
    /**
     * Créer un dossier
     */
    private function createDirectory($dirname) {
        if (empty($dirname)) {
            return ['success' => false, 'error' => 'Nom de dossier manquant'];
        }
        
        $dirname = $this->securePath($dirname);
        $dirpath = $this->basePath . '/' . $dirname;
        
        if (is_dir($dirpath)) {
            return ['success' => false, 'error' => "Le dossier '$dirname' existe déjà"];
        }
        
        if (mkdir($dirpath, 0755, true)) {
            $this->logAction("CREATE_DIR: $dirname");
            return [
                'success' => true,
                'message' => "Dossier '$dirname' créé avec succès",
                'data' => ['directory' => $dirname]
            ];
        } else {
            return ['success' => false, 'error' => "Impossible de créer le dossier '$dirname'"];
        }
    }
    
    /**
     * Supprimer un fichier
     */
    private function deleteFile($filename) {
        if (empty($filename)) {
            return ['success' => false, 'error' => 'Nom de fichier manquant'];
        }
        
        $filename = $this->securePath($filename);
        $filepath = $this->basePath . '/' . $filename;
        
        if (!file_exists($filepath)) {
            return ['success' => false, 'error' => "Fichier '$filename' introuvable"];
        }
        
        if (unlink($filepath)) {
            $this->logAction("DELETE_FILE: $filename");
            return [
                'success' => true,
                'message' => "Fichier '$filename' supprimé avec succès",
                'data' => ['filename' => $filename]
            ];
        } else {
            return ['success' => false, 'error' => "Impossible de supprimer le fichier '$filename'"];
        }
    }
    
    /**
     * Démarrer le serveur
     */
    private function startServer() {
        // Déléguer à l'API server
        $serverApi = $this->basePath . '/api/server.php';
        if (file_exists($serverApi)) {
            // Simuler un appel POST
            $_POST = ['action' => 'startServer'];
            ob_start();
            include $serverApi;
            $output = ob_get_clean();
            $result = json_decode($output, true);
            return $result ?: ['success' => false, 'error' => 'Erreur serveur'];
        }
        
        return ['success' => false, 'error' => 'API serveur non disponible'];
    }
    
    /**
     * Arrêter le serveur
     */
    private function stopServer() {
        // Déléguer à l'API server
        $serverApi = $this->basePath . '/api/server.php';
        if (file_exists($serverApi)) {
            $_POST = ['action' => 'stopServer'];
            ob_start();
            include $serverApi;
            $output = ob_get_clean();
            $result = json_decode($output, true);
            return $result ?: ['success' => false, 'error' => 'Erreur serveur'];
        }
        
        return ['success' => false, 'error' => 'API serveur non disponible'];
    }
    
    /**
     * Obtenir le statut du serveur
     */
    private function getServerStatus() {
        $configPath = $this->basePath . '/core/config/settings.json';
        $config = ['port' => 5000, 'host' => '0.0.0.0'];
        
        if (file_exists($configPath)) {
            $savedConfig = json_decode(file_get_contents($configPath), true);
            if ($savedConfig && isset($savedConfig['server'])) {
                $config = array_merge($config, $savedConfig['server']);
            }
        }
        
        $port = $config['port'];
        $host = $config['host'];
        
        $connection = @fsockopen($host === '0.0.0.0' ? 'localhost' : $host, $port, $errno, $errstr, 1);
        if ($connection) {
            fclose($connection);
            return [
                'success' => true,
                'message' => "Serveur actif sur $host:$port",
                'data' => ['status' => 'running', 'port' => $port, 'host' => $host]
            ];
        } else {
            return [
                'success' => true,
                'message' => "Serveur arrêté",
                'data' => ['status' => 'stopped', 'port' => $port, 'host' => $host]
            ];
        }
    }
    
    /**
     * Afficher l'aide
     */
    private function showHelp() {
        return [
            'success' => true,
            'message' => 'Commandes disponibles',
            'data' => [
                'commands' => [
                    'createFile nom.txt : contenu' => 'Créer un fichier avec du contenu',
                    'readFile nom.txt' => 'Lire le contenu d\'un fichier',
                    'listDir dossier' => 'Lister le contenu d\'un dossier',
                    'createDir nouveau-dossier' => 'Créer un nouveau dossier',
                    'deleteFile nom.txt' => 'Supprimer un fichier',
                    'startServer' => 'Démarrer le serveur PHP',
                    'stopServer' => 'Arrêter le serveur PHP',
                    'serverStatus' => 'Vérifier l\'état du serveur',
                    'help' => 'Afficher cette aide'
                ]
            ]
        ];
    }
    
    /**
     * Sécuriser un chemin
     */
    private function securePath($path) {
        // Supprimer les tentatives de path traversal
        $path = str_replace(['../', '..\\', '../', '..\\'], '', $path);
        $path = ltrim($path, '/\\');
        return $path;
    }
    
    /**
     * Logger une action
     */
    private function logAction($message) {
        $logFile = $this->logsPath . '/actions.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
?>