<?php
/**
 * API Server - Contrôle du serveur PHP intégré
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

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Action manquante']);
    exit;
}

$action = $input['action'];
$configPath = __DIR__ . '/../core/config/server.json';

try {
    switch ($action) {
        case 'serverStatus':
            $result = getServerStatus();
            break;
            
        case 'startServer':
            $result = startServer($input);
            break;
            
        case 'stopServer':
            $result = stopServer();
            break;
            
        case 'restartServer':
            $result = restartServer();
            break;
            
        case 'saveConfig':
            $result = saveConfig($input['config'] ?? []);
            break;
            
        case 'serverLogs':
            $result = getServerLogs();
            break;
            
        default:
            $result = ['success' => false, 'error' => "Action inconnue : $action"];
    }
} catch (Exception $e) {
    $result = ['success' => false, 'error' => 'Erreur serveur : ' . $e->getMessage()];
}

echo json_encode($result);

// Fonctions utilitaires

function getConfig() {
    global $configPath;
    
    $defaultConfig = [
        'port' => 5000,
        'host' => '0.0.0.0',
        'debug' => false,
        'document_root' => '.'
    ];
    
    if (file_exists($configPath)) {
        $config = json_decode(file_get_contents($configPath), true);
        return array_merge($defaultConfig, $config ?: []);
    }
    
    return $defaultConfig;
}

function saveConfig($newConfig) {
    global $configPath;
    
    $config = getConfig();
    $config = array_merge($config, $newConfig);
    
    // Créer le dossier si nécessaire
    $configDir = dirname($configPath);
    if (!is_dir($configDir)) {
        mkdir($configDir, 0755, true);
    }
    
    if (file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT))) {
        return ['success' => true, 'message' => 'Configuration sauvegardée'];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder la configuration'];
    }
}

function getServerStatus() {
    $config = getConfig();
    $port = $config['port'];
    $host = $config['host'];
    
    // Vérifier si le port est utilisé
    $connection = @fsockopen($host === '0.0.0.0' ? 'localhost' : $host, $port, $errno, $errstr, 1);
    
    if ($connection) {
        fclose($connection);
        
        // Essayer de récupérer le PID
        $pid = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec("netstat -ano | findstr :$port");
            if (preg_match('/:\d+\s+(\d+)/', $output, $matches)) {
                $pid = $matches[1];
            }
        } else {
            $output = shell_exec("lsof -i :$port -t 2>/dev/null");
            if (!empty(trim($output))) {
                $pid = trim($output);
            }
        }
        
        return [
            'success' => true,
            'data' => [
                'status' => 'running',
                'port' => $port,
                'host' => $host,
                'pid' => $pid,
                'debug' => $config['debug']
            ]
        ];
    } else {
        return [
            'success' => true,
            'data' => [
                'status' => 'stopped',
                'port' => $port,
                'host' => $host,
                'debug' => $config['debug']
            ]
        ];
    }
}

function startServer($input) {
    $config = getConfig();
    
    // Mettre à jour la config si des paramètres sont fournis
    if (isset($input['port'])) $config['port'] = $input['port'];
    if (isset($input['host'])) $config['host'] = $input['host'];
    if (isset($input['debug'])) $config['debug'] = $input['debug'];
    
    $port = $config['port'];
    $host = $config['host'];
    $documentRoot = __DIR__ . '/..';
    
    // Vérifier si le serveur est déjà en cours d'exécution
    $status = getServerStatus();
    if ($status['data']['status'] === 'running') {
        return ['success' => false, 'error' => 'Le serveur est déjà en cours d\'exécution'];
    }
    
    // Commande pour démarrer le serveur
    $indexFile = $documentRoot . '/index.php';
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = "start /B php -S $host:$port -t " . escapeshellarg($documentRoot) . " " . escapeshellarg($indexFile) . " > nul 2>&1";
    } else {
        $command = "php -S $host:$port -t " . escapeshellarg($documentRoot) . " " . escapeshellarg($indexFile) . " > /dev/null 2>&1 &";
    }
    
    // Exécuter la commande
    exec($command, $output, $returnCode);
    
    // Sauvegarder la configuration
    saveConfig($config);
    
    // Attendre un peu pour que le serveur démarre
    sleep(2);
    
    // Vérifier si le serveur a démarré
    $newStatus = getServerStatus();
    if ($newStatus['data']['status'] === 'running') {
        logAction("Serveur démarré sur $host:$port");
        return ['success' => true, 'message' => 'Serveur démarré avec succès'];
    } else {
        return ['success' => false, 'error' => 'Impossible de démarrer le serveur'];
    }
}

function stopServer() {
    $status = getServerStatus();
    
    if ($status['data']['status'] !== 'running') {
        return ['success' => false, 'error' => 'Le serveur n\'est pas en cours d\'exécution'];
    }
    
    $port = $status['data']['port'];
    $pid = $status['data']['pid'];
    
    if ($pid) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec("taskkill /PID $pid /F > nul 2>&1");
        } else {
            exec("kill -9 $pid > /dev/null 2>&1");
        }
        
        // Attendre un peu
        sleep(1);
        
        // Vérifier si le serveur s'est arrêté
        $newStatus = getServerStatus();
        if ($newStatus['data']['status'] === 'stopped') {
            logAction("Serveur arrêté (PID: $pid)");
            return ['success' => true, 'message' => 'Serveur arrêté avec succès'];
        } else {
            return ['success' => false, 'error' => 'Impossible d\'arrêter le serveur'];
        }
    } else {
        return ['success' => false, 'error' => 'Impossible de trouver le processus du serveur'];
    }
}

function restartServer() {
    $stopResult = stopServer();
    if (!$stopResult['success']) {
        return $stopResult;
    }
    
    sleep(2);
    
    $startResult = startServer([]);
    return $startResult;
}

function getServerLogs() {
    $logPath = __DIR__ . '/../core/logs/server.log';
    
    if (!file_exists($logPath)) {
        return [
            'success' => true,
            'data' => ['logs' => []]
        ];
    }
    
    $logs = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Garder seulement les 100 dernières lignes
    $logs = array_slice($logs, -100);
    
    return [
        'success' => true,
        'data' => ['logs' => $logs]
    ];
}

function logAction($message) {
    $logPath = __DIR__ . '/../core/logs/server.log';
    $logDir = dirname($logPath);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] INFO: $message\n";
    
    file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
}
?>