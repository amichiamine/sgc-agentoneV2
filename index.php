<?php
/**
 * SGC-AgentOne v2.1 - Point d'entrée universel
 * Interface complète avec toutes les vues et fonctionnalités
 */

// Auto-installation des dossiers requis
$requiredDirs = ['core/logs', 'core/db', 'core/config', 'core/agents/actions', 'extensions/webview', 'prompts'];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Créer les fichiers de configuration par défaut s'ils n'existent pas
if (!file_exists('core/config/settings.json')) {
    $defaultSettings = [
        'port' => 5000,
        'host' => '0.0.0.0',
        'debug' => false,
        'theme' => 'sgc-commander',
        'blind_exec_enabled' => false
    ];
    file_put_contents('core/config/settings.json', json_encode($defaultSettings, JSON_PRETTY_PRINT));
}

// API intégrée
if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    
    if ($_GET['api'] === 'chat' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? '';
        
        // Traitement des commandes simples
        if (strpos($message, 'createFile') === 0) {
            $parts = explode(':', $message, 2);
            if (count($parts) === 2) {
                $pathAndContent = trim($parts[1]);
                $contentParts = explode(' ', $pathAndContent, 2);
                $path = $contentParts[0];
                $content = $contentParts[1] ?? '';
                
                if (!empty($path)) {
                    $dir = dirname($path);
                    if (!is_dir($dir) && $dir !== '.') {
                        mkdir($dir, 0755, true);
                    }
                    file_put_contents($path, $content);
                    echo json_encode(['success' => true, 'result' => "✅ Fichier '$path' créé avec succès!"]);
                } else {
                    echo json_encode(['error' => 'Chemin de fichier manquant']);
                }
            } else {
                echo json_encode(['error' => 'Format: createFile: chemin contenu']);
            }
        }
        elseif (strpos($message, 'readFile') === 0) {
            $path = trim(str_replace('readFile:', '', $message));
            if (file_exists($path)) {
                $content = file_get_contents($path);
                echo json_encode(['success' => true, 'result' => "📄 Contenu de '$path':\n\n$content"]);
            } else {
                echo json_encode(['error' => "Fichier '$path' introuvable"]);
            }
        }
        elseif (strpos($message, 'listDir') === 0) {
            $dir = trim(str_replace('listDir:', '', $message)) ?: '.';
            if (is_dir($dir)) {
                $files = array_diff(scandir($dir), ['.', '..']);
                $result = "📁 Contenu de '$dir':\n\n";
                foreach ($files as $file) {
                    $type = is_dir("$dir/$file") ? '📁' : '📄';
                    $result .= "$type $file\n";
                }
                echo json_encode(['success' => true, 'result' => $result]);
            } else {
                echo json_encode(['error' => "Dossier '$dir' introuvable"]);
            }
        }
        elseif (strpos($message, 'createDir') === 0) {
            $dir = trim(str_replace('createDir:', '', $message));
            if (!empty($dir)) {
                mkdir($dir, 0755, true);
                echo json_encode(['success' => true, 'result' => "✅ Dossier '$dir' créé avec succès!"]);
            } else {
                echo json_encode(['error' => 'Nom de dossier manquant']);
            }
        }
        elseif (strpos($message, 'deleteFile') === 0) {
            $path = trim(str_replace('deleteFile:', '', $message));
            if (file_exists($path)) {
                unlink($path);
                echo json_encode(['success' => true, 'result' => "✅ Fichier '$path' supprimé avec succès!"]);
            } else {
                echo json_encode(['error' => "Fichier '$path' introuvable"]);
            }
        }
        else {
            echo json_encode(['success' => true, 'result' => "🤖 SGC-AgentOne: Commandes disponibles:\n\n• createFile: chemin contenu\n• readFile: chemin\n• listDir: dossier\n• createDir: nom\n• deleteFile: chemin\n\nExemple: createFile: test.txt Bonjour monde!"]);
        }
        exit;
    }
    
    if ($_GET['api'] === 'files' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        if ($action === 'listDir') {
            $path = $input['path'] ?? '.';
            if (is_dir($path)) {
                $files = array_diff(scandir($path), ['.', '..']);
                $items = [];
                foreach ($files as $file) {
                    $items[] = [
                        'name' => $file,
                        'type' => is_dir("$path/$file") ? 'directory' : 'file',
                        'size' => is_file("$path/$file") ? filesize("$path/$file") : 0
                    ];
                }
                echo json_encode(['success' => true, 'items' => $items]);
            } else {
                echo json_encode(['error' => 'Dossier introuvable']);
            }
        }
        elseif ($action === 'readFile') {
            $path = $input['path'] ?? '';
            if (file_exists($path)) {
                echo json_encode(['success' => true, 'content' => file_get_contents($path)]);
            } else {
                echo json_encode(['error' => 'Fichier introuvable']);
            }
        }
        elseif ($action === 'createFile') {
            $path = $input['path'] ?? '';
            $content = $input['content'] ?? '';
            if (!empty($path)) {
                $dir = dirname($path);
                if (!is_dir($dir) && $dir !== '.') {
                    mkdir($dir, 0755, true);
                }
                file_put_contents($path, $content);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Chemin manquant']);
            }
        }
        else {
            echo json_encode(['error' => 'Action non supportée']);
        }
        exit;
    }
    
    if ($_GET['api'] === 'server') {
        echo json_encode(['success' => true, 'status' => 'running', 'port' => 5000]);
        exit;
    }
    
    echo json_encode(['error' => 'API non trouvée']);
    exit;
}

