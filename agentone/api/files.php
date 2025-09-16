<?php
/**
 * API Files - Gestion des fichiers et dossiers
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion des requêtes GET pour les téléchargements
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'downloadFile' && isset($_GET['path'])) {
        downloadFile($_GET['path']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Traitement des uploads
if (isset($_FILES['file'])) {
    $result = uploadFile($_FILES['file'], $_POST['path'] ?? '');
    echo json_encode($result);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Action manquante']);
    exit;
}

$action = $input['action'];
$path = $input['path'] ?? '';

try {
    switch ($action) {
        case 'listDir':
            $result = listDirectory($path);
            break;
            
        case 'createFile':
            $result = createFile($path, $input['content'] ?? '');
            break;
            
        case 'readFile':
            $result = readFile($path);
            break;
            
        case 'deleteFile':
            $result = deleteFile($path);
            break;
            
        case 'renameFile':
            $result = renameFile($path, $input['newName'] ?? '');
            break;
            
        case 'createDir':
            $result = createDirectory($path);
            break;
            
        case 'deleteDir':
            $result = deleteDirectory($path);
            break;
            
        default:
            $result = ['success' => false, 'error' => "Action inconnue : $action"];
    }
} catch (Exception $e) {
    $result = ['success' => false, 'error' => 'Erreur : ' . $e->getMessage()];
}

echo json_encode($result);

// Fonctions utilitaires

function sanitizePath($path) {
    // Sécurité : empêcher les path traversal
    $path = str_replace(['../', '..\\', '../', '..\\'], '', $path);
    $path = ltrim($path, '/\\');
    return $path;
}

function getBasePath() {
    return __DIR__ . '/../../';
}

function listDirectory($path) {
    $path = sanitizePath($path);
    $fullPath = getBasePath() . ($path ? $path : '.');
    
    if (!is_dir($fullPath)) {
        return ['success' => false, 'error' => 'Dossier introuvable'];
    }
    
    $items = [];
    $iterator = new DirectoryIterator($fullPath);
    
    foreach ($iterator as $item) {
        if ($item->isDot()) continue;
        
        $items[] = [
            'name' => $item->getFilename(),
            'type' => $item->isDir() ? 'directory' : 'file',
            'size' => $item->isFile() ? $item->getSize() : 0,
            'modified' => date('Y-m-d H:i:s', $item->getMTime()),
            'permissions' => substr(sprintf('%o', $item->getPerms()), -4),
            'readable' => $item->isReadable(),
            'writable' => $item->isWritable()
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
        'data' => [
            'path' => $path,
            'items' => $items,
            'total' => count($items)
        ]
    ];
}

function createFile($path, $content) {
    $path = sanitizePath($path);
    if (empty($path)) {
        return ['success' => false, 'error' => 'Chemin de fichier manquant'];
    }
    
    $fullPath = getBasePath() . $path;
    $dir = dirname($fullPath);
    
    // Créer le dossier parent si nécessaire
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            return ['success' => false, 'error' => 'Impossible de créer le dossier parent'];
        }
    }
    
    if (file_put_contents($fullPath, $content) !== false) {
        return [
            'success' => true,
            'data' => [
                'path' => $path,
                'size' => strlen($content)
            ]
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de créer le fichier'];
    }
}

function readFile($path) {
    $path = sanitizePath($path);
    if (empty($path)) {
        return ['success' => false, 'error' => 'Chemin de fichier manquant'];
    }
    
    $fullPath = getBasePath() . $path;
    
    if (!file_exists($fullPath)) {
        return ['success' => false, 'error' => 'Fichier introuvable'];
    }
    
    if (!is_readable($fullPath)) {
        return ['success' => false, 'error' => 'Fichier non lisible'];
    }
    
    $content = file_get_contents($fullPath);
    
    return [
        'success' => true,
        'data' => [
            'path' => $path,
            'content' => $content,
            'size' => filesize($fullPath),
            'modified' => date('Y-m-d H:i:s', filemtime($fullPath))
        ]
    ];
}

function deleteFile($path) {
    $path = sanitizePath($path);
    if (empty($path)) {
        return ['success' => false, 'error' => 'Chemin de fichier manquant'];
    }
    
    $fullPath = getBasePath() . $path;
    
    if (!file_exists($fullPath)) {
        return ['success' => false, 'error' => 'Fichier introuvable'];
    }
    
    if (is_dir($fullPath)) {
        return ['success' => false, 'error' => 'Utilisez deleteDir pour supprimer un dossier'];
    }
    
    if (unlink($fullPath)) {
        return ['success' => true, 'data' => ['path' => $path]];
    } else {
        return ['success' => false, 'error' => 'Impossible de supprimer le fichier'];
    }
}

function renameFile($oldPath, $newName) {
    $oldPath = sanitizePath($oldPath);
    if (empty($oldPath) || empty($newName)) {
        return ['success' => false, 'error' => 'Chemin ou nouveau nom manquant'];
    }
    
    $oldFullPath = getBasePath() . $oldPath;
    $newFullPath = dirname($oldFullPath) . '/' . $newName;
    
    if (!file_exists($oldFullPath)) {
        return ['success' => false, 'error' => 'Fichier source introuvable'];
    }
    
    if (file_exists($newFullPath)) {
        return ['success' => false, 'error' => 'Un fichier avec ce nom existe déjà'];
    }
    
    if (rename($oldFullPath, $newFullPath)) {
        return [
            'success' => true,
            'data' => [
                'oldPath' => $oldPath,
                'newPath' => dirname($oldPath) . '/' . $newName
            ]
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible de renommer le fichier'];
    }
}

function createDirectory($path) {
    $path = sanitizePath($path);
    if (empty($path)) {
        return ['success' => false, 'error' => 'Chemin de dossier manquant'];
    }
    
    $fullPath = getBasePath() . $path;
    
    if (is_dir($fullPath)) {
        return ['success' => false, 'error' => 'Le dossier existe déjà'];
    }
    
    if (mkdir($fullPath, 0755, true)) {
        return ['success' => true, 'data' => ['path' => $path]];
    } else {
        return ['success' => false, 'error' => 'Impossible de créer le dossier'];
    }
}

function deleteDirectory($path) {
    $path = sanitizePath($path);
    if (empty($path)) {
        return ['success' => false, 'error' => 'Chemin de dossier manquant'];
    }
    
    $fullPath = getBasePath() . $path;
    
    if (!is_dir($fullPath)) {
        return ['success' => false, 'error' => 'Dossier introuvable'];
    }
    
    // Vérifier que le dossier est vide
    $iterator = new DirectoryIterator($fullPath);
    $isEmpty = true;
    foreach ($iterator as $item) {
        if (!$item->isDot()) {
            $isEmpty = false;
            break;
        }
    }
    
    if (!$isEmpty) {
        return ['success' => false, 'error' => 'Le dossier n\'est pas vide'];
    }
    
    if (rmdir($fullPath)) {
        return ['success' => true, 'data' => ['path' => $path]];
    } else {
        return ['success' => false, 'error' => 'Impossible de supprimer le dossier'];
    }
}

function uploadFile($file, $targetPath) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'Fichier invalide'];
    }
    
    $targetPath = sanitizePath($targetPath);
    $fileName = basename($file['name']);
    $fullTargetPath = getBasePath() . ($targetPath ? $targetPath . '/' : '') . $fileName;
    
    // Créer le dossier cible si nécessaire
    $targetDir = dirname($fullTargetPath);
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            return ['success' => false, 'error' => 'Impossible de créer le dossier cible'];
        }
    }
    
    // Vérifier la taille du fichier (limite à 50MB)
    if ($file['size'] > 50 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Fichier trop volumineux (max 50MB)'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $fullTargetPath)) {
        return [
            'success' => true,
            'data' => [
                'fileName' => $fileName,
                'path' => ($targetPath ? $targetPath . '/' : '') . $fileName,
                'size' => $file['size']
            ]
        ];
    } else {
        return ['success' => false, 'error' => 'Impossible d\'uploader le fichier'];
    }
}

function downloadFile($path) {
    $path = sanitizePath($path);
    if (empty($path)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Chemin de fichier manquant']);
        return;
    }
    
    $fullPath = getBasePath() . $path;
    
    if (!file_exists($fullPath) || !is_file($fullPath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Fichier introuvable']);
        return;
    }
    
    $fileName = basename($path);
    $fileSize = filesize($fullPath);
    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';
    
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . $fileSize);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    
    readfile($fullPath);
}
?>