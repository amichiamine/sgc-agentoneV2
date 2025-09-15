<?php
require_once '../core/utils/PathHelper.php';

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['error' => 'Action manquante']);
    exit;
}

$action = $input['action'];
$filePath = $input['path'] ?? '';
$content = $input['content'] ?? '';

// Valider l'action
$allowedActions = ['listDir', 'createFile', 'createDir', 'readFile', 'deleteFile', 'deleteDir', 'renameFile', 'renameDir', 'moveFile', 'moveDir', 'copyFile', 'copyDir', 'uploadFile', 'downloadFile'];
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
    if ($action === 'listDir') {
        $result = $instance->execute($filePath);
    } elseif ($action === 'createFile') {
        $result = $instance->execute($filePath, $content);
    } elseif ($action === 'createDir') {
        $result = $instance->execute($filePath);
    } elseif ($action === 'readFile') {
        $result = $instance->execute($filePath);
    } elseif ($action === 'deleteFile') {
        $result = $instance->execute($filePath);
    } elseif ($action === 'deleteDir') {
        $result = $instance->execute($filePath);
    } elseif ($action === 'renameFile') {
        $result = $instance->execute($filePath, $input['newName'] ?? '');
    } elseif ($action === 'renameDir') {
        $result = $instance->execute($filePath, $input['newName'] ?? '');
    } elseif ($action === 'moveFile') {
        $result = $instance->execute($filePath, $input['destination'] ?? '');
    } elseif ($action === 'moveDir') {
        $result = $instance->execute($filePath, $input['destination'] ?? '');
    } elseif ($action === 'copyFile') {
        $result = $instance->execute($filePath, $input['destination'] ?? '');
    } elseif ($action === 'copyDir') {
        $result = $instance->execute($filePath, $input['destination'] ?? '');
    } elseif ($action === 'uploadFile') {
        // Gestion du fichier uploadé via $_FILES
        if (!isset($_FILES['file'])) {
            echo json_encode(['error' => 'Aucun fichier uploadé']);
            exit;
        }
        $result = $instance->execute($_FILES['file'], $filePath);
    } elseif ($action === 'downloadFile') {
        $result = $instance->execute($filePath);
    }

    // Loguer l'action
    file_put_contents(
        PathHelper::getLogsPath() . '/actions.log',
        '[' . date('Y-m-d H:i:s') . "] ACTION: $action | PATH: $filePath | RESULT: " . ($result['success'] ? 'success' : $result['error']) . "\n",
        FILE_APPEND | LOCK_EX
    );

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