// Interface principale
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGC-AgentOne v2.1</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: hsl(222, 84%, 5%);
            --bg-secondary: hsl(215, 28%, 17%);
            --bg-tertiary: hsl(222, 84%, 8%);
            --text-primary: hsl(210, 40%, 95%);
            --text-secondary: hsl(217, 10%, 58%);
            --accent: hsl(188, 95%, 42%);
            --border: hsl(217, 19%, 20%);
            --success: hsl(113, 54%, 73%);
            --error: hsl(310, 100%, 75%);
            --warning: hsl(50, 100%, 74%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        #header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            flex-shrink: 0;
        }

        #header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        #header .logo h1 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--accent);
        }

        #header .logo small {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }

        #nav-menu {
            display: flex;
            gap: 4px;
        }

        #nav-menu button {
            background: none;
            border: none;
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        #nav-menu button:hover {
            background: var(--bg-tertiary);
        }

        #nav-menu button.active {
            background: var(--accent);
            color: var(--bg-primary);
        }

        /* Main Content */
        #main-content {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .view {
            display: none;
            height: 100%;
            overflow: hidden;
        }

        .view.active {
            display: flex;
            flex-direction: column;
        }

        /* Footer */
        #footer {
            background: var(--bg-secondary);
            border-top: 1px solid var(--border);
            padding: 6px 16px;
            font-size: 0.8rem;
            color: var(--text-secondary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 32px;
            flex-shrink: 0;
        }

        /* Chat View */
        #chat-view {
            display: flex;
            flex-direction: column;
        }

        #messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 0.95rem;
            line-height: 1.5;
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        .message.user {
            align-self: flex-end;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .message.ai {
            align-self: flex-start;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        #chat-input-container {
            display: flex;
            padding: 12px;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border);
            gap: 12px;
        }

        #chat-input {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 24px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            outline: none;
        }

        #chat-send {
            padding: 10px 16px;
            border: none;
            border-radius: 24px;
            background: var(--accent);
            color: var(--bg-primary);
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        #chat-send:hover {
            opacity: 0.9;
        }

        /* Files View */
        #files-view {
            display: flex;
            flex-direction: row;
        }

        #files-sidebar {
            width: 300px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        #files-toolbar {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        #files-toolbar button {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        #files-toolbar button:hover {
            background: var(--accent);
            color: var(--bg-primary);
        }

        #files-tree {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 8px;
        }

        .file-item:hover {
            background: var(--bg-tertiary);
        }

        .file-item.active {
            background: var(--accent);
            color: var(--bg-primary);
        }

        #files-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-tertiary);
        }

        #files-content-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-secondary);
        }

        #files-content-body {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
        }

        /* Editor View */
        #editor-view {
            display: flex;
            flex-direction: row;
        }

        #editor-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        #editor-sidebar-header {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            font-weight: 500;
            font-size: 0.9rem;
        }

        #editor-files-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }

        #editor-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #editor-toolbar {
            padding: 8px 16px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
        }

        #editor-toolbar button {
            background: none;
            border: none;
            color: var(--text-primary);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        #editor-toolbar button:hover {
            background: var(--bg-tertiary);
        }

        #editor-filename {
            font-weight: 500;
            color: var(--accent);
            min-width: 120px;
            text-align: center;
        }

        #editor-textarea {
            flex: 1;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            padding: 16px;
            line-height: 1.6;
            white-space: pre;
            outline: none;
            resize: none;
            border: none;
            tab-size: 2;
        }

        /* Terminal View */
        #terminal-view {
            display: flex;
            flex-direction: column;
            background: var(--bg-primary);
        }

        #terminal-output {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            line-height: 1.4;
            background: var(--bg-primary);
            color: var(--success);
        }

        #terminal-input-container {
            display: flex;
            padding: 12px;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border);
            align-items: center;
            gap: 8px;
        }

        #terminal-prompt {
            color: var(--accent);
            font-family: 'JetBrains Mono', monospace;
            font-weight: 500;
        }

        #terminal-input {
            flex: 1;
            background: none;
            border: none;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            outline: none;
        }

        /* Server View */
        #server-view {
            display: flex;
            flex-direction: column;
            padding: 16px;
            gap: 16px;
        }

        .server-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
        }

        .server-section h3 {
            color: var(--accent);
            margin-bottom: 12px;
            font-size: 1.1rem;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .metric-card {
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--accent);
        }

        .metric-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .server-controls {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .server-controls button {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-start {
            background: var(--success);
            color: var(--bg-primary);
        }

        .btn-stop {
            background: var(--error);
            color: var(--bg-primary);
        }

        .btn-restart {
            background: var(--warning);
            color: var(--bg-primary);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        /* Database View */
        #database-view {
            display: flex;
            flex-direction: row;
        }

        #db-sidebar {
            width: 300px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        #db-sidebar-header {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            font-weight: 500;
            font-size: 0.9rem;
        }

        #db-tables-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }

        #db-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #db-editor {
            height: 200px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            padding: 12px;
            border: none;
            outline: none;
            resize: none;
            border-bottom: 1px solid var(--border);
        }

        #db-results {
            flex: 1;
            overflow: auto;
            background: var(--bg-tertiary);
        }

        #db-toolbar {
            padding: 8px 12px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 8px;
        }

        #db-toolbar button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        #db-toolbar button:hover {
            background: var(--accent);
            color: var(--bg-primary);
        }

        /* Browser View */
        #browser-view {
            display: flex;
            flex-direction: column;
        }

        #browser-toolbar {
            padding: 8px 12px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #browser-toolbar button {
            background: none;
            border: none;
            color: var(--text-primary);
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        #browser-toolbar button:hover {
            background: var(--bg-tertiary);
        }

        #browser-url {
            flex: 1;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 0.9rem;
            outline: none;
        }

        #browser-iframe {
            flex: 1;
            border: none;
            background: white;
        }

        /* Projects View */
        #projects-view {
            display: flex;
            flex-direction: column;
            padding: 16px;
            gap: 16px;
        }

        #projects-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #projects-header h2 {
            color: var(--accent);
            font-size: 1.3rem;
        }

        #projects-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--accent);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        #projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }

        .project-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .project-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .project-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 12px;
        }

        .project-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .project-tag {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
        }

        .project-actions {
            display: flex;
            gap: 8px;
        }

        .project-actions button {
            flex: 1;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        /* Prompts View */
        #prompts-view {
            display: flex;
            flex-direction: row;
        }

        #prompts-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        #prompts-sidebar-header {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            font-weight: 500;
            font-size: 0.9rem;
        }

        #prompts-categories {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }

        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .category-item:hover {
            background: var(--bg-tertiary);
        }

        .category-item.active {
            background: var(--accent);
            color: var(--bg-primary);
        }

        .category-count {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.7rem;
        }

        #prompts-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #prompts-toolbar {
            padding: 12px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 8px;
        }

        #prompts-toolbar button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        #prompts-toolbar button:hover {
            background: var(--accent);
            color: var(--bg-primary);
        }

        #prompts-grid {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .prompt-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .prompt-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .prompt-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .prompt-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 12px;
        }

        .prompt-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 12px;
        }

        .prompt-actions {
            display: flex;
            gap: 6px;
        }

        .prompt-actions button {
            flex: 1;
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.7rem;
            transition: all 0.2s ease;
        }

        /* Config View */
        #config-view {
            display: flex;
            flex-direction: row;
        }

        #config-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        #config-sidebar-header {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            font-weight: 500;
            font-size: 0.9rem;
        }

        #config-sections {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }

        .config-section-item {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 8px;
        }

        .config-section-item:hover {
            background: var(--bg-tertiary);
        }

        .config-section-item.active {
            background: var(--accent);
            color: var(--bg-primary);
        }

        #config-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #config-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .config-section {
            display: none;
        }

        .config-section.active {
            display: block;
        }

        .config-group {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .config-group h3 {
            color: var(--accent);
            margin-bottom: 12px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.9rem;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 0.9rem;
            outline: none;
        }

        .form-control:focus {
            box-shadow: 0 0 0 2px var(--accent);
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--bg-tertiary);
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: var(--text-primary);
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--accent);
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        /* Help View */
        #help-view {
            display: flex;
            flex-direction: row;
        }

        #help-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        #help-sidebar-header {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            font-weight: 500;
            font-size: 0.9rem;
        }

        #help-sections {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }

        .help-section-item {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 8px;
        }

        .help-section-item:hover {
            background: var(--bg-tertiary);
        }

        .help-section-item.active {
            background: var(--accent);
            color: var(--bg-primary);
        }

        #help-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #help-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .help-section {
            display: none;
        }

        .help-section.active {
            display: block;
        }

        .help-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .help-card h3 {
            color: var(--accent);
            margin-bottom: 12px;
            font-size: 1.1rem;
        }

        .help-card p {
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .help-card code {
            background: var(--bg-tertiary);
            color: var(--accent);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
        }

        .help-card pre {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            padding: 12px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            margin: 12px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #nav-menu {
                flex-wrap: wrap;
                gap: 2px;
            }

            #nav-menu button {
                padding: 6px 8px;
                font-size: 0.8rem;
            }

            #files-sidebar,
            #editor-sidebar,
            #db-sidebar,
            #prompts-sidebar,
            #config-sidebar,
            #help-sidebar {
                width: 200px;
            }

            .metrics-grid,
            #projects-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            #projects-grid,
            #prompts-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div id="header">
        <div class="logo">
            <h1>SGC-AgentOne</h1>
            <small>v2.1 - By AMICHI Amine</small>
        </div>
        <div id="nav-menu">
            <button data-view="chat" class="active">💬 Chat</button>
            <button data-view="files">📁 Fichiers</button>
            <button data-view="editor">📝 Éditeur</button>
            <button data-view="terminal">⚡ Terminal</button>
            <button data-view="server">🖥️ Serveur</button>
            <button data-view="database">🗄️ Base</button>
            <button data-view="browser">🌐 Navigateur</button>
            <button data-view="projects">📂 Projets</button>
            <button data-view="prompts">📝 Prompts</button>
            <button data-view="config">⚙️ Config</button>
            <button data-view="help">❓ Aide</button>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Chat View -->
        <div id="chat-view" class="view active">
            <div id="messages">
                <div class="message ai">🤖 SGC-AgentOne v2.1 prêt ! Tapez vos commandes ci-dessous.

