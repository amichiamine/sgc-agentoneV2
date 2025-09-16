<?php
/**
 * API Settings - Gestion des paramètres de l'application
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
$settingsPath = __DIR__ . '/../core/config/settings.json';

try {
    switch ($action) {
        case 'getSettings':
            $result = getSettings();
            break;
            
        case 'saveSettings':
            $result = saveSettings($input['settings'] ?? []);
            break;
            
        case 'resetSettings':
            $result = resetSettings();
            break;
            
        case 'clearLogs':
            $result = clearLogs();
            break;
            
        case 'factoryReset':
            $result = factoryReset();
            break;
            
        default:
            $result = ['success' => false, 'error' => "Action inconnue : $action"];
    }
} catch (Exception $e) {
    $result = ['success' => false, 'error' => 'Erreur : ' . $e->getMessage()];
}

echo json_encode($result);

// Fonctions utilitaires

function getDefaultSettings() {
    return [
        'appearance' => [
            'title' => 'SGC-AgentOne',
            'subtitle' => 'v3.0 - Architecture Modulaire',
            'darkMode' => true,
            'primaryColor' => '#1ab8b8',
            'secondaryColor' => '#2d3a45',
            'accentColor' => '#1ab8b8',
            'fontSize' => 'medium'
        ],
        'server' => [
            'port' => 5000,
            'host' => '0.0.0.0',
            'autoStart' => false,
            'debugMode' => false
        ],
        'editor' => [
            'theme' => 'dark',
            'fontSize' => 14,
            'tabSize' => 4,
            'lineNumbers' => true,
            'autoSave' => true
        ],
        'security' => [
            'blindExec' => false,
            'maxFileSize' => 50,
            'actionLogging' => true,
            'sessionTimeout' => 60
        ],
        'advanced' => [
            'memoryLimit' => 256,
            'executionTime' => 300,
            'fileCache' => true,
            'backupFrequency' => 'weekly'
        ]
    ];
}

function getSettings() {
    global $settingsPath;
    
    if (file_exists($settingsPath)) {
        $settings = json_decode(file_get_contents($settingsPath), true);
        if ($settings) {
            // Fusionner avec les paramètres par défaut pour les nouvelles options
            $defaultSettings = getDefaultSettings();
            $settings = array_merge_recursive($defaultSettings, $settings);
        } else {
            $settings = getDefaultSettings();
        }
    } else {
        $settings = getDefaultSettings();
    }
    
    return [
        'success' => true,
        'data' => ['settings' => $settings]
    ];
}

function saveSettings($newSettings) {
    global $settingsPath;
    
    if (empty($newSettings)) {
        return ['success' => false, 'error' => 'Paramètres manquants'];
    }
    
    // Créer le dossier si nécessaire
    $dir = dirname($settingsPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Ajouter les métadonnées
    $newSettings['_meta'] = [
        'version' => '3.0',
        'saved_at' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION
    ];
    
    if (file_put_contents($settingsPath, json_encode($newSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        // Log de l'action
        logAction('Paramètres sauvegardés');
        
        return [
            'success' => true,
            'message' => 'Paramètres sauvegardés avec succès'
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder les paramètres'];
    }
}

function resetSettings() {
    global $settingsPath;
    
    $defaultSettings = getDefaultSettings();
    
    if (file_put_contents($settingsPath, json_encode($defaultSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        logAction('Paramètres réinitialisés');
        
        return [
            'success' => true,
            'data' => ['settings' => $defaultSettings],
            'message' => 'Paramètres réinitialisés'
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de réinitialiser les paramètres'];
    }
}

function clearLogs() {
    $logsDir = __DIR__ . '/../core/logs/';
    $cleared = 0;
    
    if (is_dir($logsDir)) {
        $logFiles = ['actions.log', 'chat.log', 'server.log', 'errors.log'];
        
        foreach ($logFiles as $logFile) {
            $logPath = $logsDir . $logFile;
            if (file_exists($logPath)) {
                if (unlink($logPath)) {
                    $cleared++;
                }
            }
        }
    }
    
    logAction("$cleared fichier(s) de log effacé(s)");
    
    return [
        'success' => true,
        'message' => "$cleared fichier(s) de log effacé(s)"
    ];
}

function factoryReset() {
    // Effacer les logs
    clearLogs();
    
    // Réinitialiser les paramètres
    resetSettings();
    
    // Effacer la base de données des projets
    $projectsPath = __DIR__ . '/../core/db/projects.json';
    if (file_exists($projectsPath)) {
        unlink($projectsPath);
    }
    
    // Effacer la base de données des prompts
    $promptsPath = __DIR__ . '/../core/db/prompts.json';
    if (file_exists($promptsPath)) {
        unlink($promptsPath);
    }
    
    // Effacer la base de données SQLite
    $dbPath = __DIR__ . '/../core/db/app.db';
    if (file_exists($dbPath)) {
        unlink($dbPath);
    }
    
    logAction('Reset complet effectué');
    
    return [
        'success' => true,
        'message' => 'Reset complet effectué avec succès'
    ];
}

function logAction($message) {
    $logPath = __DIR__ . '/../core/logs/actions.log';
    $logDir = dirname($logPath);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] SETTINGS: $message\n";
    
    file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
}
?>