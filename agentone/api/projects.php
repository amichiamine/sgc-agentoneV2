<?php
/**
 * API Projects - Gestionnaire de projets
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
$projectsPath = __DIR__ . '/../core/db/projects.json';

try {
    switch ($action) {
        case 'getProjects':
            $result = getProjects();
            break;
            
        case 'createProject':
            $result = createProject($input['project'] ?? []);
            break;
            
        case 'updateProject':
            $result = updateProject($input['projectId'] ?? '', $input['project'] ?? []);
            break;
            
        case 'deleteProject':
            $result = deleteProject($input['projectId'] ?? '');
            break;
            
        case 'exportProject':
            $result = exportProject($input['projectId'] ?? '');
            break;
            
        case 'importProject':
            $result = importProject($input['projectData'] ?? []);
            break;
            
        default:
            $result = ['success' => false, 'error' => "Action inconnue : $action"];
    }
} catch (Exception $e) {
    $result = ['success' => false, 'error' => 'Erreur : ' . $e->getMessage()];
}

echo json_encode($result);

// Fonctions utilitaires

function loadProjects() {
    global $projectsPath;
    
    if (!file_exists($projectsPath)) {
        return [];
    }
    
    $content = file_get_contents($projectsPath);
    return json_decode($content, true) ?: [];
}

function saveProjects($projects) {
    global $projectsPath;
    
    // Créer le dossier si nécessaire
    $dir = dirname($projectsPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    return file_put_contents($projectsPath, json_encode($projects, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getProjects() {
    $projects = loadProjects();
    
    return [
        'success' => true,
        'data' => [
            'projects' => $projects,
            'count' => count($projects)
        ]
    ];
}

function createProject($projectData) {
    if (empty($projectData['name']) || empty($projectData['path'])) {
        return ['success' => false, 'error' => 'Nom et chemin du projet requis'];
    }
    
    $projects = loadProjects();
    
    // Vérifier si un projet avec ce nom existe déjà
    foreach ($projects as $project) {
        if ($project['name'] === $projectData['name']) {
            return ['success' => false, 'error' => 'Un projet avec ce nom existe déjà'];
        }
    }
    
    $newProject = [
        'id' => uniqid(),
        'name' => $projectData['name'],
        'description' => $projectData['description'] ?? '',
        'path' => $projectData['path'],
        'languages' => $projectData['languages'] ?? [],
        'status' => $projectData['status'] ?? 'active',
        'notes' => $projectData['notes'] ?? '',
        'created' => date('Y-m-d H:i:s'),
        'modified' => date('Y-m-d H:i:s')
    ];
    
    $projects[] = $newProject;
    
    if (saveProjects($projects)) {
        // Créer le fichier project.json dans le dossier du projet
        $projectJsonPath = $projectData['path'] . '/project.json';
        $projectDir = dirname($projectJsonPath);
        
        if (!is_dir($projectDir)) {
            mkdir($projectDir, 0755, true);
        }
        
        file_put_contents($projectJsonPath, json_encode($newProject, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return [
            'success' => true,
            'data' => ['project' => $newProject]
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder le projet'];
    }
}

function updateProject($projectId, $projectData) {
    if (empty($projectId)) {
        return ['success' => false, 'error' => 'ID du projet manquant'];
    }
    
    $projects = loadProjects();
    $projectIndex = -1;
    
    foreach ($projects as $index => $project) {
        if ($project['id'] === $projectId) {
            $projectIndex = $index;
            break;
        }
    }
    
    if ($projectIndex === -1) {
        return ['success' => false, 'error' => 'Projet non trouvé'];
    }
    
    // Mettre à jour les données
    $projects[$projectIndex] = array_merge($projects[$projectIndex], $projectData);
    $projects[$projectIndex]['modified'] = date('Y-m-d H:i:s');
    
    if (saveProjects($projects)) {
        // Mettre à jour le fichier project.json
        $projectJsonPath = $projects[$projectIndex]['path'] . '/project.json';
        if (file_exists($projectJsonPath)) {
            file_put_contents($projectJsonPath, json_encode($projects[$projectIndex], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        
        return [
            'success' => true,
            'data' => ['project' => $projects[$projectIndex]]
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de sauvegarder les modifications'];
    }
}

function deleteProject($projectId) {
    if (empty($projectId)) {
        return ['success' => false, 'error' => 'ID du projet manquant'];
    }
    
    $projects = loadProjects();
    $projectIndex = -1;
    $projectToDelete = null;
    
    foreach ($projects as $index => $project) {
        if ($project['id'] === $projectId) {
            $projectIndex = $index;
            $projectToDelete = $project;
            break;
        }
    }
    
    if ($projectIndex === -1) {
        return ['success' => false, 'error' => 'Projet non trouvé'];
    }
    
    // Supprimer le projet de la liste
    array_splice($projects, $projectIndex, 1);
    
    if (saveProjects($projects)) {
        // Supprimer le fichier project.json (optionnel)
        $projectJsonPath = $projectToDelete['path'] . '/project.json';
        if (file_exists($projectJsonPath)) {
            unlink($projectJsonPath);
        }
        
        return ['success' => true, 'message' => 'Projet supprimé'];
    } else {
        return ['success' => false, 'error' => 'Impossible de supprimer le projet'];
    }
}

function exportProject($projectId) {
    if (empty($projectId)) {
        return ['success' => false, 'error' => 'ID du projet manquant'];
    }
    
    $projects = loadProjects();
    $project = null;
    
    foreach ($projects as $p) {
        if ($p['id'] === $projectId) {
            $project = $p;
            break;
        }
    }
    
    if (!$project) {
        return ['success' => false, 'error' => 'Projet non trouvé'];
    }
    
    // Créer un export JSON du projet
    $exportData = [
        'project' => $project,
        'exported_at' => date('Y-m-d H:i:s'),
        'version' => '1.0'
    ];
    
    $filename = 'project_' . $project['name'] . '_' . date('Y-m-d_H-i-s') . '.json';
    
    return [
        'success' => true,
        'data' => [
            'content' => json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'filename' => $filename
        ]
    ];
}

function importProject($projectData) {
    if (empty($projectData)) {
        return ['success' => false, 'error' => 'Données du projet manquantes'];
    }
    
    // Si c'est un export, extraire les données du projet
    if (isset($projectData['project'])) {
        $projectData = $projectData['project'];
    }
    
    // Générer un nouvel ID
    $projectData['id'] = uniqid();
    $projectData['created'] = date('Y-m-d H:i:s');
    $projectData['modified'] = date('Y-m-d H:i:s');
    
    return createProject($projectData);
}
?>