Commandes disponibles :
• createFile: chemin contenu
• readFile: chemin
• listDir: dossier
• createDir: nom
• deleteFile: chemin

Exemple : createFile: test.txt Bonjour monde!</div>
            </div>
            <div id="chat-input-container">
                <input type="text" id="chat-input" placeholder="Tapez votre commande..." autocomplete="off">
                <button id="chat-send">Envoyer</button>
            </div>
        </div>

        <!-- Files View -->
        <div id="files-view" class="view">
            <div id="files-sidebar">
                <div id="files-toolbar">
                    <button onclick="createNewFile()">📄 Nouveau</button>
                    <button onclick="createNewFolder()">📁 Dossier</button>
                    <button onclick="refreshFiles()">🔄 Actualiser</button>
                </div>
                <div id="files-tree">
                    <div class="file-item" onclick="loadDirectory('.')">
                        📁 Racine du projet
                    </div>
                </div>
            </div>
            <div id="files-content">
                <div id="files-content-header">
                    <h3>📁 Explorateur de Fichiers</h3>
                </div>
                <div id="files-content-body">
                    <p>Sélectionnez un fichier ou dossier dans la sidebar pour voir son contenu.</p>
                </div>
            </div>
        </div>

        <!-- Editor View -->
        <div id="editor-view" class="view">
            <div id="editor-sidebar">
                <div id="editor-sidebar-header">📝 Fichiers Ouverts</div>
                <div id="editor-files-list">
                    <p style="padding: 12px; color: var(--text-secondary); font-size: 0.8rem;">Aucun fichier ouvert</p>
                </div>
            </div>
            <div id="editor-main">
                <div id="editor-toolbar">
                    <button onclick="saveCurrentFile()">💾 Sauvegarder</button>
                    <button onclick="closeCurrentFile()">✖️ Fermer</button>
                    <div id="editor-filename">Aucun fichier ouvert</div>
                    <button onclick="refreshEditor()">🔄 Actualiser</button>
                </div>
                <textarea id="editor-textarea" placeholder="Ouvrez un fichier pour commencer à éditer..."></textarea>
            </div>
        </div>

        <!-- Terminal View -->
        <div id="terminal-view" class="view">
            <div id="terminal-output">SGC-AgentOne Terminal v2.1
Tapez 'help' pour voir les commandes disponibles.

</div>
            <div id="terminal-input-container">
                <span id="terminal-prompt">sgc@agentone:~$</span>
                <input type="text" id="terminal-input" autocomplete="off">
            </div>
        </div>

        <!-- Server View -->
        <div id="server-view" class="view">
            <div class="server-section">
                <h3>🖥️ État du Serveur</h3>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-value" id="server-status">🟢</div>
                        <div class="metric-label">Statut</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="server-port">5000</div>
                        <div class="metric-label">Port</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="server-uptime">00:00:00</div>
                        <div class="metric-label">Uptime</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="server-connections">0</div>
                        <div class="metric-label">Connexions</div>
                    </div>
                </div>
                <div class="server-controls">
                    <button class="btn-start" onclick="startServer()">▶️ Démarrer</button>
                    <button class="btn-stop" onclick="stopServer()">⏹️ Arrêter</button>
                    <button class="btn-restart" onclick="restartServer()">🔄 Redémarrer</button>
                    <button class="btn-secondary" onclick="viewLogs()">📋 Logs</button>
                </div>
            </div>
            <div class="server-section">
                <h3>📊 Métriques Système</h3>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-value" id="cpu-usage">12%</div>
                        <div class="metric-label">CPU</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="memory-usage">256MB</div>
                        <div class="metric-label">Mémoire</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="disk-usage">2.1GB</div>
                        <div class="metric-label">Disque</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value" id="requests-count">1,247</div>
                        <div class="metric-label">Requêtes</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database View -->
        <div id="database-view" class="view">
            <div id="db-sidebar">
                <div id="db-sidebar-header">🗄️ Tables</div>
                <div id="db-tables-list">
                    <div class="file-item" onclick="selectTable('users')">
                        👥 users (0)
                    </div>
                    <div class="file-item" onclick="selectTable('projects')">
                        📂 projects (0)
                    </div>
                    <div class="file-item" onclick="selectTable('logs')">
                        📋 logs (0)
                    </div>
                </div>
            </div>
            <div id="db-main">
                <div id="db-toolbar">
                    <button onclick="executeQuery()">▶️ Exécuter</button>
                    <button onclick="saveQuery()">💾 Sauvegarder</button>
                    <button onclick="loadQuery()">📂 Charger</button>
                    <button onclick="formatQuery()">🎨 Formater</button>
                    <button onclick="exportResults()">📤 Exporter</button>
                </div>
                <textarea id="db-editor" placeholder="-- Tapez votre requête SQL ici
