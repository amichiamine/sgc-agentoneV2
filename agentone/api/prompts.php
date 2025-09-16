<?php
/**
 * API Prompts - Gestionnaire de prompts et templates
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
$promptsPath = __DIR__ . '/../core/db/prompts.json';

try {
    switch ($action) {
        case 'getPrompts':
            $result = getPrompts();
            break;
            
        case 'createPrompt':
            $result = createPrompt($input['prompt'] ?? []);
            break;
            
        case 'updatePrompt':
            $result = updatePrompt($input['promptId'] ?? '', $input['prompt'] ?? []);
            break;
            
        case 'deletePrompt':
            $result = deletePrompt($input['promptId'] ?? '');
            break;
            
        case 'incrementUsage':
            $result = incrementUsage($input['promptId'] ?? '');
            break;
            
        case 'exportPrompts':
            $result = exportPrompts();
            break;
            
        case 'importPrompts':
            $result = importPrompts($input['prompts'] ?? []);
            break;
            
        default:
            $result = ['success' => false, 'error' => "Action inconnue : $action"];
    }
} catch (Exception $e) {
    $result = ['success' => false, 'error' => 'Erreur : ' . $e->getMessage()];
}

echo json_encode($result);

// Fonctions utilitaires

function loadPrompts() {
    global $promptsPath;
    
    if (!file_exists($promptsPath)) {
        return [];
    }
    
    $content = file_get_contents($promptsPath);
    return json_decode($content, true) ?: [];
}

function savePrompts($prompts) {
    global $promptsPath;
    
    // Créer le dossier si nécessaire
    $dir = dirname($promptsPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    return file_put_contents($promptsPath, json_encode($prompts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getPrompts() {
    $prompts = loadPrompts();
    
    return [
        'success' => true,
        'data' => [
            'prompts' => $prompts,
            'count' => count($prompts)
        ]
    ];
}

function createPrompt($promptData) {
    if (empty($promptData['name']) || empty($promptData['content'])) {
        return ['success' => false, 'error' => 'Nom et contenu du prompt requis'];
    }
    
    $prompts = loadPrompts();
    
    // Vérifier si un prompt avec ce nom existe déjà
    foreach ($prompts as $prompt) {
        if ($prompt['name'] === $promptData['name']) {
            return ['success' => false, 'error' => 'Un prompt avec ce nom existe déjà'];
        }
    }
    
    $newPrompt = [
        'id' => uniqid(),
        'name' => $promptData['name'],
        'description' => $promptData['description'] ?? '',
        'category' => $promptData['category'] ?? 'general',
        'content' => $promptData['content'],
        'tags' => $promptData['tags'] ?? [],
        'shortcut' => $promptData['shortcut'] ?? '',
        'favorite' => $promptData['favorite'] ?? false,
        'usage_count' => 0,
        'created' => date('Y-m-d H:i:s'),
        'modified' => date('Y-m-d H:i:s')
    ];
    
    $prompts[] = $newPrompt;
    
    if (savePrompts($prompts)) {
        return [
            'success' => true,
            'data' => ['prompt' => $newPrompt]
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder le prompt'];
    }
}

function updatePrompt($promptId, $promptData) {
    if (empty($promptId)) {
        return ['success' => false, 'error' => 'ID du prompt manquant'];
    }
    
    $prompts = loadPrompts();
    $promptIndex = -1;
    
    foreach ($prompts as $index => $prompt) {
        if ($prompt['id'] === $promptId) {
            $promptIndex = $index;
            break;
        }
    }
    
    if ($promptIndex === -1) {
        return ['success' => false, 'error' => 'Prompt non trouvé'];
    }
    
    // Mettre à jour les données
    $prompts[$promptIndex] = array_merge($prompts[$promptIndex], $promptData);
    $prompts[$promptIndex]['modified'] = date('Y-m-d H:i:s');
    
    if (savePrompts($prompts)) {
        return [
            'success' => true,
            'data' => ['prompt' => $prompts[$promptIndex]]
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder les modifications'];
    }
}

function deletePrompt($promptId) {
    if (empty($promptId)) {
        return ['success' => false, 'error' => 'ID du prompt manquant'];
    }
    
    $prompts = loadPrompts();
    $promptIndex = -1;
    
    foreach ($prompts as $index => $prompt) {
        if ($prompt['id'] === $promptId) {
            $promptIndex = $index;
            break;
        }
    }
    
    if ($promptIndex === -1) {
        return ['success' => false, 'error' => 'Prompt non trouvé'];
    }
    
    // Supprimer le prompt
    array_splice($prompts, $promptIndex, 1);
    
    if (savePrompts($prompts)) {
        return ['success' => true, 'message' => 'Prompt supprimé'];
    } else {
        return ['success' => false, 'error' => 'Impossible de supprimer le prompt'];
    }
}

function incrementUsage($promptId) {
    if (empty($promptId)) {
        return ['success' => false, 'error' => 'ID du prompt manquant'];
    }
    
    $prompts = loadPrompts();
    $promptIndex = -1;
    
    foreach ($prompts as $index => $prompt) {
        if ($prompt['id'] === $promptId) {
            $promptIndex = $index;
            break;
        }
    }
    
    if ($promptIndex === -1) {
        return ['success' => false, 'error' => 'Prompt non trouvé'];
    }
    
    // Incrémenter le compteur d'utilisation
    $prompts[$promptIndex]['usage_count'] = ($prompts[$promptIndex]['usage_count'] ?? 0) + 1;
    $prompts[$promptIndex]['last_used'] = date('Y-m-d H:i:s');
    
    if (savePrompts($prompts)) {
        return ['success' => true, 'usage_count' => $prompts[$promptIndex]['usage_count']];
    } else {
        return ['success' => false, 'error' => 'Impossible de mettre à jour les statistiques'];
    }
}

function exportPrompts() {
    $prompts = loadPrompts();
    
    $exportData = [
        'prompts' => $prompts,
        'exported_at' => date('Y-m-d H:i:s'),
        'version' => '1.0',
        'count' => count($prompts)
    ];
    
    $filename = 'prompts_export_' . date('Y-m-d_H-i-s') . '.json';
    
    return [
        'success' => true,
        'data' => [
            'content' => json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'filename' => $filename
        ]
    ];
}

function importPrompts($importData) {
    if (empty($importData)) {
        return ['success' => false, 'error' => 'Données d\'import manquantes'];
    }
    
    // Si c'est un export, extraire les prompts
    if (isset($importData['prompts'])) {
        $importData = $importData['prompts'];
    }
    
    if (!is_array($importData)) {
        return ['success' => false, 'error' => 'Format de données invalide'];
    }
    
    $prompts = loadPrompts();
    $imported = 0;
    $skipped = 0;
    
    foreach ($importData as $promptData) {
        // Vérifier si un prompt avec ce nom existe déjà
        $exists = false;
        foreach ($prompts as $existingPrompt) {
            if ($existingPrompt['name'] === $promptData['name']) {
                $exists = true;
                break;
            }
        }
        
        if ($exists) {
            $skipped++;
            continue;
        }
        
        // Créer le nouveau prompt
        $newPrompt = [
            'id' => uniqid(),
            'name' => $promptData['name'],
            'description' => $promptData['description'] ?? '',
            'category' => $promptData['category'] ?? 'general',
            'content' => $promptData['content'],
            'tags' => $promptData['tags'] ?? [],
            'shortcut' => $promptData['shortcut'] ?? '',
            'favorite' => $promptData['favorite'] ?? false,
            'usage_count' => 0,
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s')
        ];
        
        $prompts[] = $newPrompt;
        $imported++;
    }
    
    if (savePrompts($prompts)) {
        return [
            'success' => true,
            'message' => "$imported prompt(s) importé(s), $skipped ignoré(s)"
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder les prompts importés'];
    }
}
?>