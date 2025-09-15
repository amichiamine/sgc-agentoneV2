<?php
require_once '../core/utils/PathHelper.php';

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['error' => 'Action manquante']);
    exit;
}

$action = $input['action'];

$allowedActions = ['startServer', 'stopServer', 'restartServer', 'serverStatus', 'serverLogs', 'setPort', 'toggleDebug'];
if (!in_array($action, $allowedActions)) {
    echo json_encode(['error' => 'Action non autorisée']);
    exit;
}

// Charger l'action dynamiquement
$className = '\\core\\agents\\actions\\' . ucfirst($action);
if (!class_exists($className)) {
    echo json_encode(['error' => 'Action non trouvée']);
    exit;
}

$instance = new $className();

try {
    $result = $instance->execute();
    
    // Loguer l'action
    file_put_contents(
        PathHelper::getLogsPath() . '/actions.log',
        '[' . date('Y-m-d H:i:s') . "] ACTION: $action | RESULT: " . ($result['success'] ? 'success' : $result['error']) . "\n",
        FILE_APPEND | LOCK_EX
    );

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