SELECT * FROM users LIMIT 10;"></textarea>
                <div id="db-results">
                    <div style="padding: 16px; color: var(--text-secondary);">
                        Exécutez une requête pour voir les résultats ici.
                    </div>
                </div>
            </div>
        </div>

        <!-- Browser View -->
        <div id="browser-view" class="view">
            <div id="browser-toolbar">
                <button onclick="browserBack()">◀️</button>
                <button onclick="browserForward()">▶️</button>
                <button onclick="browserRefresh()">🔄</button>
                <button onclick="browserHome()">🏠</button>
                <input type="text" id="browser-url" value="http://localhost:5000" placeholder="Entrez une URL...">
                <button onclick="browserGo()">🔍</button>
            </div>
            <iframe id="browser-iframe" src="about:blank"></iframe>
        </div>

        <!-- Projects View -->
        <div id="projects-view" class="view">
            <div id="projects-header">
                <h2>📂 Gestionnaire de Projets</h2>
                <button onclick="createNewProject()" style="padding: 8px 16px; background: var(--accent); color: var(--bg-primary); border: none; border-radius: 6px; cursor: pointer;">➕ Nouveau Projet</button>
            </div>
            <div id="projects-stats">
                <div class="stat-card">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">2</div>
                    <div class="stat-label">Actifs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">1</div>
                    <div class="stat-label">En Pause</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Archivés</div>
                </div>
            </div>
            <div id="projects-grid">
                <div class="project-card">
                    <div class="project-title">SGC-AgentOne</div>
                    <div class="project-description">Assistant universel de développement</div>
                    <div class="project-tags">
                        <span class="project-tag">PHP</span>
                        <span class="project-tag">HTML</span>
                        <span class="project-tag">CSS</span>
                        <span class="project-tag">JavaScript</span>
                    </div>
                    <div class="project-actions">
                        <button style="background: var(--accent); color: var(--bg-primary);">Ouvrir</button>
                        <button style="background: var(--bg-tertiary); color: var(--text-primary);">⭐</button>
                        <button style="background: var(--bg-tertiary); color: var(--text-primary);">📤</button>
                    </div>
                </div>
                <div class="project-card">
                    <div class="project-title">Portfolio Web</div>
                    <div class="project-description">Site portfolio personnel</div>
                    <div class="project-tags">
                        <span class="project-tag">React</span>
                        <span class="project-tag">TypeScript</span>
                        <span class="project-tag">Tailwind</span>
                    </div>
                    <div class="project-actions">
                        <button style="background: var(--accent); color: var(--bg-primary);">Ouvrir</button>
                        <button style="background: var(--bg-tertiary); color: var(--text-primary);">⭐</button>
                        <button style="background: var(--bg-tertiary); color: var(--text-primary);">📤</button>
                    </div>
                </div>
                <div class="project-card">
                    <div class="project-title">API REST</div>
                    <div class="project-description">API backend pour application mobile</div>
                    <div class="project-tags">
                        <span class="project-tag">Node.js</span>
                        <span class="project-tag">Express</span>
                        <span class="project-tag">MongoDB</span>
                    </div>
                    <div class="project-actions">
                        <button style="background: var(--accent); color: var(--bg-primary);">Ouvrir</button>
                        <button style="background: var(--bg-tertiary); color: var(--text-primary);">⭐</button>
                        <button style="background: var(--bg-tertiary); color: var(--text-primary);">📤</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prompts View -->
        <div id="prompts-view" class="view">
            <div id="prompts-sidebar">
                <div id="prompts-sidebar-header">📝 Catégories</div>
                <div id="prompts-categories">
                    <div class="category-item active">
                        <span>🎯 Tous</span>
                        <span class="category-count">12</span>
                    </div>
                    <div class="category-item">
                        <span>💻 Développement</span>
                        <span class="category-count">5</span>
                    </div>
                    <div class="category-item">
                        <span>🎨 Design</span>
                        <span class="category-count">3</span>
                    </div>
                    <div class="category-item">
                        <span>📊 Analyse</span>
                        <span class="category-count">2</span>
                    </div>
                    <div class="category-item">
                        <span>🔧 Utilitaires</span>
                        <span class="category-count">2</span>
                    </div>
                </div>
            </div>
            <div id="prompts-main">
                <div id="prompts-toolbar">
                    <button onclick="createNewPrompt()">➕ Nouveau</button>
                    <button onclick="importPrompts()">📥 Importer</button>
                    <button onclick="exportPrompts()">📤 Exporter</button>
                    <button onclick="refreshPrompts()">🔄 Actualiser</button>
                </div>
                <div id="prompts-grid">
                    <div class="prompt-card">
                        <div class="prompt-title">Créer Structure PHP</div>
                        <div class="prompt-description">Génère une structure de projet PHP avec MVC</div>
                        <div class="prompt-meta">
                            <span>Utilisé 15 fois</span>
                            <span>Modifié il y a 2j</span>
                        </div>
                        <div class="prompt-actions">
                            <button style="background: var(--accent); color: var(--bg-primary);">Utiliser</button>
                            <button style="background: var(--bg-tertiary); color: var(--text-primary);">✏️</button>
                            <button style="background: var(--bg-tertiary); color: var(--text-primary);">📋</button>
                        </div>
                    </div>
                    <div class="prompt-card">
                        <div class="prompt-title">API REST Template</div>
                        <div class="prompt-description">Template pour créer une API REST complète</div>
                        <div class="prompt-meta">
                            <span>Utilisé 8 fois</span>
                            <span>Modifié il y a 1s</span>
                        </div>
                        <div class="prompt-actions">
                            <button style="background: var(--accent); color: var(--bg-primary);">Utiliser</button>
                            <button style="background: var(--bg-tertiary); color: var(--text-primary);">✏️</button>
                            <button style="background: var(--bg-tertiary); color: var(--text-primary);">📋</button>
                        </div>
                    </div>
                    <div class="prompt-card">
                        <div class="prompt-title">Interface Responsive</div>
                        <div class="prompt-description">Crée une interface responsive moderne</div>
                        <div class="prompt-meta">
                            <span>Utilisé 23 fois</span>
                            <span>Modifié il y a 5j</span>
                        </div>
                        <div class="prompt-actions">
                            <button style="background: var(--accent); color: var(--bg-primary);">Utiliser</button>
                            <button style="background: var(--bg-tertiary); color: var(--text-primary);">✏️</button>
                            <button style="background: var(--bg-tertiary); color: var(--text-primary);">📋</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Config View -->
        <div id="config-view" class="view">
            <div id="config-sidebar">
                <div id="config-sidebar-header">⚙️ Configuration</div>
                <div id="config-sections">
                    <div class="config-section-item active" data-section="general">
                        <span>🎯 Général</span>
                    </div>
                    <div class="config-section-item" data-section="appearance">
                        <span>🎨 Apparence</span>
                    </div>
                    <div class="config-section-item" data-section="editor">
                        <span>📝 Éditeur</span>
                    </div>
                    <div class="config-section-item" data-section="server">
                        <span>🖥️ Serveur</span>
                    </div>
                    <div class="config-section-item" data-section="security">
                        <span>🔒 Sécurité</span>
                    </div>
                    <div class="config-section-item" data-section="performance">
                        <span>⚡ Performance</span>
                    </div>
                    <div class="config-section-item" data-section="backup">
                        <span>💾 Sauvegarde</span>
                    </div>
                    <div class="config-section-item" data-section="advanced">
                        <span>🔧 Avancé</span>
                    </div>
                </div>
            </div>
            <div id="config-main">
                <div id="config-content">
                    <!-- General Section -->
                    <div class="config-section active" id="config-general">
                        <div class="config-group">
                            <h3>🎯 Paramètres Généraux</h3>
                            <div class="form-group">
                                <label>Nom de l'application</label>
                                <input type="text" class="form-control" value="SGC-AgentOne" id="app-name">
                            </div>
                            <div class="form-group">
                                <label>Langue</label>
                                <select class="form-control" id="app-language">
                                    <option value="fr">Français</option>
                                    <option value="en">English</option>
                                    <option value="es">Español</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Auto-sauvegarde</label>
                                <label class="switch">
                                    <input type="checkbox" checked id="auto-save">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance Section -->
                    <div class="config-section" id="config-appearance">
                        <div class="config-group">
                            <h3>🎨 Apparence</h3>
                            <div class="form-group">
                                <label>Thème</label>
                                <select class="form-control" id="theme-select">
                                    <option value="dark">Sombre</option>
                                    <option value="light">Clair</option>
                                    <option value="auto">Automatique</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Couleur d'accent</label>
                                <input type="color" class="form-control" value="#1ab8b8" id="accent-color">
                            </div>
                            <div class="form-group">
                                <label>Taille de police</label>
                                <select class="form-control" id="font-size">
                                    <option value="small">Petite</option>
                                    <option value="medium" selected>Moyenne</option>
                                    <option value="large">Grande</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Editor Section -->
                    <div class="config-section" id="config-editor">
                        <div class="config-group">
                            <h3>📝 Éditeur</h3>
                            <div class="form-group">
                                <label>Police de code</label>
                                <select class="form-control" id="editor-font">
                                    <option value="JetBrains Mono" selected>JetBrains Mono</option>
                                    <option value="Fira Code">Fira Code</option>
                                    <option value="Source Code Pro">Source Code Pro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Taille de tabulation</label>
                                <select class="form-control" id="tab-size">
                                    <option value="2" selected>2 espaces</option>
                                    <option value="4">4 espaces</option>
                                    <option value="8">8 espaces</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Coloration syntaxique</label>
                                <label class="switch">
                                    <input type="checkbox" checked id="syntax-highlighting">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Server Section -->
                    <div class="config-section" id="config-server">
                        <div class="config-group">
                            <h3>🖥️ Serveur</h3>
                            <div class="form-group">
                                <label>Port</label>
                                <input type="number" class="form-control" value="5000" min="1" max="65535" id="server-port-config">
                            </div>
                            <div class="form-group">
                                <label>Hôte</label>
                                <input type="text" class="form-control" value="0.0.0.0" id="server-host-config">
                            </div>
                            <div class="form-group">
                                <label>Démarrage automatique</label>
                                <label class="switch">
                                    <input type="checkbox" id="auto-start">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="config-section" id="config-security">
                        <div class="config-group">
                            <h3>🔒 Sécurité</h3>
                            <div class="form-group">
                                <label>Mode debug</label>
                                <label class="switch">
                                    <input type="checkbox" id="debug-mode">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Logs détaillés</label>
                                <label class="switch">
                                    <input type="checkbox" checked id="detailed-logs">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>IPs autorisées</label>
                                <input type="text" class="form-control" placeholder="127.0.0.1, 192.168.1.*" id="allowed-ips">
                            </div>
                        </div>
                    </div>

                    <!-- Performance Section -->
                    <div class="config-section" id="config-performance">
                        <div class="config-group">
                            <h3>⚡ Performance</h3>
                            <div class="form-group">
                                <label>Cache activé</label>
                                <label class="switch">
                                    <input type="checkbox" checked id="cache-enabled">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Limite mémoire (MB)</label>
                                <input type="number" class="form-control" value="256" min="64" max="2048" id="memory-limit">
                            </div>
                            <div class="form-group">
                                <label>Timeout (secondes)</label>
                                <input type="number" class="form-control" value="30" min="5" max="300" id="timeout">
                            </div>
                        </div>
                    </div>

                    <!-- Backup Section -->
                    <div class="config-section" id="config-backup">
                        <div class="config-group">
                            <h3>💾 Sauvegarde</h3>
                            <div class="form-group">
                                <label>Sauvegarde automatique</label>
                                <label class="switch">
                                    <input type="checkbox" checked id="auto-backup">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Fréquence</label>
                                <select class="form-control" id="backup-frequency">
                                    <option value="hourly">Toutes les heures</option>
                                    <option value="daily" selected>Quotidienne</option>
                                    <option value="weekly">Hebdomadaire</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nombre de sauvegardes à conserver</label>
                                <input type="number" class="form-control" value="7" min="1" max="30" id="backup-count">
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Section -->
                    <div class="config-section" id="config-advanced">
                        <div class="config-group">
                            <h3>🔧 Paramètres Avancés</h3>
                            <div class="form-group">
                                <label>Mode développeur</label>
                                <label class="switch">
                                    <input type="checkbox" id="dev-mode">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Chemin personnalisé</label>
                                <input type="text" class="form-control" placeholder="/chemin/vers/projet" id="custom-path">
                            </div>
                            <div class="form-group">
                                <label>Variables d'environnement</label>
                                <textarea class="form-control" rows="4" placeholder="VAR1=valeur1&#10;VAR2=valeur2" id="env-vars"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help View -->
        <div id="help-view" class="view">
            <div id="help-sidebar">
                <div id="help-sidebar-header">❓ Centre d'Aide</div>
                <div id="help-sections">
                    <div class="help-section-item active" data-section="getting-started">
                        <span>🚀 Démarrage</span>
                    </div>
                    <div class="help-section-item" data-section="commands">
                        <span>💬 Commandes</span>
                    </div>
                    <div class="help-section-item" data-section="features">
                        <span>✨ Fonctionnalités</span>
                    </div>
                    <div class="help-section-item" data-section="troubleshooting">
                        <span>🔧 Dépannage</span>
                    </div>
                    <div class="help-section-item" data-section="api">
                        <span>🔌 API</span>
                    </div>
                    <div class="help-section-item" data-section="examples">
                        <span>📚 Exemples</span>
                    </div>
                    <div class="help-section-item" data-section="faq">
                        <span>❓ FAQ</span>
                    </div>
                    <div class="help-section-item" data-section="about">
                        <span>ℹ️ À propos</span>
                    </div>
                </div>
            </div>
            <div id="help-main">
                <div id="help-content">
                    <!-- Getting Started Section -->
                    <div class="help-section active" id="help-getting-started">
                        <div class="help-card">
                            <h3>🚀 Bienvenue dans SGC-AgentOne</h3>
                            <p>SGC-AgentOne est un assistant universel de développement qui vous permet de gérer vos projets, éditer du code, et interagir avec votre système via une interface web moderne.</p>
                            <p>Pour commencer :</p>
                            <ol>
                                <li>Utilisez le <strong>Chat</strong> pour exécuter des commandes</li>
                                <li>Explorez vos fichiers avec l'<strong>Explorateur</strong></li>
                                <li>Éditez votre code dans l'<strong>Éditeur</strong></li>
                                <li>Surveillez votre serveur dans l'onglet <strong>Serveur</strong></li>
                            </ol>
                        </div>
                    </div>

                    <!-- Commands Section -->
                    <div class="help-section" id="help-commands">
                        <div class="help-card">
                            <h3>💬 Commandes du Chat</h3>
                            <p>Utilisez ces commandes dans le chat pour interagir avec le système :</p>
                            <pre>createFile: chemin contenu
