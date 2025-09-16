<?php
/**
 * API Chat - Traitement des commandes de l'assistant avec interpréteur intégré
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['message'])) {
    echo json_encode(['success' => false, 'error' => 'Message manquant']);
    exit;
}

$message = trim($input['message']);

// Créer les dossiers nécessaires s'ils n'existent pas
$logsDir = __DIR__ . '/../core/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

// Parser la commande : "action cible : contenu"
if (strpos($message, ':') !== false) {
    list($actionAndTarget, $content) = explode(':', $message, 2);
    $actionAndTarget = trim($actionAndTarget);
    $content = trim($content);
} else {
    $actionAndTarget = $message;
    $content = '';
}

// Séparer action et cible
$parts = explode(' ', $actionAndTarget, 2);
$action = strtolower(trim($parts[0]));
$target = isset($parts[1]) ? trim($parts[1]) : '';

// Traitement des commandes
try {
    switch ($action) {
        case 'createfile':
            $result = createFile($target, $content);
            break;
            
        case 'readfile':
            $result = readFile($target);
            break;
            
        case 'listdir':
            $result = listDirectory($target ?: '.');
            break;
            
        case 'createdir':
            $result = createDirectory($target);
            break;
            
        case 'deletefile':
            $result = deleteFile($target);
            break;
            
        case 'startserver':
            $result = startServer();
            break;
            
        case 'stopserver':
            $result = stopServer();
            break;
            
        case 'serverstatus':
            $result = getServerStatus();
            break;
            
        case 'help':
            $result = showHelp();
            break;
            
        default:
            $result = [
                'success' => false,
                'error' => "Commande inconnue : $action. Tapez 'help' pour voir les commandes disponibles."
            ];
    }
} catch (Exception $e) {
    $result = [
        'success' => false,
        'error' => 'Erreur lors de l\'exécution : ' . $e->getMessage()
    ];
}

// Logger l'action
$logEntry = '[' . date('Y-m-d H:i:s') . "] USER: \"$message\" | RESULT: " . ($result['success'] ? 'success' : $result['error']) . "\n";
file_put_contents($logsDir . '/chat.log', $logEntry, FILE_APPEND | LOCK_EX);

echo json_encode($result);

// Fonctions de traitement

function createFile($filename, $content) {
    if (empty($filename)) {
        return ['success' => false, 'error' => 'Nom de fichier manquant'];
    }
    
    // Sécurité : empêcher les path traversal
    if (strpos($filename, '..') !== false || strpos($filename, '/') === 0) {
        return ['success' => false, 'error' => 'Nom de fichier non autorisé'];
    }
    
    $filepath = __DIR__ . '/../../' . $filename;
    $dir = dirname($filepath);
    
    // Créer le dossier parent si nécessaire
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (file_put_contents($filepath, $content) !== false) {
        return [
            'success' => true,
            'message' => "Fichier '$filename' créé avec succès",
            'data' => [
                'filename' => $filename,
                'size' => strlen($content),
                'path' => $filepath
            ]
        ];
    } else {
        return ['success' => false, 'error' => "Impossible de créer le fichier '$filename'"];
    }
}

function readFile($filename) {
    if (empty($filename)) {
        return ['success' => false, 'error' => 'Nom de fichier manquant'];
    }
    
    // Sécurité
    if (strpos($filename, '..') !== false || strpos($filename, '/') === 0) {
        return ['success' => false, 'error' => 'Nom de fichier non autorisé'];
    }
    
    $filepath = __DIR__ . '/../../' . $filename;
    
    if (!file_exists($filepath)) {
        return ['success' => false, 'error' => "Fichier '$filename' introuvable"];
    }
    
    $content = file_get_contents($filepath);
    
    return [
        'success' => true,
        'message' => "Contenu du fichier '$filename'",
        'data' => [
            'filename' => $filename,
            'content' => $content,
            'size' => filesize($filepath),
            'modified' => date('Y-m-d H:i:s', filemtime($filepath))
        ]
    ];
}

function listDirectory($dirname) {
    // Sécurité
    if (strpos($dirname, '..') !== false || strpos($dirname, '/') === 0) {
        return ['success' => false, 'error' => 'Nom de dossier non autorisé'];
    }
    
    $dirpath = __DIR__ . '/../../' . $dirname;
    
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

function createDirectory($dirname) {
    if (empty($dirname)) {
        return ['success' => false, 'error' => 'Nom de dossier manquant'];
    }
    
    // Sécurité
    if (strpos($dirname, '..') !== false || strpos($dirname, '/') === 0) {
        return ['success' => false, 'error' => 'Nom de dossier non autorisé'];
    }
    
    $dirpath = __DIR__ . '/../../' . $dirname;
    
    if (is_dir($dirpath)) {
        return ['success' => false, 'error' => "Le dossier '$dirname' existe déjà"];
    }
    
    if (mkdir($dirpath, 0755, true)) {
        return [
            'success' => true,
            'message' => "Dossier '$dirname' créé avec succès",
            'data' => [
                'directory' => $dirname,
                'path' => $dirpath
            ]
        ];
    } else {
        return ['success' => false, 'error' => "Impossible de créer le dossier '$dirname'"];
    }
}

function deleteFile($filename) {
    if (empty($filename)) {
        return ['success' => false, 'error' => 'Nom de fichier manquant'];
    }
    
    // Sécurité
    if (strpos($filename, '..') !== false || strpos($filename, '/') === 0) {
        return ['success' => false, 'error' => 'Nom de fichier non autorisé'];
    }
    
    $filepath = __DIR__ . '/../../' . $filename;
    
    if (!file_exists($filepath)) {
        return ['success' => false, 'error' => "Fichier '$filename' introuvable"];
    }
    
    if (unlink($filepath)) {
        return [
            'success' => true,
            'message' => "Fichier '$filename' supprimé avec succès",
            'data' => [
                'filename' => $filename
            ]
        ];
    } else {
        return ['success' => false, 'error' => "Impossible de supprimer le fichier '$filename'"];
    }
}

function startServer() {
    $configPath = __DIR__ . '/../core/config/settings.json';
    $config = ['port' => 5000, 'host' => '0.0.0.0'];
    
    if (file_exists($configPath)) {
        $savedConfig = json_decode(file_get_contents($configPath), true);
        if ($savedConfig && isset($savedConfig['server'])) {
            $config = array_merge($config, $savedConfig['server']);
        }
    }
    
    $port = $config['port'];
    $host = $config['host'];
    
    // Vérifier si le serveur est déjà en cours
    $connection = @fsockopen($host === '0.0.0.0' ? 'localhost' : $host, $port, $errno, $errstr, 1);
    if ($connection) {
        fclose($connection);
        return ['success' => false, 'error' => 'Le serveur est déjà en cours d\'exécution'];
    }
    
    // Démarrer le serveur
    $documentRoot = __DIR__ . '/..';
    $indexFile = $documentRoot . '/index.php';
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = "start /B php -S $host:$port -t " . escapeshellarg($documentRoot) . " " . escapeshellarg($indexFile) . " > nul 2>&1";
    } else {
        $command = "php -S $host:$port -t " . escapeshellarg($documentRoot) . " " . escapeshellarg($indexFile) . " > /dev/null 2>&1 &";
    }
    
    exec($command);
    sleep(2);
    
    // Vérifier si le serveur a démarré
    $connection = @fsockopen($host === '0.0.0.0' ? 'localhost' : $host, $port, $errno, $errstr, 1);
    if ($connection) {
        fclose($connection);
        return ['success' => true, 'message' => "Serveur démarré sur $host:$port"];
    } else {
        return ['success' => false, 'error' => 'Impossible de démarrer le serveur'];
    }
}

function stopServer() {
    $configPath = __DIR__ . '/../core/config/settings.json';
    $config = ['port' => 5000];
    
    if (file_exists($configPath)) {
        $savedConfig = json_decode(file_get_contents($configPath), true);
        if ($savedConfig && isset($savedConfig['server'])) {
            $config = array_merge($config, $savedConfig['server']);
        }
    }
    
    $port = $config['port'];
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec("for /f \"tokens=5\" %a in ('netstat -aon ^| find \":$port\"') do taskkill /f /pid %a > nul 2>&1");
    } else {
        exec("pkill -f 'php -S.*:$port' > /dev/null 2>&1");
    }
    
    return ['success' => true, 'message' => 'Serveur arrêté'];
}

function getServerStatus() {
    $configPath = __DIR__ . '/../core/config/settings.json';
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

function showHelp() {
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
            ],
            'examples' => [
                'createFile test.txt : Bonjour le monde !',
                'readFile test.txt',
                'listDir .',
                'createDir mon-projet',
                'deleteFile test.txt',
                'startServer',
                'serverStatus'
            ]
        ]
    ];
}
?>