<?php
require_once '../core/utils/PathHelper.php';
require_once '../core/agents/interpreter.php';

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['message'])) {
    echo json_encode(['error' => 'Message manquant']);
    exit;
}

$interpreter = new \core\agents\Interpreter();
$result = $interpreter->interpret($input['message']);

// Loguer l'action
file_put_contents(
    PathHelper::getLogsPath() . '/actions.log',
    '[' . date('Y-m-d H:i:s') . "] USER: \"" . $input['message'] . "\" | AI: \"" . ($result['success'] ? 'ok' : $result['error']) . "\"\n",
    FILE_APPEND | LOCK_EX
);

echo json_encode($result);