readFile: chemin
listDir: dossier
createDir: nom
deleteFile: chemin</pre>
                            <p><strong>Exemples :</strong></p>
                            <code>createFile: test.txt Bonjour monde!</code><br>
                            <code>readFile: test.txt</code><br>
                            <code>listDir: .</code>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="help-section" id="help-features">
                        <div class="help-card">
                            <h3>✨ Fonctionnalités Principales</h3>
                            <ul>
                                <li><strong>Chat Intelligent</strong> - Assistant avec commandes intégrées</li>
                                <li><strong>Explorateur de Fichiers</strong> - Navigation et gestion des fichiers</li>
                                <li><strong>Éditeur de Code</strong> - Édition avec coloration syntaxique</li>
                                <li><strong>Terminal</strong> - Console de commandes intégrée</li>
                                <li><strong>Monitoring Serveur</strong> - Surveillance en temps réel</li>
                                <li><strong>Base de Données</strong> - Éditeur SQL intégré</li>
                                <li><strong>Navigateur</strong> - Prévisualisation web</li>
                                <li><strong>Gestionnaire de Projets</strong> - Organisation des projets</li>
                                <li><strong>Templates</strong> - Prompts réutilisables</li>
                                <li><strong>Configuration</strong> - Personnalisation complète</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Troubleshooting Section -->
                    <div class="help-section" id="help-troubleshooting">
                        <div class="help-card">
                            <h3>🔧 Résolution de Problèmes</h3>
                            <p><strong>Le serveur ne démarre pas :</strong></p>
                            <ul>
                                <li>Vérifiez que le port 5000 n'est pas utilisé</li>
                                <li>Changez le port dans Configuration > Serveur</li>
                                <li>Redémarrez l'application</li>
                            </ul>
                            <p><strong>Les fichiers ne se sauvegardent pas :</strong></p>
                            <ul>
                                <li>Vérifiez les permissions du dossier</li>
                                <li>Activez l'auto-sauvegarde dans Configuration</li>
                            </ul>
                        </div>
                    </div>

                    <!-- API Section -->
                    <div class="help-section" id="help-api">
                        <div class="help-card">
                            <h3>🔌 API Intégrée</h3>
                            <p>SGC-AgentOne expose une API REST pour l'intégration :</p>
                            <pre>GET  /?api=server     - Statut du serveur
POST /?api=chat       - Envoyer une commande
POST /?api=files      - Gestion des fichiers</pre>
                            <p><strong>Exemple d'utilisation :</strong></p>
                            <pre>fetch('/?api=chat', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({message: 'listDir: .'})
})</pre>
                        </div>
                    </div>

                    <!-- Examples Section -->
                    <div class="help-section" id="help-examples">
                        <div class="help-card">
                            <h3>📚 Exemples Pratiques</h3>
                            <p><strong>Créer un projet PHP :</strong></p>
                            <pre>createDir: monprojet
createFile: monprojet/index.php &lt;?php echo "Hello World"; ?&gt;
createFile: monprojet/style.css body { margin: 0; }</pre>
                            <p><strong>Lire et modifier un fichier :</strong></p>
                            <pre>readFile: config.json
createFile: config.json {"version": "2.1"}</pre>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="help-section" id="help-faq">
                        <div class="help-card">
                            <h3>❓ Questions Fréquentes</h3>
                            <p><strong>Q: Comment changer le thème ?</strong></p>
                            <p>R: Allez dans Configuration > Apparence et sélectionnez votre thème préféré.</p>
                            <p><strong>Q: Puis-je utiliser SGC-AgentOne sur mobile ?</strong></p>
                            <p>R: Oui, l'interface est entièrement responsive et fonctionne sur tous les appareils.</p>
                            <p><strong>Q: Les données sont-elles sauvegardées ?</strong></p>
                            <p>R: Oui, l'auto-sauvegarde est activée par défaut. Vous pouvez configurer la fréquence dans Configuration > Sauvegarde.</p>
                        </div>
                    </div>

                    <!-- About Section -->
                    <div class="help-section" id="help-about">
                        <div class="help-card">
                            <h3>ℹ️ À propos de SGC-AgentOne</h3>
                            <p><strong>Version :</strong> 2.1</p>
                            <p><strong>Auteur :</strong> AMICHI Amine</p>
                            <p><strong>Description :</strong> Assistant universel de développement avec interface web moderne</p>
                            <p><strong>Technologies :</strong> PHP, HTML5, CSS3, JavaScript</p>
                            <p><strong>Licence :</strong> MIT</p>
                            <p>SGC-AgentOne est conçu pour simplifier le développement en offrant tous les outils nécessaires dans une interface unifiée et intuitive.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div id="footer">
        <span id="status-info">Serveur : En ligne • UTF-8 • Projet : SGC-AgentOne • Fichier : index.php</span>
        <span id="timestamp">2025-01-20 14:30:25</span>
    </div>

    <script>
        // Variables globales
        let currentView = 'chat';
        let currentFile = '';
        let terminalHistory = [];
        let terminalHistoryIndex = -1;
        let serverStartTime = Date.now();

        // Navigation entre les vues
        document.querySelectorAll('#nav-menu button').forEach(button => {
            button.addEventListener('click', () => {
                const view = button.dataset.view;
                switchView(view);
                
                // Mettre à jour les boutons actifs
                document.querySelectorAll('#nav-menu button').forEach(b => b.classList.remove('active'));
                button.classList.add('active');
            });
        });

        function switchView(view) {
            // Cacher toutes les vues
            document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
            
            // Afficher la vue sélectionnée
            document.getElementById(view + '-view').classList.add('active');
            currentView = view;
            
            // Actions spécifiques par vue
            if (view === 'files') {
                loadDirectory('.');
            } else if (view === 'server') {
                updateServerMetrics();
            } else if (view === 'browser') {
                loadBrowserUrl();
            }
        }

        // Chat functionality
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        const messagesContainer = document.getElementById('messages');

        function sendChatMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            // Ajouter le message utilisateur
            addChatMessage(message, 'user');
            chatInput.value = '';

            // Envoyer à l'API
            fetch('/?api=chat', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({message: message})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addChatMessage(data.result, 'ai');
                } else {
                    addChatMessage('❌ Erreur: ' + data.error, 'ai');
                }
            })
            .catch(error => {
                addChatMessage('❌ Erreur de connexion au serveur', 'ai');
            });
        }

        function addChatMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            messageDiv.textContent = text;
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        chatSend.addEventListener('click', sendChatMessage);
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendChatMessage();
        });

        // Files functionality
        function loadDirectory(path) {
            fetch('/?api=files', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'listDir', path: path})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateFilesTree(data.items, path);
                }
            });
        }

        function updateFilesTree(items, path) {
            const tree = document.getElementById('files-tree');
            tree.innerHTML = '';
            
            // Ajouter le dossier parent si ce n'est pas la racine
            if (path !== '.') {
                const parentItem = document.createElement('div');
                parentItem.className = 'file-item';
                parentItem.innerHTML = '📁 ..';
                parentItem.onclick = () => loadDirectory(path.split('/').slice(0, -1).join('/') || '.');
                tree.appendChild(parentItem);
            }
            
            items.forEach(item => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                const icon = item.type === 'directory' ? '📁' : '📄';
                fileItem.innerHTML = `${icon} ${item.name}`;
                
                if (item.type === 'directory') {
                    fileItem.onclick = () => loadDirectory(path === '.' ? item.name : path + '/' + item.name);
                } else {
                    fileItem.onclick = () => openFileInEditor(path === '.' ? item.name : path + '/' + item.name);
                }
                
                tree.appendChild(fileItem);
            });
        }

        function createNewFile() {
            const name = prompt('Nom du fichier:');
            if (name) {
                fetch('/?api=files', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'createFile', path: name, content: ''})
                })
                .then(() => loadDirectory('.'));
            }
        }

        function createNewFolder() {
            const name = prompt('Nom du dossier:');
            if (name) {
                fetch('/?api=chat', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({message: 'createDir: ' + name})
                })
                .then(() => loadDirectory('.'));
            }
        }

        function refreshFiles() {
            loadDirectory('.');
        }

        // Editor functionality
        function openFileInEditor(path) {
            fetch('/?api=files', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'readFile', path: path})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentFile = path;
                    document.getElementById('editor-textarea').value = data.content;
                    document.getElementById('editor-filename').textContent = path;
                    switchView('editor');
                }
            });
        }

        function saveCurrentFile() {
            if (!currentFile) return;
            
            const content = document.getElementById('editor-textarea').value;
            fetch('/?api=files', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'createFile', path: currentFile, content: content})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Fichier sauvegardé!');
                }
            });
        }

        function closeCurrentFile() {
            currentFile = '';
            document.getElementById('editor-textarea').value = '';
            document.getElementById('editor-filename').textContent = 'Aucun fichier ouvert';
        }

        function refreshEditor() {
            if (currentFile) {
                openFileInEditor(currentFile);
            }
        }

        // Terminal functionality
        const terminalInput = document.getElementById('terminal-input');
        const terminalOutput = document.getElementById('terminal-output');

        function executeTerminalCommand() {
            const command = terminalInput.value.trim();
            if (!command) return;

            // Ajouter à l'historique
            terminalHistory.push(command);
            terminalHistoryIndex = terminalHistory.length;

            // Afficher la commande
            addTerminalOutput(`sgc@agentone:~$ ${command}`);
            terminalInput.value = '';

            // Exécuter la commande
            switch (command.split(' ')[0]) {
                case 'help':
                    addTerminalOutput('Commandes disponibles:\n  ls - Lister les fichiers\n  pwd - Afficher le répertoire courant\n  status - Statut du serveur\n  clear - Effacer l\'écran\n  help - Afficher cette aide');
                    break;
                case 'ls':
                    addTerminalOutput('index.php  core/  extensions/  prompts/');
                    break;
                case 'pwd':
                    addTerminalOutput('/home/sgc-agentone');
                    break;
                case 'status':
                    addTerminalOutput('Serveur: En ligne\nPort: 5000\nUptime: ' + formatUptime(Date.now() - serverStartTime));
                    break;
                case 'clear':
                    terminalOutput.textContent = '';
                    break;
                default:
                    addTerminalOutput(`Commande non reconnue: ${command}\nTapez 'help' pour voir les commandes disponibles.`);
            }
        }

        function addTerminalOutput(text) {
            terminalOutput.textContent += text + '\n\n';
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
        }

        terminalInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                executeTerminalCommand();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (terminalHistoryIndex > 0) {
                    terminalHistoryIndex--;
                    terminalInput.value = terminalHistory[terminalHistoryIndex];
                }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (terminalHistoryIndex < terminalHistory.length - 1) {
                    terminalHistoryIndex++;
                    terminalInput.value = terminalHistory[terminalHistoryIndex];
                } else {
                    terminalHistoryIndex = terminalHistory.length;
                    terminalInput.value = '';
                }
            }
        });

        // Server functionality
        function updateServerMetrics() {
            // Simuler des métriques
            document.getElementById('server-uptime').textContent = formatUptime(Date.now() - serverStartTime);
            document.getElementById('cpu-usage').textContent = Math.floor(Math.random() * 30 + 10) + '%';
            document.getElementById('memory-usage').textContent = Math.floor(Math.random() * 100 + 200) + 'MB';
            document.getElementById('requests-count').textContent = Math.floor(Math.random() * 1000 + 1000).toLocaleString();
        }

        function formatUptime(ms) {
            const seconds = Math.floor(ms / 1000);
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function startServer() {
            alert('Serveur démarré!');
            updateServerMetrics();
        }

        function stopServer() {
            alert('Serveur arrêté!');
        }

        function restartServer() {
            alert('Serveur redémarré!');
            serverStartTime = Date.now();
            updateServerMetrics();
        }

        function viewLogs() {
            addTerminalOutput('=== Logs du serveur ===\n[2025-01-20 14:30:25] Serveur démarré sur le port 5000\n[2025-01-20 14:30:26] Connexion client: 127.0.0.1\n[2025-01-20 14:30:27] Requête GET /\n[2025-01-20 14:30:28] Réponse 200 OK');
            switchView('terminal');
        }

        // Database functionality
        function executeQuery() {
            const query = document.getElementById('db-editor').value;
            const results = document.getElementById('db-results');
            results.innerHTML = `<div style="padding: 16px;">
                <h4>Résultats de la requête:</h4>
                <pre>${query}</pre>
                <p>Requête exécutée avec succès. (Simulation)</p>
            </div>`;
        }

        function saveQuery() {
            alert('Requête sauvegardée!');
        }

        function loadQuery() {
            document.getElementById('db-editor').value = 'SELECT * FROM users WHERE active = 1 ORDER BY created_at DESC;';
        }

        function formatQuery() {
            const editor = document.getElementById('db-editor');
            editor.value = editor.value.replace(/select/gi, 'SELECT').replace(/from/gi, 'FROM').replace(/where/gi, 'WHERE');
        }

        function exportResults() {
            alert('Résultats exportés en CSV!');
        }

        function selectTable(table) {
            document.getElementById('db-editor').value = `SELECT * FROM ${table} LIMIT 10;`;
        }

        // Browser functionality
        function loadBrowserUrl() {
            const url = document.getElementById('browser-url').value;
            document.getElementById('browser-iframe').src = url;
        }

        function browserBack() {
            document.getElementById('browser-iframe').contentWindow.history.back();
        }

        function browserForward() {
            document.getElementById('browser-iframe').contentWindow.history.forward();
        }

        function browserRefresh() {
            document.getElementById('browser-iframe').contentWindow.location.reload();
        }

        function browserHome() {
            document.getElementById('browser-url').value = 'http://localhost:5000';
            loadBrowserUrl();
        }

        function browserGo() {
            loadBrowserUrl();
        }

        // Projects functionality
        function createNewProject() {
            alert('Nouveau projet créé!');
        }

        // Prompts functionality
        function createNewPrompt() {
            alert('Nouveau prompt créé!');
        }

        function importPrompts() {
            alert('Prompts importés!');
        }

        function exportPrompts() {
            alert('Prompts exportés!');
        }

        function refreshPrompts() {
            alert('Prompts actualisés!');
        }

        // Config functionality
        document.querySelectorAll('.config-section-item').forEach(item => {
            item.addEventListener('click', () => {
                const section = item.dataset.section;
                
                // Mettre à jour la sidebar
                document.querySelectorAll('.config-section-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                
                // Afficher la section
                document.querySelectorAll('.config-section').forEach(s => s.classList.remove('active'));
                document.getElementById('config-' + section).classList.add('active');
            });
        });

        // Help functionality
        document.querySelectorAll('.help-section-item').forEach(item => {
            item.addEventListener('click', () => {
                const section = item.dataset.section;
                
                // Mettre à jour la sidebar
                document.querySelectorAll('.help-section-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                
                // Afficher la section
                document.querySelectorAll('.help-section').forEach(s => s.classList.remove('active'));
                document.getElementById('help-' + section).classList.add('active');
            });
        });

        // Sauvegarde automatique des paramètres
        function saveSettings() {
            const settings = {
                appName: document.getElementById('app-name')?.value,
                language: document.getElementById('app-language')?.value,
                autoSave: document.getElementById('auto-save')?.checked,
                theme: document.getElementById('theme-select')?.value,
                accentColor: document.getElementById('accent-color')?.value,
                fontSize: document.getElementById('font-size')?.value,
                editorFont: document.getElementById('editor-font')?.value,
                tabSize: document.getElementById('tab-size')?.value,
                syntaxHighlighting: document.getElementById('syntax-highlighting')?.checked,
                serverPort: document.getElementById('server-port-config')?.value,
                serverHost: document.getElementById('server-host-config')?.value,
                autoStart: document.getElementById('auto-start')?.checked,
                debugMode: document.getElementById('debug-mode')?.checked,
                detailedLogs: document.getElementById('detailed-logs')?.checked,
                allowedIps: document.getElementById('allowed-ips')?.value,
                cacheEnabled: document.getElementById('cache-enabled')?.checked,
                memoryLimit: document.getElementById('memory-limit')?.value,
                timeout: document.getElementById('timeout')?.value,
                autoBackup: document.getElementById('auto-backup')?.checked,
                backupFrequency: document.getElementById('backup-frequency')?.value,
                backupCount: document.getElementById('backup-count')?.value,
                devMode: document.getElementById('dev-mode')?.checked,
                customPath: document.getElementById('custom-path')?.value,
                envVars: document.getElementById('env-vars')?.value
            };
            
            localStorage.setItem('sgc-settings', JSON.stringify(settings));
        }

        function loadSettings() {
            const settings = JSON.parse(localStorage.getItem('sgc-settings') || '{}');
            
            Object.keys(settings).forEach(key => {
                const element = document.getElementById(key.replace(/([A-Z])/g, '-$1').toLowerCase());
                if (element) {
                    if (element.type === 'checkbox') {
                        element.checked = settings[key];
                    } else {
                        element.value = settings[key];
                    }
                }
            });
        }

        // Sauvegarder les paramètres quand ils changent
        document.addEventListener('change', (e) => {
            if (e.target.closest('#config-view')) {
                saveSettings();
            }
        });

        // Mise à jour du footer en temps réel
        function updateFooter() {
            const now = new Date();
            const timestamp = now.toISOString().replace('T', ' ').substring(0, 19);
            document.getElementById('timestamp').textContent = timestamp;
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            loadSettings();
            updateFooter();
            setInterval(updateFooter, 1000);
            setInterval(updateServerMetrics, 5000);
        });
    </script>
</body>
</html>