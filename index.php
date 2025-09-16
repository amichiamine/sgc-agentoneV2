<?php
/**
 * SGC-AgentOne v2.1 - Interface Complète
 * Point d'entrée universel avec toutes les vues
 */

// === CONFIGURATION ===
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';
$projectRoot = __DIR__;

// === FONCTIONS D'AUTO-INSTALLATION ===
function createProjectStructure($root) {
    $dirs = [
        'core/config', 'core/logs', 'core/agents/actions', 'core/db',
        'api', 'extensions/webview', 'prompts', 'assets', 'backups'
    ];
    
    foreach ($dirs as $dir) {
        $path = $root . '/' . $dir;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
    
    // Configuration par défaut
    $configPath = $root . '/core/config/settings.json';
    if (!file_exists($configPath)) {
        $defaultConfig = [
            'title' => 'SGC-AgentOne',
            'author' => 'By AMICHI Amine',
            'port' => 5000,
            'host' => '0.0.0.0',
            'debug' => false,
            'theme' => 'dark',
            'blind_exec_enabled' => false,
            'auto_save' => true,
            'syntax_highlighting' => true,
            'file_watcher' => true,
            'backup_enabled' => true
        ];
        file_put_contents($configPath, json_encode($defaultConfig, JSON_PRETTY_PRINT));
    }
}

// Gestion des routes API intégrées
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$route = parse_url($requestUri, PHP_URL_PATH);

// API Chat intégrée
if (strpos($route, '/api/chat') !== false) {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Méthode non autorisée']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['message'])) {
        echo json_encode(['error' => 'Message manquant']);
        exit;
    }
    
    $message = trim($input['message']);
    
    // Traitement des commandes
    if (strpos($message, 'createFile') === 0) {
        if (preg_match('/createFile\s+(.+?)\s*:\s*(.*)/', $message, $matches)) {
            $filename = trim($matches[1]);
            $content = trim($matches[2]);
            $filepath = __DIR__ . '/' . $filename;
            $dir = dirname($filepath);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents($filepath, $content);
            echo json_encode(['success' => true, 'result' => "✅ Fichier '$filename' créé avec succès!"]);
        } else {
            echo json_encode(['error' => 'Format: createFile filename.ext : contenu']);
        }
    }
    elseif (strpos($message, 'readFile') === 0) {
        if (preg_match('/readFile\s+(.+)/', $message, $matches)) {
            $filename = trim($matches[1]);
            $filepath = __DIR__ . '/' . $filename;
            if (file_exists($filepath)) {
                $content = file_get_contents($filepath);
                echo json_encode(['success' => true, 'result' => "📄 Contenu de '$filename':\n\n$content"]);
            } else {
                echo json_encode(['error' => "❌ Fichier '$filename' introuvable"]);
            }
        }
    }
    elseif (strpos($message, 'listDir') === 0) {
        $dir = __DIR__;
        if (preg_match('/listDir\s+(.+)/', $message, $matches)) {
            $dir = __DIR__ . '/' . trim($matches[1]);
        }
        if (is_dir($dir)) {
            $items = scandir($dir);
            $result = "📁 Contenu du dossier:\n\n";
            foreach ($items as $item) {
                if ($item != '.' && $item != '..') {
                    $icon = is_dir($dir . '/' . $item) ? '📁' : '📄';
                    $result .= "$icon $item\n";
                }
            }
            echo json_encode(['success' => true, 'result' => $result]);
        } else {
            echo json_encode(['error' => '❌ Dossier introuvable']);
        }
    }
    else {
        echo json_encode(['success' => true, 'result' => "🤖 SGC-AgentOne: Commandes disponibles:\n\n• createFile nom.ext : contenu\n• readFile nom.ext\n• listDir [dossier]\n• createDir nom_dossier\n• deleteFile nom.ext"]);
    }
    exit;
}

// API Auth
if (strpos($route, '/api/auth') !== false) {
    session_start();
    header('Content-Type: application/json');
    $token = bin2hex(random_bytes(32));
    $_SESSION['auth_token'] = $token;
    echo json_encode(['token' => $token]);
    exit;
}

// Interface principale ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SGC-AgentOne - Interface Complète</title>
    <title>SGC-AgentOne v2.1 - Assistant Universel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0f1c;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --accent: #38bdf8;
            --success: #22c55e;
            --warning: #f59e0b;
            --error: #ef4444;
            --border: #475569;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary); 
            color: var(--text-primary); 
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Header */
        #header { 
            background: var(--bg-secondary); 
            padding: 12px 20px; 
            border-bottom: 1px solid var(--border);
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        #header h1 { 
            font-size: 1.3rem; 
            color: var(--accent); 
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        #header .subtitle {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 400;
        }
        
        /* Navigation */
        #nav { 
            display: flex; 
            gap: 6px; 
            flex-wrap: wrap;
        }
        
        #nav button { 
            background: var(--bg-tertiary); 
            border: none; 
            color: var(--text-primary); 
            padding: 8px 14px;
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 0.85rem; 
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        
        #nav button:hover { 
            background: var(--border); 
            transform: translateY(-1px);
        }
        
        #nav button.active { 
            background: var(--accent); 
            color: var(--bg-primary);
            box-shadow: 0 2px 8px rgba(56, 189, 248, 0.3);
        }
        
        /* Main Content */
        #main { 
            height: calc(100vh - 120px); 
            display: flex; 
            flex-direction: column; 
        }
        
        .view { 
            display: none; 
            flex: 1; 
            padding: 20px; 
            overflow-y: auto;
        }
        
        .view.active { 
            display: flex; 
            flex-direction: column; 
        }
        
        /* Chat Interface */
        #chat-container { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        #messages { 
            flex: 1; 
            overflow-y: auto; 
            padding: 20px; 
            background: var(--bg-secondary); 
            border-radius: 12px; 
            margin-bottom: 20px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .message { 
            margin-bottom: 16px; 
            padding: 16px; 
            border-radius: 12px; 
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .user { 
            background: var(--bg-tertiary); 
            margin-left: 15%; 
            border-left: 4px solid var(--accent);
        }
        
        .ai { 
            background: var(--bg-primary); 
            margin-right: 15%; 
            border-left: 4px solid var(--success);
        }
        
        .message strong {
            color: var(--accent);
            font-weight: 600;
        }
        
        .message pre {
            background: var(--bg-primary);
            padding: 12px;
            border-radius: 6px;
            margin: 8px 0;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
        }
        
        /* Input Area */
        #input-area { 
            display: flex; 
            gap: 12px; 
            align-items: flex-end;
        }
        
        #message-input { 
            flex: 1; 
            padding: 16px; 
            background: var(--bg-secondary); 
            border: 2px solid var(--border);
            border-radius: 12px; 
            color: var(--text-primary); 
            font-size: 1rem; 
            outline: none;
            resize: vertical;
            min-height: 50px;
            max-height: 150px;
            font-family: inherit;
        }
        
        #message-input:focus { 
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
        }
        
        #send-btn { 
            padding: 16px 24px; 
            background: var(--accent); 
            color: var(--bg-primary); 
            border: none;
            border-radius: 12px; 
            cursor: pointer; 
            font-weight: 600; 
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        #send-btn:hover { 
            background: #0ea5e9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
        }
        
        #send-btn:disabled {
            background: var(--bg-tertiary);
            cursor: not-allowed;
            transform: none;
        }
        
        /* Cards and Panels */
        .card {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            color: var(--accent);
            margin-bottom: 16px;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        /* Forms */
        .form-group { 
            margin-bottom: 20px; 
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea { 
            width: 100%; 
            padding: 12px; 
            background: var(--bg-primary); 
            border: 2px solid var(--border);
            border-radius: 8px; 
            color: var(--text-primary); 
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
        }
        
        /* Buttons */
        .btn { 
            padding: 12px 20px; 
            background: var(--accent); 
            color: var(--bg-primary); 
            border: none;
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            margin-right: 10px;
            margin-bottom: 10px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
        }
        
        .btn-secondary { 
            background: var(--bg-tertiary); 
            color: var(--text-primary); 
        }
        
        .btn-success { 
            background: var(--success); 
            color: white; 
        }
        
        .btn-warning { 
            background: var(--warning); 
            color: white; 
        }
        
        .btn-error { 
            background: var(--error); 
            color: white; 
        }
        
        /* File List */
        .file-list { 
            margin-top: 20px; 
        }
        
        .file-item { 
            padding: 16px; 
            background: var(--bg-secondary); 
            margin-bottom: 8px; 
            border-radius: 8px;
            cursor: pointer; 
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background: hsl(222, 84%, 5%); 
            color: hsl(210, 40%, 95%); 
            overflow: hidden; 
            height: 100vh; 
        }
        
        /* Header */
        #header { 
            background: hsl(215, 28%, 17%); 
            padding: 12px 20px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            border-bottom: 1px solid hsl(217, 19%, 20%); 
        }
        
        #header .logo { 
            font-size: 1.2rem; 
            font-weight: 700; 
            color: hsl(188, 95%, 42%); 
        }
        
        #header .subtitle { 
            font-size: 0.8rem; 
            color: hsl(217, 10%, 58%); 
            margin-top: 2px; 
        }
        
        /* Navigation */
        #nav-menu { 
            display: flex; 
            gap: 8px; 
            flex-wrap: wrap;
        }
        
        #nav-menu button { 
            background: hsl(215, 16%, 25%); 
            border: 1px solid hsl(217, 19%, 20%); 
            color: hsl(210, 40%, 95%); 
            padding: 8px 16px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 0.9rem; 
            transition: all 0.3s ease; 
        }
        
        #nav-menu button:hover { 
            background: hsl(215, 16%, 30%); 
        }
        
        #nav-menu button.active { 
            background: hsl(188, 95%, 42%); 
            color: hsl(222, 84%, 5%); 
            font-weight: 600;
        }
        
        /* Corps principal */
        #main-content { 
            height: calc(100vh - 120px); 
            overflow: hidden; 
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
            background: hsl(215, 28%, 17%); 
            padding: 8px 20px; 
            font-size: 0.8rem; 
            color: hsl(217, 10%, 58%); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-top: 1px solid hsl(217, 19%, 20%);
        }
        
        /* Vue Chat */
        #chat-container { 
            display: flex; 
            flex-direction: column; 
            height: 100%; 
            background: hsl(215, 28%, 17%); 
            margin: 8px;
            border-radius: 12px; 
            overflow: hidden; 
        }
        
        #messages { 
            flex: 1; 
            padding: 20px; 
            overflow-y: auto; 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
        }
        
        .message { 
            max-width: 80%; 
            padding: 12px 16px; 
            border-radius: 12px; 
            font-size: 0.95rem; 
            line-height: 1.5; 
            word-wrap: break-word; 
        }
        
        .message.user { 
            align-self: flex-end; 
            background: hsl(188, 95%, 42%); 
            color: hsl(222, 84%, 5%); 
        }
        
        .message.ai { 
            align-self: flex-start; 
            background: hsl(215, 16%, 25%); 
            color: hsl(210, 40%, 95%); 
            border: 1px solid hsl(217, 19%, 20%); 
        }
        
        #input-container { 
            display: flex; 
            padding: 20px; 
            background: hsl(222, 84%, 8%); 
            border-top: 1px solid hsl(217, 19%, 20%); 
        }
        
        #input { 
            flex: 1; 
            padding: 12px 16px; 
            border: none; 
            border-radius: 24px; 
            background: hsl(222, 84%, 4%); 
            color: hsl(210, 40%, 95%); 
            font-family: inherit; 
            outline: none; 
            font-size: 0.95rem;
        }
        
        #send { 
            margin-left: 12px; 
            padding: 12px 20px; 
            border: none; 
            border-radius: 24px; 
            background: hsl(188, 95%, 42%); 
            color: hsl(222, 84%, 5%); 
            cursor: pointer; 
            font-weight: 600; 
            transition: all 0.3s ease; 
        }
        
        #send:hover { 
            background: hsl(188, 95%, 48%); 
        }
        
        /* Autres vues - Layout de base */
        .view-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: hsl(215, 28%, 17%);
            margin: 8px;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .view-header {
            padding: 16px 20px;
            background: hsl(222, 84%, 8%);
            border-bottom: 1px solid hsl(217, 19%, 20%);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .view-content {
            flex: 1;
            overflow: auto;
            padding: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            #header { flex-direction: column; gap: 12px; }
            #nav-menu { width: 100%; justify-content: space-around; }
            #nav-menu button { flex: 1; min-width: 80px; margin: 2px; }
            .message { max-width: 95%; }
        }
    </style>
</head>
<body>
    <div id="header">
        <div>
            <h1>🚀 SGC-AgentOne <span class="subtitle">v2.1</span></h1>
            <div class="subtitle">Assistant Universel de Développement</div>
        </div>
        <div id="nav">
            <button class="nav-btn active" data-view="chat">💬 Chat</button>
            <button class="nav-btn" data-view="files">📁 Fichiers</button>
            <button data-view="editor">📝 Éditeur</button>
            <button data-view="terminal">⚡ Terminal</button>
            <button data-view="server">🖥️ Serveur</button>
            <button data-view="database">🗄️ Base</button>
            <button data-view="browser">🌐 Navigateur</button>
            <button data-view="projects">📂 Projets</button>
            <button data-view="prompts">📝 Prompts</button>
            <button data-view="config">⚙️ Config</button>
            <button data-view="help">❓ Aide</button>
            <button class="nav-btn" data-view="terminal">⚡ Terminal</button>
            <button class="nav-btn" data-view="server">🖥️ Serveur</button>
            <button class="nav-btn" data-view="database">🗄️ Base</button>
            <button class="nav-btn" data-view="logs">📊 Logs</button>
            <button class="nav-btn" data-view="backup">💾 Backup</button>
            <button class="nav-btn" data-view="settings">⚙️ Config</button>
            <button class="nav-btn" data-view="help">❓ Aide</button>
        </div>
    </div>
    
    <div id="main">
        <!-- Chat View -->
        <div id="chat" class="view active">
            <div id="chat-container">
                <div id="messages">
                    <div class="message ai">
                        <strong>🤖 SGC-AgentOne:</strong> Bonjour ! Je suis votre assistant de développement universel. 
                        <br><br>📋 <strong>Commandes disponibles :</strong>
                        <pre>• createFile nom.ext : contenu
• readFile nom.ext
• listDir dossier
• createDir nom-dossier
• deleteFile nom.ext
• serverStatus
• backup : créer une sauvegarde</pre>
                        
                        <br>💡 <strong>Exemples :</strong>
                        <pre>createFile index.php : &lt;?php echo "Hello World!"; ?&gt;
listDir .
readFile index.php
backup : sauvegarde complète</pre>
                    </div>
                </div>
                <div id="input-area">
                    <textarea id="message-input" placeholder="Tapez votre commande ou question..." rows="2"></textarea>
                    <button id="send-btn">
                        <span>📤</span>
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Files View -->
        <div id="files" class="view">
            <div class="card">
                <h3>📁 Gestionnaire de Fichiers</h3>
                <div class="flex gap-3 mb-3">
                    <button class="btn" onclick="loadFileList()">🔄 Actualiser</button>
                    <button class="btn btn-secondary" onclick="createNewFile()">➕ Nouveau fichier</button>
                    <button class="btn btn-secondary" onclick="createNewFolder()">📁 Nouveau dossier</button>
                    <button class="btn btn-warning" onclick="uploadFile()">⬆️ Upload</button>
                </div>
                <div class="form-group">
                    <label>📂 Chemin actuel</label>
                    <input type="text" id="current-path" value="." placeholder="Chemin du dossier">
                    <button class="btn mt-1" onclick="navigateToPath()">📂 Naviguer</button>
                </div>
            </div>
            
            <div id="file-list" class="file-list">
                <div class="text-center" style="padding: 40px; color: var(--text-secondary);">
                    <div style="font-size: 3rem; margin-bottom: 16px;">📂</div>
                    <p>Cliquez sur "Actualiser" pour voir les fichiers du projet</p>
                </div>
            </div>
        </div>
        
        <!-- Editor View -->
        <div id="editor" class="view">
            <div class="card">
                <h3>📝 Éditeur de Code</h3>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="editor-file">📄 Fichier à éditer</label>
                        <input type="text" id="editor-file" placeholder="Nom du fichier (ex: index.php)">
                    </div>
                    <div class="form-group">
                        <label>🔧 Actions</label>
                        <div class="flex gap-2">
                            <button class="btn" onclick="loadFileInEditor()">📂 Charger</button>
                            <button class="btn btn-success" onclick="saveFileFromEditor()">💾 Sauvegarder</button>
                            <button class="btn btn-secondary" onclick="clearEditor()">🗑️ Vider</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="flex justify-between items-center mb-3">
                    <h3>📄 Contenu du fichier</h3>
                    <div class="flex gap-2">
                        <button class="btn btn-secondary" onclick="formatCode()">🎨 Formater</button>
                        <button class="btn btn-secondary" onclick="toggleWrap()">📏 Retour ligne</button>
                    </div>
                </div>
                <textarea id="code-editor" class="code-editor" placeholder="Contenu du fichier apparaîtra ici..."></textarea>
            </div>
        </div>
        
        <!-- Terminal View -->
        <div id="terminal" class="view">
            <div class="card">
                <h3>⚡ Terminal de Commandes</h3>
                <div class="flex gap-2 mb-3">
                    <button class="btn" onclick="runQuickCommand('listDir .')">📂 Lister</button>
                    <button class="btn" onclick="runQuickCommand('readFile index.php')">📄 Lire index</button>
                    <button class="btn" onclick="runQuickCommand('serverStatus')">🖥️ Statut serveur</button>
                    <button class="btn btn-warning" onclick="runQuickCommand('backup : sauvegarde auto')">💾 Backup</button>
                    <button class="btn btn-error" onclick="clearTerminal()">🗑️ Effacer</button>
                </div>
            </div>
            
            <div class="card">
                <h3>📟 Sortie Terminal</h3>
                <div id="terminal-output" class="terminal"></div>
                <div class="flex gap-3 mt-3">
                    <input type="text" id="terminal-input" placeholder="Tapez votre commande..." class="flex-1">
                    <button class="btn" onclick="runTerminalCommand()">▶️ Exécuter</button>
                </div>
            </div>
        </div>
        
        <!-- Server View -->
        <div id="server" class="view">
            <div class="grid grid-2">
                <div class="card">
                    <h3>🖥️ Statut du Serveur</h3>
                    <div id="server-status" class="status">
                        <div class="loading"></div> Vérification du statut...
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button class="btn btn-success" onclick="startServer()">▶️ Démarrer</button>
                        <button class="btn btn-error" onclick="stopServer()">⏹️ Arrêter</button>
                        <button class="btn" onclick="restartServer()">🔄 Redémarrer</button>
                        <button class="btn btn-secondary" onclick="checkServerStatus()">📊 Vérifier</button>
                    </div>
                </div>
                
                <div class="card">
                    <h3>⚙️ Configuration Serveur</h3>
                    <div class="form-group">
                        <label>🌐 Port</label>
                        <input type="number" id="server-port" value="5000" min="1000" max="65535">
                    </div>
                    <div class="form-group">
                        <label>🏠 Hôte</label>
                        <input type="text" id="server-host" value="0.0.0.0">
                    </div>
                    <button class="btn" onclick="updateServerConfig()">💾 Appliquer</button>
                </div>
            </div>
            
            <div class="card">
                <h3>📈 Monitoring</h3>
                <div class="grid grid-3">
                    <div class="text-center">
                        <div style="font-size: 2rem; color: var(--success);">●</div>
                        <div>Serveur PHP</div>
                        <div class="text-secondary">Actif</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size: 2rem; color: var(--accent);">📊</div>
                        <div>Requêtes</div>
                        <div class="text-secondary">0/min</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size: 2rem; color: var(--warning);">⚡</div>
                        <div>Performance</div>
                        <div class="text-secondary">Optimale</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Database View -->
        <div id="database" class="view">
            <div class="card">
                <h3>🗄️ Gestionnaire de Base de Données</h3>
                <div class="flex gap-2 mb-3">
                    <button class="btn" onclick="createDatabase()">➕ Créer DB</button>
                    <button class="btn btn-secondary" onclick="listTables()">📋 Tables</button>
                    <button class="btn btn-warning" onclick="backupDatabase()">💾 Backup DB</button>
                    <button class="btn btn-error" onclick="optimizeDatabase()">⚡ Optimiser</button>
                </div>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <h3>📊 Requêtes SQL</h3>
                    <textarea id="sql-query" class="code-editor" placeholder="SELECT * FROM users;" style="min-height: 200px;"></textarea>
                    <div class="flex gap-2 mt-3">
                        <button class="btn" onclick="executeSQLQuery()">▶️ Exécuter</button>
                        <button class="btn btn-secondary" onclick="formatSQL()">🎨 Formater</button>
                        <button class="btn btn-secondary" onclick="clearSQL()">🗑️ Vider</button>
                    </div>
                </div>
                
                <div class="card">
                    <h3>📋 Résultats</h3>
                    <div id="sql-results" class="terminal" style="min-height: 200px;">
                        Aucune requête exécutée
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h3>🏗️ Générateur de Tables</h3>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label>📝 Nom de la table</label>
                        <input type="text" id="table-name" placeholder="users">
                    </div>
                    <div class="form-group">
                        <label>🔧 Colonnes (JSON)</label>
                        <textarea id="table-columns" placeholder='{"id": "INTEGER PRIMARY KEY", "name": "TEXT NOT NULL", "email": "TEXT UNIQUE"}'></textarea>
                    </div>
                </div>
                <button class="btn" onclick="generateTable()">🏗️ Créer Table</button>
            </div>
        </div>
        
        <!-- Logs View -->
        <div id="logs" class="view">
            <div class="card">
                <h3>📊 Gestionnaire de Logs</h3>
                <div class="tabs">
                    <div class="tab active" onclick="switchLogTab('actions')">🎯 Actions</div>
                    <div class="tab" onclick="switchLogTab('chat')">💬 Chat</div>
                    <div class="tab" onclick="switchLogTab('errors')">❌ Erreurs</div>
                    <div class="tab" onclick="switchLogTab('system')">🖥️ Système</div>
                </div>
                <div class="flex gap-2 mb-3">
                    <button class="btn" onclick="refreshLogs()">🔄 Actualiser</button>
                    <button class="btn btn-warning" onclick="downloadLogs()">⬇️ Télécharger</button>
                    <button class="btn btn-error" onclick="clearAllLogs()">🗑️ Effacer tout</button>
                    <button class="btn btn-secondary" onclick="toggleAutoRefresh()">⏱️ Auto-refresh</button>
                </div>
            </div>
            
            <div class="card">
                <div class="flex justify-between items-center mb-3">
                    <h3 id="log-title">📊 Logs des Actions</h3>
                    <div class="flex gap-2">
                        <input type="text" id="log-filter" placeholder="Filtrer les logs..." style="width: 200px;">
                        <button class="btn btn-secondary" onclick="filterLogs()">🔍 Filtrer</button>
                    </div>
                </div>
                <div id="logs-content" class="terminal" style="min-height: 400px;">
                    Chargement des logs...
                </div>
            </div>
            
            <div class="card">
                <h3>📈 Statistiques</h3>
                <div class="grid grid-4">
                    <div class="text-center">
                        <div style="font-size: 2rem; color: var(--success);">✅</div>
                        <div>Succès</div>
                        <div id="stats-success" class="text-secondary">0</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size: 2rem; color: var(--error);">❌</div>
                        <div>Erreurs</div>
                        <div id="stats-errors" class="text-secondary">0</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size: 2rem; color: var(--warning);">⚠️</div>
                        <div>Avertissements</div>
                        <div id="stats-warnings" class="text-secondary">0</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size: 2rem; color: var(--accent);">📊</div>
                        <div>Total</div>
                        <div id="stats-total" class="text-secondary">0</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Backup View -->
        <div id="backup" class="view">
            <div class="card">
                <h3>💾 Gestionnaire de Sauvegardes</h3>
                <div class="flex gap-2 mb-3">
                    <button class="btn btn-success" onclick="createBackup()">💾 Créer Backup</button>
                    <button class="btn" onclick="listBackups()">📋 Lister</button>
                    <button class="btn btn-warning" onclick="scheduleBackup()">⏰ Programmer</button>
                    <button class="btn btn-secondary" onclick="restoreBackup()">🔄 Restaurer</button>
                </div>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <h3>⚙️ Configuration Backup</h3>
                    <div class="form-group">
                        <label>📝 Nom de la sauvegarde</label>
                        <input type="text" id="backup-name" placeholder="backup_manuel">
                    </div>
                    <div class="form-group">
                        <label>📂 Dossiers à inclure</label>
                        <textarea id="backup-folders" placeholder="core/&#10;extensions/&#10;api/"></textarea>
                    </div>
                    <div class="form-group">
                        <label>🚫 Dossiers à exclure</label>
                        <textarea id="backup-exclude" placeholder="logs/&#10;backups/&#10;tmp/"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="backup-compress"> 
                            🗜️ Compression ZIP
                        </label>
                    </div>
                </div>
                
                <div class="card">
                    <h3>📊 Statut des Sauvegardes</h3>
                    <div id="backup-status" class="status">
                        <div>📊 Prêt pour sauvegarde</div>
                    </div>
                    <div class="grid grid-2 mt-3">
                        <div class="text-center">
                            <div style="font-size: 2rem; color: var(--success);">💾</div>
                            <div>Dernière sauvegarde</div>
                            <div id="last-backup" class="text-secondary">Jamais</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size: 2rem; color: var(--accent);">📦</div>
                            <div>Taille totale</div>
                            <div id="backup-size" class="text-secondary">0 MB</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h3>📋 Liste des Sauvegardes</h3>
                <div id="backup-list" class="file-list">
                    <div class="text-center" style="padding: 40px; color: var(--text-secondary);">
                        <div style="font-size: 3rem; margin-bottom: 16px;">💾</div>
                        <p>Aucune sauvegarde trouvée</p>
                        <p>Cliquez sur "Créer Backup" pour commencer</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings View -->
        <div id="settings" class="view">
            <div class="tabs">
                <div class="tab active" onclick="switchSettingsTab('general')">🎛️ Général</div>
                <div class="tab" onclick="switchSettingsTab('appearance')">🎨 Apparence</div>
                <div class="tab" onclick="switchSettingsTab('security')">🔒 Sécurité</div>
                <div class="tab" onclick="switchSettingsTab('advanced')">⚙️ Avancé</div>
            </div>
            
            <!-- General Settings -->
            <div id="settings-general" class="settings-tab">
                <div class="grid grid-2">
                    <div class="card">
                        <h3>📝 Informations Générales</h3>
                        <div class="form-group">
                            <label>🏷️ Titre de l'application</label>
                            <input type="text" id="app-title" placeholder="SGC-AgentOne">
                        </div>
                        <div class="form-group">
                            <label>👤 Auteur</label>
                            <input type="text" id="app-author" placeholder="By AMICHI Amine">
                        </div>
                        <div class="form-group">
                            <label>📝 Description</label>
                            <textarea id="app-description" placeholder="Assistant universel de développement"></textarea>
                        </div>
                        <div class="form-group">
                            <label>🌐 Langue</label>
                            <select id="app-language">
                                <option value="fr">Français</option>
                                <option value="en">English</option>
                                <option value="es">Español</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h3>⚙️ Comportement</h3>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="auto-save"> 
                                💾 Sauvegarde automatique
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="file-watcher"> 
                                👁️ Surveillance des fichiers
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="syntax-highlighting"> 
                                🎨 Coloration syntaxique
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="backup-enabled"> 
                                💾 Sauvegardes automatiques
                            </label>
                        </div>
                        <div class="form-group">
                            <label>⏱️ Intervalle de sauvegarde (minutes)</label>
                            <input type="number" id="backup-interval" value="30" min="5" max="1440">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Appearance Settings -->
            <div id="settings-appearance" class="settings-tab hidden">
                <div class="grid grid-2">
                    <div class="card">
                        <h3>🎨 Thème et Couleurs</h3>
                        <div class="form-group">
                            <label>🌙 Mode sombre</label>
                            <select id="theme-mode">
                                <option value="dark">Sombre</option>
                                <option value="light">Clair</option>
                                <option value="auto">Automatique</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>🎨 Couleur d'accent</label>
                            <input type="color" id="accent-color" value="#38bdf8">
                        </div>
                        <div class="form-group">
                            <label>📝 Police de l'éditeur</label>
                            <select id="editor-font">
                                <option value="JetBrains Mono">JetBrains Mono</option>
                                <option value="Fira Code">Fira Code</option>
                                <option value="Source Code Pro">Source Code Pro</option>
                                <option value="Consolas">Consolas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>📏 Taille de police</label>
                            <input type="range" id="font-size" min="12" max="20" value="14">
                            <span id="font-size-value">14px</span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h3>🖼️ Interface</h3>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="show-line-numbers"> 
                                🔢 Numéros de ligne
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="word-wrap"> 
                                📏 Retour à la ligne automatique
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="minimap"> 
                                🗺️ Mini-carte
                            </label>
                        </div>
                        <div class="form-group">
                            <label>📐 Indentation</label>
                            <select id="indentation">
                                <option value="2">2 espaces</option>
                                <option value="4">4 espaces</option>
                                <option value="tab">Tabulations</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div id="settings-security" class="settings-tab hidden">
                <div class="grid grid-2">
                    <div class="card">
                        <h3>🔒 Sécurité</h3>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="debug-mode"> 
                                🐛 Mode Debug
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="blind-exec"> 
                                ⚡ Mode Blind-Exec (Dangereux)
                            </label>
                        </div>
                        <div class="form-group">
                            <label>🔑 Clé API (optionnelle)</label>
                            <input type="password" id="api-key" placeholder="Clé d'authentification">
                        </div>
                        <div class="form-group">
                            <label>🌐 IPs autorisées</label>
                            <textarea id="allowed-ips" placeholder="127.0.0.1&#10;192.168.1.*"></textarea>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h3>📊 Logs et Monitoring</h3>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="log-actions"> 
                                📝 Logger les actions
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="log-errors"> 
                                ❌ Logger les erreurs
                            </label>
                        </div>
                        <div class="form-group">
                            <label>📊 Niveau de log</label>
                            <select id="log-level">
                                <option value="error">Erreurs seulement</option>
                                <option value="warning">Erreurs + Avertissements</option>
                                <option value="info">Informations</option>
                                <option value="debug">Debug complet</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>🗑️ Rotation des logs (jours)</label>
                            <input type="number" id="log-rotation" value="30" min="1" max="365">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Advanced Settings -->
            <div id="settings-advanced" class="settings-tab hidden">
                <div class="grid grid-2">
                    <div class="card">
                        <h3>🖥️ Serveur</h3>
                        <div class="form-group">
                            <label>🌐 Port du serveur</label>
                            <input type="number" id="server-port-setting" value="5000" min="1000" max="65535">
                        </div>
                        <div class="form-group">
                            <label>🏠 Hôte</label>
                            <input type="text" id="server-host-setting" value="0.0.0.0">
                        </div>
                        <div class="form-group">
                            <label>⏱️ Timeout (secondes)</label>
                            <input type="number" id="server-timeout" value="30" min="5" max="300">
                        </div>
                        <div class="form-group">
                            <label>📊 Limite mémoire (MB)</label>
                            <input type="number" id="memory-limit" value="256" min="64" max="2048">
                        </div>
                    </div>
                    
                    <div class="card">
                        <h3>🔧 Performance</h3>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="cache-enabled"> 
                                💾 Cache activé
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="compression"> 
                                🗜️ Compression GZIP
                            </label>
                        </div>
                        <div class="form-group">
                            <label>📊 Taille max fichier (MB)</label>
                            <input type="number" id="max-file-size" value="50" min="1" max="500">
                        </div>
                        <div class="form-group">
                            <label>⏱️ Délai d'expiration cache (minutes)</label>
                            <input type="number" id="cache-timeout" value="60" min="5" max="1440">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Settings Actions -->
            <div class="card mt-3">
                <div class="flex gap-3">
                    <button class="btn btn-success" onclick="saveAllSettings()">💾 Enregistrer tout</button>
                    <button class="btn" onclick="loadAllSettings()">📂 Charger</button>
                    <button class="btn btn-warning" onclick="exportSettings()">📤 Exporter</button>
                    <button class="btn btn-secondary" onclick="importSettings()">📥 Importer</button>
                    <button class="btn btn-error" onclick="resetAllSettings()">🔄 Réinitialiser</button>
                </div>
                <div id="settings-status" class="status hidden mt-3"></div>
            </div>
        </div>
        
        <!-- Help View -->
        <div id="help" class="view">
            <div class="card">
                <h3>❓ Guide d'Aide SGC-AgentOne</h3>
                <div class="tabs">
                    <div class="tab active" onclick="switchHelpTab('commands')">📋 Commandes</div>
                    <div class="tab" onclick="switchHelpTab('features')">🚀 Fonctionnalités</div>
                    <div class="tab" onclick="switchHelpTab('troubleshooting')">🔧 Dépannage</div>
                    <div class="tab" onclick="switchHelpTab('api')">🔌 API</div>
                </div>
            </div>
            
            <!-- Commands Help -->
            <div id="help-commands" class="help-tab">
                <div class="card">
                    <h3>📋 Commandes Disponibles</h3>
                    <div class="grid grid-2">
                        <div>
                            <h4>📁 Gestion des Fichiers</h4>
                            <pre>createFile nom.ext : contenu
readFile nom.ext
listDir dossier
createDir nom-dossier
deleteFile nom.ext</pre>
                        </div>
                        <div>
                            <h4>🖥️ Serveur et Système</h4>
                            <pre>serverStatus
backup : description
startServer
stopServer
restartServer</pre>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <h3>💡 Exemples Pratiques</h3>
                    <div class="grid grid-2">
                        <div>
                            <h4>🌐 Développement Web</h4>
                            <pre>createFile index.html : &lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;&lt;title&gt;Mon Site&lt;/title&gt;&lt;/head&gt;
&lt;body&gt;&lt;h1&gt;Hello World!&lt;/h1&gt;&lt;/body&gt;
&lt;/html&gt;

createFile style.css : body {
    font-family: Arial, sans-serif;
    background: #f0f0f0;
    margin: 0;
    padding: 20px;
}</pre>
                        </div>
                        <div>
                            <h4>🐘 Développement PHP</h4>
                            <pre>createFile config.php : &lt;?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'myapp');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME,
        DB_USER, DB_PASS
    );
} catch(PDOException $e) {
    die("Erreur: " . $e-&gt;getMessage());
}</pre>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Features Help -->
            <div id="help-features" class="help-tab hidden">
                <div class="grid grid-2">
                    <div class="card">
                        <h3>🚀 Fonctionnalités Principales</h3>
                        <ul style="line-height: 2;">
                            <li><strong>💬 Chat Intelligent</strong> - Assistant conversationnel</li>
                            <li><strong>📁 Gestionnaire de Fichiers</strong> - Navigation et édition</li>
                            <li><strong>📝 Éditeur de Code</strong> - Coloration syntaxique</li>
                            <li><strong>⚡ Terminal</strong> - Commandes rapides</li>
                            <li><strong>🖥️ Serveur</strong> - Contrôle du serveur PHP</li>
                            <li><strong>🗄️ Base de Données</strong> - Gestion SQL</li>
                            <li><strong>📊 Logs</strong> - Monitoring et debug</li>
                            <li><strong>💾 Sauvegardes</strong> - Protection des données</li>
                        </ul>
                    </div>
                    
                    <div class="card">
                        <h3>⚙️ Fonctionnalités Avancées</h3>
                        <ul style="line-height: 2;">
                            <li><strong>🎨 Thèmes personnalisables</strong></li>
                            <li><strong>🔒 Sécurité renforcée</strong></li>
                            <li><strong>📱 Interface responsive</strong></li>
                            <li><strong>⚡ Mode Blind-Exec</strong></li>
                            <li><strong>🔄 Sauvegarde automatique</strong></li>
                            <li><strong>📊 Statistiques détaillées</strong></li>
                            <li><strong>🌐 Support multi-langues</strong></li>
                            <li><strong>🔌 API REST complète</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Troubleshooting Help -->
            <div id="help-troubleshooting" class="help-tab hidden">
                <div class="card">
                    <h3>🔧 Résolution de Problèmes</h3>
                    <div class="grid grid-2">
                        <div>
                            <h4>❌ Problèmes Courants</h4>
                            <div class="mb-3">
                                <strong>🚫 "Erreur de connexion au serveur"</strong>
                                <ul>
                                    <li>Vérifiez que le serveur PHP est démarré</li>
                                    <li>Contrôlez le port (par défaut 5000)</li>
                                    <li>Vérifiez les permissions des fichiers</li>
                                </ul>
                            </div>
                            
                            <div class="mb-3">
                                <strong>📁 "Fichier introuvable"</strong>
                                <ul>
                                    <li>Vérifiez le chemin du fichier</li>
                                    <li>Contrôlez les permissions de lecture</li>
                                    <li>Utilisez des chemins relatifs</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div>
                            <h4>🔍 Mode Debug</h4>
                            <p>Ajoutez <code>?debug=1</code> à l'URL pour activer le mode debug :</p>
                            <pre>http://localhost:5000/?debug=1</pre>
                            
                            <h4>📊 Vérification du Système</h4>
                            <ul>
                                <li><strong>PHP :</strong> Version 7.4+ requise</li>
                                <li><strong>Extensions :</strong> json, mbstring, zip</li>
                                <li><strong>Permissions :</strong> Lecture/écriture sur le dossier</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- API Help -->
            <div id="help-api" class="help-tab hidden">
                <div class="card">
                    <h3>🔌 Documentation API</h3>
                    <div class="grid grid-2">
                        <div>
                            <h4>📤 Endpoints Disponibles</h4>
                            <pre>POST /?action=chat
POST /?action=listFiles
POST /?action=saveSettings
GET  /?action=loadSettings
GET  /?action=getLogs
POST /?action=clearLogs</pre>
                        </div>
                        
                        <div>
                            <h4>📝 Format des Requêtes</h4>
                            <pre>// Chat
{
  "message": "createFile test.php : &lt;?php echo 'Hello'; ?&gt;"
}

// Réponse
{
  "success": true,
  "result": "✅ Fichier créé: test.php"
}</pre>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <h3>🔧 Intégration</h3>
                    <pre>// Exemple JavaScript
async function sendCommand(command) {
    const response = await fetch('/?action=chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: command })
    });
    
    const result = await response.json();
    return result;
}

// Utilisation
const result = await sendCommand('listDir .');
console.log(result.result);</pre>
                </div>
            </div>
        </div>
    </div>
    
    <div id="status">
        <div class="flex items-center gap-3">
            <span>🚀 SGC-AgentOne v2.1</span>
            <span id="connection-status">🟢 Connecté</span>
            <span id="current-project">📁 Projet: /</span>
        </div>
        <div class="flex items-center gap-3">
            <span id="current-time"><?php echo date('Y-m-d H:i:s'); ?></span>
            <span>👤 <?php echo get_current_user(); ?></span>
        </div>
    </div>

    <script>
        // Variables globales
        let currentView = 'chat';
        let currentLogTab = 'actions';
        let currentSettingsTab = 'general';
        let currentHelpTab = 'commands';
        let autoRefreshLogs = false;
        let autoRefreshInterval = null;
        
        // Navigation entre vues
        document.querySelectorAll(".nav-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const view = btn.dataset.view;
                
                // Mettre à jour navigation
                document.querySelectorAll(".nav-btn").forEach(b => b.classList.remove("active"));
            <div class="view-container">
                <div class="view-header">
                    <h2>📁 Gestionnaire de Fichiers</h2>
                    <div>
                        <button onclick="createNewFile()">➕ Nouveau Fichier</button>
                        <button onclick="createNewFolder()">📁 Nouveau Dossier</button>
                    </div>
                </div>
                <div class="view-content">
                    <div id="file-tree">
                        <p>Chargement de l'arborescence...</p>
                    </div>
                </div>
                document.querySelectorAll(".view").forEach(v => v.classList.remove("active"));
                const targetView = document.getElementById(view);
                if (targetView) {
                    targetView.classList.add("active");
                    currentView = view;
                    
                    // Actions spécifiques par vue
                    switch(view) {
                        case 'files':
                            loadFileList();
                            break;
                        case 'logs':
                            refreshLogs();
                            break;
                        case 'server':
                            checkServerStatus();
                            break;
                        case 'settings':
                            loadAllSettings();
                            break;
                        case 'backup':
                            listBackups();
                            break;
                    }
                }
            });
        });
        
        // === CHAT FUNCTIONALITY ===
        const messagesContainer = document.getElementById('messages');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-btn');
        
        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender} fade-in`;
            
            // Formatage spécial pour les réponses de l'IA
            if (sender === 'ai') {
                // Remplacer les retours à la ligne par des <br>
                text = text.replace(/\n/g, '<br>');
                // Mettre en forme les blocs de code
                text = text.replace(/```([\s\S]*?)```/g, '<pre>$1</pre>');
                messageDiv.innerHTML = `<strong>🤖 SGC-AgentOne:</strong> ${text}`;
            } else {
                messageDiv.innerHTML = `<strong>👤 Vous:</strong> ${text}`;
            }
            
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        async function sendMessage() {
            const text = messageInput.value.trim();
            if (!text) return;
            
            addMessage(text, 'user');
            messageInput.value = '';
            
            sendButton.disabled = true;
            sendButton.innerHTML = '<div class="loading"></div> Envoi...';
            
            try {
                const response = await fetch('?action=chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                
                const result = await response.json();
                
                if (result.error) {
                    addMessage(`❌ ${result.error}`, 'ai');
                } else if (result.success && result.result) {
                    addMessage(result.result, 'ai');
                } else {
                    addMessage("🤔 Réponse inattendue du serveur.", 'ai');
                }
            } catch (error) {
                addMessage(`🔌 Erreur de connexion: ${error.message}`, 'ai');
            } finally {
                sendButton.disabled = false;
                sendButton.innerHTML = '<span>📤</span> Envoyer';
            }
        }
        
        // Événements chat
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // === FILES FUNCTIONALITY ===
        async function loadFileList(path = '.') {
            const fileListDiv = document.getElementById("file-list");
            fileListDiv.innerHTML = '<div class="text-center" style="padding: 40px;"><div class="loading"></div><p>Chargement des fichiers...</p></div>';
            
            try {
                const response = await fetch(`?action=listFiles&path=${encodeURIComponent(path)}`);
                const result = await response.json();
                
                if (result.success && result.files) {
                    let html = '';
                    
                    // Bouton retour si pas à la racine
                    if (path !== '.') {
                        const parentPath = path.split('/').slice(0, -1).join('/') || '.';
                        html += `<div class="file-item" onclick="loadFileList('${parentPath}')">
                            <div class="file-icon">📁</div>
                            <div class="file-info">
                                <div class="file-name">.. (Dossier parent)</div>
                                <div class="file-meta">Remonter d'un niveau</div>
                            </div>
                        </div>`;
                    }
                    
                    // Trier: dossiers d'abord, puis fichiers
                    result.files.sort((a, b) => {
                        if (a.type !== b.type) {
                            return a.type === 'dir' ? -1 : 1;
                        }
                        return a.name.localeCompare(b.name);
                    });
                    
                    result.files.forEach(file => {
                        const icon = file.type === 'dir' ? '📁' : getFileIcon(file.extension);
                        const size = file.type === 'file' ? formatFileSize(file.size) : '';
                        const date = new Date(file.modified * 1000).toLocaleDateString();
                        const fullPath = path === '.' ? file.name : `${path}/${file.name}`;
                        
                        const onclick = file.type === 'dir' 
                            ? `loadFileList('${fullPath}')`
                            : `selectFile('${fullPath}')`;
                        
                        html += `<div class="file-item" onclick="${onclick}">
                            <div class="file-icon">${icon}</div>
                            <div class="file-info">
                                <div class="file-name">${file.name}</div>
                                <div class="file-meta">${size} • ${date}</div>
                            </div>
                        </div>`;
                    });
                    
                    fileListDiv.innerHTML = html;
                    document.getElementById('current-path').value = path;
                } else {
                    fileListDiv.innerHTML = '<div class="status error">❌ Erreur lors du chargement des fichiers</div>';
                }
            } catch (error) {
                fileListDiv.innerHTML = '<div class="status error">🔌 Erreur de connexion</div>';
            }
        }
        
        function getFileIcon(extension) {
            const icons = {
                'php': '🐘', 'html': '🌐', 'css': '🎨', 'js': '⚡', 'json': '📋',
                'md': '📝', 'txt': '📄', 'sql': '🗄️', 'zip': '📦', 'pdf': '📕',
                'jpg': '🖼️', 'jpeg': '🖼️', 'png': '🖼️', 'gif': '🖼️', 'svg': '🎨'
            };
            return icons[extension?.toLowerCase()] || '📄';
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function navigateToPath() {
            const path = document.getElementById('current-path').value.trim();
            loadFileList(path);
        }
        
        function createNewFile() {
            const filename = prompt("📄 Nom du nouveau fichier :");
            if (filename) {
                const path = document.getElementById('current-path').value;
                const fullPath = path === '.' ? filename : `${path}/${filename}`;
                addMessage(`createFile ${fullPath} : // Nouveau fichier créé`, "user");
                sendMessage();
            }
        }
        
        function createNewFolder() {
            const foldername = prompt("📁 Nom du nouveau dossier :");
            if (foldername) {
                const path = document.getElementById('current-path').value;
                const fullPath = path === '.' ? foldername : `${path}/${foldername}`;
                addMessage(`createDir ${fullPath}`, "user");
                sendMessage();
            }
        }
        
        function selectFile(filename) {
            // Charger le fichier dans l'éditeur
            document.getElementById('editor-file').value = filename;
            
            // Basculer vers l'éditeur
            document.querySelector('[data-view="editor"]').click();
            
            // Charger le contenu
            setTimeout(() => {
                loadFileInEditor();
            }, 100);
        }
        
        function uploadFile() {
            const input = document.createElement('input');
            input.type = 'file';
            input.multiple = true;
            input.onchange = (e) => {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const content = event.target.result;
                        const path = document.getElementById('current-path').value;
                        const fullPath = path === '.' ? file.name : `${path}/${file.name}`;
                        addMessage(`createFile ${fullPath} : ${content}`, "user");
                        sendMessage();
                    };
                    reader.readAsText(file);
                });
            };
            input.click();
        }
        
        // === EDITOR FUNCTIONALITY ===
        async function loadFileInEditor() {
            const filename = document.getElementById("editor-file").value.trim();
            if (!filename) {
                showStatus('❌ Veuillez entrer un nom de fichier', 'error');
                return;
            }
            
            try {
                const response = await fetch("?action=chat", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: `readFile ${filename}` })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const content = result.result.replace(/^📄 Contenu de [^:]+:\n\n/, '');
                    document.getElementById("code-editor").value = content;
                    showStatus(`✅ Fichier chargé: ${filename}`, 'success');
                } else {
                    showStatus(`❌ ${result.error}`, 'error');
                }
            } catch (error) {
                showStatus(`🔌 Erreur de connexion: ${error.message}`, 'error');
            }
        }
        
        async function saveFileFromEditor() {
            const filename = document.getElementById("editor-file").value.trim();
            const content = document.getElementById("code-editor").value;
            
            if (!filename) {
                showStatus('❌ Veuillez entrer un nom de fichier', 'error');
                return;
            }
            
            try {
                const response = await fetch("?action=chat", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: `createFile ${filename} : ${content}` })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showStatus(`✅ ${result.result}`, 'success');
                } else {
                    showStatus(`❌ ${result.error}`, 'error');
                }
            } catch (error) {
                showStatus(`🔌 Erreur de connexion: ${error.message}`, 'error');
            }
        }
        
        function clearEditor() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir vider l\'éditeur ?')) {
                document.getElementById("code-editor").value = '';
                document.getElementById("editor-file").value = '';
                showStatus('🗑️ Éditeur vidé', 'success');
            }
        }
        
        function formatCode() {
            const editor = document.getElementById("code-editor");
            let content = editor.value;
            
            // Formatage basique pour différents langages
            const filename = document.getElementById("editor-file").value.toLowerCase();
            
            if (filename.endsWith('.json')) {
                try {
                    const parsed = JSON.parse(content);
                    content = JSON.stringify(parsed, null, 2);
                    editor.value = content;
                    showStatus('🎨 Code JSON formaté', 'success');
                } catch (e) {
                    showStatus('❌ JSON invalide', 'error');
                }
            } else {
                // Formatage basique pour autres langages
                content = content.replace(/\s*{\s*/g, ' {\n    ')
                               .replace(/;\s*/g, ';\n    ')
                               .replace(/}\s*/g, '\n}\n');
                editor.value = content;
                showStatus('🎨 Code formaté (basique)', 'success');
            }
        }
        
        function toggleWrap() {
            const editor = document.getElementById("code-editor");
            if (editor.style.whiteSpace === 'pre-wrap') {
                editor.style.whiteSpace = 'pre';
                showStatus('📏 Retour ligne désactivé', 'success');
            } else {
                editor.style.whiteSpace = 'pre-wrap';
                showStatus('📏 Retour ligne activé', 'success');
            }
        }
        
        // === TERMINAL FUNCTIONALITY ===
        async function runQuickCommand(command) {
            document.getElementById("terminal-input").value = command;
            await runTerminalCommand();
        }
        
        async function runTerminalCommand() {
            const command = document.getElementById("terminal-input").value.trim();
            if (!command) return;
            
            const output = document.getElementById("terminal-output");
            output.textContent += `> ${command}\n`;
            output.scrollTop = output.scrollHeight;
            
            try {
                const response = await fetch("?action=chat", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: command })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    output.textContent += result.result + "\n\n";
                } else {
                    output.textContent += "❌ " + result.error + "\n\n";
                }
            } catch (error) {
                output.textContent += "🔌 Erreur de connexion: " + error.message + "\n\n";
            }
            
            output.scrollTop = output.scrollHeight;
            document.getElementById("terminal-input").value = "";
        }
        
        function clearTerminal() {
            document.getElementById("terminal-output").textContent = "";
            showStatus('🗑️ Terminal vidé', 'success');
        }
        
        // === SERVER FUNCTIONALITY ===
        async function checkServerStatus() {
            const statusDiv = document.getElementById("server-status");
            statusDiv.innerHTML = '<div class="loading"></div> Vérification du statut...';
            
            try {
                const response = await fetch("?action=chat", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: 'serverStatus' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (result.result.includes('🟢')) {
                        statusDiv.className = 'status success';
                        statusDiv.innerHTML = result.result;
                    } else {
                        statusDiv.className = 'status warning';
                        statusDiv.innerHTML = result.result;
                    }
                } else {
                    statusDiv.className = 'status error';
                    statusDiv.innerHTML = `❌ ${result.error}`;
                }
            } catch (error) {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = `🔌 Erreur de connexion: ${error.message}`;
            }
        }
        
        function startServer() {
            showStatus('▶️ Démarrage du serveur...', 'warning');
            // Implémentation à ajouter
        }
        
        function stopServer() {
            showStatus('⏹️ Arrêt du serveur...', 'warning');
            // Implémentation à ajouter
        }
        
        function restartServer() {
            showStatus('🔄 Redémarrage du serveur...', 'warning');
            // Implémentation à ajouter
        }
        
        function updateServerConfig() {
            const port = document.getElementById('server-port').value;
            const host = document.getElementById('server-host').value;
            showStatus(`⚙️ Configuration mise à jour: ${host}:${port}`, 'success');
        }
        
        // === DATABASE FUNCTIONALITY ===
        function createDatabase() {
            const name = prompt('🗄️ Nom de la base de données :');
            if (name) {
                showStatus(`🗄️ Création de la base "${name}"...`, 'warning');
                // Implémentation à ajouter
            }
        }
        
        function listTables() {
            showStatus('📋 Chargement des tables...', 'warning');
            // Implémentation à ajouter
        }
        
        function backupDatabase() {
            showStatus('💾 Sauvegarde de la base en cours...', 'warning');
            // Implémentation à ajouter
        }
        
        function optimizeDatabase() {
            showStatus('⚡ Optimisation de la base...', 'warning');
            // Implémentation à ajouter
        }
        
        function executeSQLQuery() {
            const query = document.getElementById('sql-query').value.trim();
            if (!query) {
                showStatus('❌ Veuillez entrer une requête SQL', 'error');
                return;
            }
            
            const results = document.getElementById('sql-results');
            results.textContent = `Exécution de: ${query}\n\nRésultat: Fonctionnalité en développement`;
            showStatus('▶️ Requête exécutée', 'success');
        }
        
        function formatSQL() {
            const query = document.getElementById('sql-query');
            let sql = query.value.toUpperCase();
            sql = sql.replace(/SELECT/g, '\nSELECT')
                     .replace(/FROM/g, '\nFROM')
                     .replace(/WHERE/g, '\nWHERE')
                     .replace(/ORDER BY/g, '\nORDER BY');
            query.value = sql.trim();
            showStatus('🎨 SQL formaté', 'success');
        }
        
        function clearSQL() {
            document.getElementById('sql-query').value = '';
            document.getElementById('sql-results').textContent = 'Aucune requête exécutée';
            showStatus('🗑️ Requête vidée', 'success');
        }
        
        function generateTable() {
            const tableName = document.getElementById('table-name').value.trim();
            const columns = document.getElementById('table-columns').value.trim();
            
            if (!tableName || !columns) {
                showStatus('❌ Nom de table et colonnes requis', 'error');
                return;
            }
            
            try {
                const columnsObj = JSON.parse(columns);
                let sql = `CREATE TABLE ${tableName} (\n`;
                
                const columnDefs = Object.entries(columnsObj).map(([name, type]) => 
                    `    ${name} ${type}`
                );
                
                sql += columnDefs.join(',\n') + '\n);';
                
                document.getElementById('sql-query').value = sql;
                showStatus(`🏗️ Table "${tableName}" générée`, 'success');
            } catch (e) {
                showStatus('❌ Format JSON invalide pour les colonnes', 'error');
            }
        }
        
        // === LOGS FUNCTIONALITY ===
        function switchLogTab(tab) {
            currentLogTab = tab;
            
            // Mettre à jour les onglets
            document.querySelectorAll('#logs .tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            // Mettre à jour le titre
            const titles = {
                'actions': '📊 Logs des Actions',
                'chat': '💬 Logs du Chat',
                'errors': '❌ Logs d\'Erreurs',
                'system': '🖥️ Logs Système'
            };
            document.getElementById('log-title').textContent = titles[tab];
            
            refreshLogs();
        }
        
        async function refreshLogs() {
            const logsContent = document.getElementById('logs-content');
            logsContent.textContent = 'Chargement des logs...';
            
            try {
                const response = await fetch(`?action=getLogs&type=${currentLogTab}`);
                const result = await response.json();
                
                if (result.success) {
                    if (result.logs.length === 0) {
                        logsContent.textContent = `Aucun log ${currentLogTab} trouvé.`;
                    } else {
                        logsContent.textContent = result.logs.join('\n');
                        updateLogStats(result.logs);
                    }
                } else {
                    logsContent.textContent = `Erreur: ${result.error}`;
                }
            } catch (error) {
                logsContent.textContent = `Erreur de connexion: ${error.message}`;
            }
            
            logsContent.scrollTop = logsContent.scrollHeight;
        }
        
        function updateLogStats(logs) {
            const stats = {
                success: logs.filter(log => log.includes('success')).length,
                errors: logs.filter(log => log.includes('error') || log.includes('ERROR')).length,
                warnings: logs.filter(log => log.includes('warning') || log.includes('WARNING')).length,
                total: logs.length
            };
            
            document.getElementById('stats-success').textContent = stats.success;
            document.getElementById('stats-errors').textContent = stats.errors;
            document.getElementById('stats-warnings').textContent = stats.warnings;
            document.getElementById('stats-total').textContent = stats.total;
        }
        
        function filterLogs() {
            const filter = document.getElementById('log-filter').value.toLowerCase();
            const logsContent = document.getElementById('logs-content');
            const allLogs = logsContent.textContent.split('\n');
            
            if (filter) {
                const filteredLogs = allLogs.filter(log => 
                    log.toLowerCase().includes(filter)
                );
                logsContent.textContent = filteredLogs.join('\n');
                showStatus(`🔍 ${filteredLogs.length} logs trouvés`, 'success');
            } else {
                refreshLogs();
            }
        }
        
        async function downloadLogs() {
            try {
                const response = await fetch(`?action=getLogs&type=${currentLogTab}`);
                const result = await response.json();
                
                if (result.success) {
                    const content = result.logs.join('\n');
                    const blob = new Blob([content], { type: 'text/plain' });
                    const url = URL.createObjectURL(blob);
                    
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `sgc-logs-${currentLogTab}-${new Date().toISOString().split('T')[0]}.txt`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                    
                    showStatus('⬇️ Logs téléchargés', 'success');
                }
            } catch (error) {
                showStatus(`❌ Erreur de téléchargement: ${error.message}`, 'error');
            }
        }
        
        async function clearAllLogs() {
            if (confirm('🗑️ Êtes-vous sûr de vouloir effacer tous les logs ?')) {
                try {
                    const response = await fetch('?action=clearLogs', { method: 'POST' });
                    const result = await response.json();
                    
                    if (result.success) {
                        refreshLogs();
                        showStatus('🗑️ Tous les logs ont été effacés', 'success');
                    } else {
                        showStatus(`❌ ${result.error}`, 'error');
                    }
                } catch (error) {
                    showStatus(`🔌 Erreur: ${error.message}`, 'error');
                }
            }
        }
        
        function toggleAutoRefresh() {
            autoRefreshLogs = !autoRefreshLogs;
            
            if (autoRefreshLogs) {
                autoRefreshInterval = setInterval(refreshLogs, 5000);
                showStatus('⏱️ Auto-refresh activé (5s)', 'success');
            } else {
                clearInterval(autoRefreshInterval);
                showStatus('⏱️ Auto-refresh désactivé', 'success');
            }
        }
        
        // === BACKUP FUNCTIONALITY ===
        async function createBackup() {
            const name = document.getElementById('backup-name').value.trim() || 'backup_manuel';
            const statusDiv = document.getElementById('backup-status');
            
            statusDiv.className = 'status warning';
            statusDiv.innerHTML = '<div class="loading"></div> Création de la sauvegarde...';
            
            try {
                const response = await fetch("?action=chat", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: `backup : ${name}` })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    statusDiv.className = 'status success';
                    statusDiv.innerHTML = `✅ ${result.result}`;
                    listBackups();
                    updateBackupStats();
                } else {
                    statusDiv.className = 'status error';
                    statusDiv.innerHTML = `❌ ${result.error}`;
                }
            } catch (error) {
                statusDiv.className = 'status error';
        <!-- Vue Éditeur -->
        <div id="editor" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>📝 Éditeur de Code</h2>
                    <div>
                        <button onclick="saveCurrentFile()">💾 Sauvegarder</button>
                        <button onclick="openFile()">📂 Ouvrir</button>
                    </div>
                `;
            } else {
                let html = '';
                backups.forEach(backup => {
                    html += `
                        <div class="file-item">
                            <div class="file-icon">💾</div>
                            <div class="file-info">
                                <div class="file-name">${backup.name}</div>
                                <div class="file-meta">${backup.size} • ${backup.date}</div>
                            </div>
                            <div class="flex gap-2">
                                <button class="btn btn-secondary" onclick="downloadBackup('${backup.name}')">⬇️</button>
                                <button class="btn btn-warning" onclick="restoreBackup('${backup.name}')">🔄</button>
                                <button class="btn btn-error" onclick="deleteBackup('${backup.name}')">🗑️</button>
                            </div>
                        </div>
                    `;
                });
                backupList.innerHTML = html;
            }
        }
        
        function updateBackupStats() {
            document.getElementById('last-backup').textContent = new Date().toLocaleString();
            document.getElementById('backup-size').textContent = '4.8 MB';
        }
        
        function scheduleBackup() {
            showStatus('⏰ Programmation des sauvegardes en développement', 'warning');
        }
        
        function restoreBackup(filename) {
            if (confirm(`🔄 Restaurer la sauvegarde "${filename}" ?\n\nCela remplacera les fichiers actuels.`)) {
                showStatus(`🔄 Restauration de ${filename}...`, 'warning');
                // Implémentation à ajouter
            }
        }
        
        function downloadBackup(filename) {
            showStatus(`⬇️ Téléchargement de ${filename}...`, 'success');
            // Implémentation à ajouter
        }
        
        function deleteBackup(filename) {
            if (confirm(`🗑️ Supprimer la sauvegarde "${filename}" ?`)) {
                showStatus(`🗑️ Suppression de ${filename}...`, 'success');
                listBackups();
            }
        }
        
        // === SETTINGS FUNCTIONALITY ===
        function switchSettingsTab(tab) {
            currentSettingsTab = tab;
            
            // Mettre à jour les onglets
            document.querySelectorAll('#settings .tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            // Mettre à jour les vues
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.add('hidden'));
            document.getElementById(`settings-${tab}`).classList.remove('hidden');
        }
        
        async function loadAllSettings() {
            try {
                const response = await fetch('?action=loadSettings');
                const result = await response.json();
                
                if (result.success && result.settings) {
                    const settings = result.settings;
                    
                    // Général
                    document.getElementById('app-title').value = settings.title || 'SGC-AgentOne';
                    document.getElementById('app-author').value = settings.author || 'By AMICHI Amine';
                    document.getElementById('app-description').value = settings.description || '';
                    document.getElementById('app-language').value = settings.language || 'fr';
                    document.getElementById('auto-save').checked = settings.auto_save !== false;
                    document.getElementById('file-watcher').checked = settings.file_watcher !== false;
                    document.getElementById('syntax-highlighting').checked = settings.syntax_highlighting !== false;
                    document.getElementById('backup-enabled').checked = settings.backup_enabled !== false;
                    document.getElementById('backup-interval').value = settings.backup_interval || 30;
                    
                    // Apparence
                    document.getElementById('theme-mode').value = settings.theme || 'dark';
                    document.getElementById('accent-color').value = settings.accent_color || '#38bdf8';
                    document.getElementById('editor-font').value = settings.editor_font || 'JetBrains Mono';
                    document.getElementById('font-size').value = settings.font_size || 14;
                    document.getElementById('font-size-value').textContent = (settings.font_size || 14) + 'px';
                    document.getElementById('show-line-numbers').checked = settings.show_line_numbers !== false;
                    document.getElementById('word-wrap').checked = settings.word_wrap !== false;
                    document.getElementById('minimap').checked = settings.minimap || false;
                    document.getElementById('indentation').value = settings.indentation || '2';
                    
                    // Sécurité
                    document.getElementById('debug-mode').checked = settings.debug || false;
                    document.getElementById('blind-exec').checked = settings.blind_exec_enabled || false;
                    document.getElementById('api-key').value = settings.api_key || '';
                    document.getElementById('allowed-ips').value = settings.allowed_ips || '';
                    document.getElementById('log-actions').checked = settings.log_actions !== false;
                    document.getElementById('log-errors').checked = settings.log_errors !== false;
                    document.getElementById('log-level').value = settings.log_level || 'info';
                    document.getElementById('log-rotation').value = settings.log_rotation || 30;
                    
                    // Avancé
                    document.getElementById('server-port-setting').value = settings.port || 5000;
                    document.getElementById('server-host-setting').value = settings.host || '0.0.0.0';
                    document.getElementById('server-timeout').value = settings.server_timeout || 30;
                    document.getElementById('memory-limit').value = settings.memory_limit || 256;
                    document.getElementById('cache-enabled').checked = settings.cache_enabled !== false;
                    document.getElementById('compression').checked = settings.compression !== false;
                    document.getElementById('max-file-size').value = settings.max_file_size || 50;
                    document.getElementById('cache-timeout').value = settings.cache_timeout || 60;
                    
                    showSettingsStatus('✅ Paramètres chargés', 'success');
                } else {
                    showSettingsStatus('⚠️ Paramètres par défaut chargés', 'warning');
                }
            } catch (error) {
                showSettingsStatus(`❌ Erreur de chargement: ${error.message}`, 'error');
            }
        }
        
        async function saveAllSettings() {
            const settings = {
                // Général
                title: document.getElementById('app-title').value.trim() || 'SGC-AgentOne',
                author: document.getElementById('app-author').value.trim() || 'By AMICHI Amine',
                description: document.getElementById('app-description').value.trim(),
                language: document.getElementById('app-language').value,
                auto_save: document.getElementById('auto-save').checked,
                file_watcher: document.getElementById('file-watcher').checked,
                syntax_highlighting: document.getElementById('syntax-highlighting').checked,
                backup_enabled: document.getElementById('backup-enabled').checked,
                backup_interval: parseInt(document.getElementById('backup-interval').value) || 30,
                
                // Apparence
                theme: document.getElementById('theme-mode').value,
                accent_color: document.getElementById('accent-color').value,
                editor_font: document.getElementById('editor-font').value,
                font_size: parseInt(document.getElementById('font-size').value) || 14,
                show_line_numbers: document.getElementById('show-line-numbers').checked,
                word_wrap: document.getElementById('word-wrap').checked,
                minimap: document.getElementById('minimap').checked,
                indentation: document.getElementById('indentation').value,
                
                // Sécurité
                debug: document.getElementById('debug-mode').checked,
                blind_exec_enabled: document.getElementById('blind-exec').checked,
                api_key: document.getElementById('api-key').value.trim(),
                allowed_ips: document.getElementById('allowed-ips').value.trim(),
                log_actions: document.getElementById('log-actions').checked,
                log_errors: document.getElementById('log-errors').checked,
                log_level: document.getElementById('log-level').value,
                log_rotation: parseInt(document.getElementById('log-rotation').value) || 30,
                
                // Avancé
                port: parseInt(document.getElementById('server-port-setting').value) || 5000,
                <div style="display: flex; flex: 1; overflow: hidden;">
                    <div style="width: 250px; background: hsl(222, 84%, 8%); border-right: 1px solid hsl(217, 19%, 20%); overflow-y: auto;">
                        <div style="padding: 16px;">
                            <h4>Fichiers Ouverts</h4>
                            <div id="open-files-list">
                                <p style="color: hsl(217, 10%, 58%); font-size: 0.9rem;">Aucun fichier ouvert</p>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column;">
                        <textarea id="code-editor" style="flex: 1; background: hsl(222, 84%, 4%); color: hsl(210, 40%, 95%); border: none; outline: none; padding: 16px; font-family: 'JetBrains Mono', monospace; font-size: 14px; line-height: 1.6; resize: none;" placeholder="Ouvrez un fichier pour commencer à éditer..."></textarea>
                    </div>
                    method: 'POST',
            </div>
        </div>

        <!-- Vue Terminal -->
        <div id="terminal" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>⚡ Terminal</h2>
                    <div>
                        <button onclick="clearTerminal()">🗑️ Effacer</button>
                        <button onclick="showTerminalHelp()">❓ Aide</button>
                    </div>
                    document.documentElement.style.setProperty('--accent', settings.accent_color);
                <div style="flex: 1; display: flex; flex-direction: column; background: hsl(222, 84%, 4%); margin: 8px; border-radius: 8px; overflow: hidden;">
                    <div id="terminal-output" style="flex: 1; padding: 16px; overflow-y: auto; font-family: 'JetBrains Mono', monospace; font-size: 14px; line-height: 1.4;">
                        <div style="color: hsl(113, 54%, 73%);">SGC-AgentOne Terminal v2.1</div>
                        <div style="color: hsl(217, 10%, 58%);">Tapez 'help' pour voir les commandes disponibles</div>
                        <div style="margin-top: 8px;"><span style="color: hsl(188, 95%, 42%);">sgc@localhost:~$</span> <span id="terminal-cursor">_</span></div>
                    </div>
                    <div style="display: flex; padding: 12px; background: hsl(215, 28%, 17%); border-top: 1px solid hsl(217, 19%, 20%);">
                        <span style="color: hsl(188, 95%, 42%); font-family: 'JetBrains Mono', monospace;">sgc@localhost:~$</span>
                        <input type="text" id="terminal-input" style="flex: 1; margin-left: 8px; background: transparent; border: none; outline: none; color: hsl(210, 40%, 95%); font-family: 'JetBrains Mono', monospace;" placeholder="Tapez une commande...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue Serveur -->
        <div id="server" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>🖥️ Serveur</h2>
                    <div>
                        <button id="server-start">▶️ Démarrer</button>
                        <button id="server-stop">⏹️ Arrêter</button>
                        <button id="server-restart">🔄 Redémarrer</button>
                    </div>
                </div>
                <div class="view-content">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                        <div style="background: hsl(222, 84%, 8%); padding: 16px; border-radius: 8px; border: 1px solid hsl(217, 19%, 20%);">
                            <h4 style="color: hsl(188, 95%, 42%); margin-bottom: 8px;">Statut</h4>
                            <div id="server-status" style="font-size: 1.2rem; font-weight: 600;">🔴 Arrêté</div>
                        </div>
                        <div style="background: hsl(222, 84%, 8%); padding: 16px; border-radius: 8px; border: 1px solid hsl(217, 19%, 20%);">
                            <h4 style="color: hsl(188, 95%, 42%); margin-bottom: 8px;">Port</h4>
                            <div style="font-size: 1.2rem; font-weight: 600;">5000</div>
                        </div>
                        <div style="background: hsl(222, 84%, 8%); padding: 16px; border-radius: 8px; border: 1px solid hsl(217, 19%, 20%);">
                            <h4 style="color: hsl(188, 95%, 42%); margin-bottom: 8px;">Uptime</h4>
                            <div id="server-uptime" style="font-size: 1.2rem; font-weight: 600;">--:--:--</div>
                        </div>
                    </div>
                    <div style="background: hsl(222, 84%, 8%); padding: 16px; border-radius: 8px; border: 1px solid hsl(217, 19%, 20%);">
                        <h4 style="color: hsl(188, 95%, 42%); margin-bottom: 12px;">Logs du Serveur</h4>
                        <div id="server-logs" style="background: hsl(222, 84%, 4%); padding: 12px; border-radius: 6px; height: 300px; overflow-y: auto; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.4;">
                            <div style="color: hsl(217, 10%, 58%);">En attente de logs...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue Base de Données -->
        <div id="database" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>🗄️ Base de Données</h2>
                    <div>
                        <button onclick="executeQuery()">▶️ Exécuter</button>
                        <button onclick="formatQuery()">🎨 Formater</button>
                        <button onclick="saveQuery()">💾 Sauvegarder</button>
                    </div>
                </div>
                <div style="display: flex; flex: 1; overflow: hidden;">
                    <div style="width: 300px; background: hsl(222, 84%, 8%); border-right: 1px solid hsl(217, 19%, 20%); overflow-y: auto;">
                        <div style="padding: 16px;">
                            <h4>Tables</h4>
                            <div id="database-tables">
                                <div style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">users (0)</div>
                                <div style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">projects (0)</div>
                                <div style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">logs (0)</div>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column;">
                        <div style="height: 200px; border-bottom: 1px solid hsl(217, 19%, 20%);">
                            <textarea id="sql-editor" style="width: 100%; height: 100%; background: hsl(222, 84%, 4%); color: hsl(210, 40%, 95%); border: none; outline: none; padding: 16px; font-family: 'JetBrains Mono', monospace; font-size: 14px; resize: none;" placeholder="SELECT * FROM users;"></textarea>
                        </div>
                        <div style="flex: 1; padding: 16px; overflow: auto;">
                            <h4 style="margin-bottom: 12px;">Résultats</h4>
                            <div id="query-results" style="background: hsl(222, 84%, 4%); padding: 12px; border-radius: 6px; min-height: 200px;">
                                <div style="color: hsl(217, 10%, 58%);">Exécutez une requête pour voir les résultats...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue Navigateur -->
        <div id="browser" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>🌐 Navigateur</h2>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button onclick="browserBack()">◀️</button>
                        <button onclick="browserForward()">▶️</button>
                        <button onclick="browserRefresh()">🔄</button>
                        <input type="text" id="browser-url" value="http://localhost:5000" style="width: 300px; padding: 6px 12px; background: hsl(222, 84%, 8%); border: 1px solid hsl(217, 19%, 20%); border-radius: 6px; color: hsl(210, 40%, 95%); outline: none;">
                        <button onclick="browserGo()">🔍</button>
                    </div>
                </div>
                <div style="flex: 1; margin: 8px; border-radius: 8px; overflow: hidden;">
                    <iframe id="browser-frame" src="about:blank" style="width: 100%; height: 100%; border: none; background: white;"></iframe>
                </div>
            </div>
        </div>

        <!-- Vue Projets -->
        <div id="projects" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>📂 Gestionnaire de Projets</h2>
                    <div>
                        <button onclick="createNewProject()">➕ Nouveau Projet</button>
                        <button onclick="importProject()">📥 Importer</button>
                        <button onclick="exportProjects()">📤 Exporter</button>
                    </div>
                </div>
                <div class="view-content">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;" id="projects-grid">
                        <div style="background: hsl(222, 84%, 8%); padding: 20px; border-radius: 12px; border: 1px solid hsl(217, 19%, 20%);">
                            <h3 style="color: hsl(188, 95%, 42%); margin-bottom: 8px;">SGC-AgentOne</h3>
                            <p style="color: hsl(217, 10%, 58%); margin-bottom: 12px; font-size: 0.9rem;">Assistant universel PHP</p>
                            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                                <span style="background: hsl(262, 90%, 20%); color: hsl(262, 90%, 75%); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">PHP</span>
                                <span style="background: hsl(17, 100%, 20%); color: hsl(17, 100%, 74%); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">HTML</span>
                                <span style="background: hsl(50, 100%, 20%); color: hsl(50, 100%, 74%); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">JS</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="background: hsl(113, 54%, 20%); color: hsl(113, 54%, 73%); padding: 4px 8px; border-radius: 6px; font-size: 0.8rem;">🟢 Actif</span>
                                <div>
                                    <button style="background: none; border: none; color: hsl(210, 40%, 95%); cursor: pointer; margin-left: 8px;">⭐</button>
                                    <button style="background: none; border: none; color: hsl(210, 40%, 95%); cursor: pointer; margin-left: 8px;">📂</button>
                                    <button style="background: none; border: none; color: hsl(210, 40%, 95%); cursor: pointer; margin-left: 8px;">⚙️</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue Prompts -->
        <div id="prompts" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>📝 Gestionnaire de Prompts</h2>
                    <div>
                        <button onclick="createNewPrompt()">➕ Nouveau Prompt</button>
                        <button onclick="importPrompts()">📥 Importer</button>
                        <button onclick="exportPrompts()">📤 Exporter</button>
                    </div>
                </div>
                <div style="display: flex; flex: 1; overflow: hidden;">
                    <div style="width: 250px; background: hsl(222, 84%, 8%); border-right: 1px solid hsl(217, 19%, 20%); overflow-y: auto;">
                        <div style="padding: 16px;">
                            <h4>Catégories</h4>
                            <div id="prompt-categories">
                                <div style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">Développement (3)</div>
                                <div style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">Serveur (2)</div>
                                <div style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">Base de données (1)</div>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1; padding: 20px; overflow: auto;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;" id="prompts-grid">
                            <div style="background: hsl(222, 84%, 8%); padding: 16px; border-radius: 8px; border: 1px solid hsl(217, 19%, 20%); cursor: pointer;">
                                <h4 style="color: hsl(188, 95%, 42%); margin-bottom: 8px;">Créer Structure PHP</h4>
                                <p style="color: hsl(217, 10%, 58%); font-size: 0.9rem; margin-bottom: 12px;">Génère une structure de projet PHP complète</p>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.8rem; color: hsl(217, 10%, 58%);">Utilisé 5 fois</span>
                                    <div>
                                        <button style="background: none; border: none; color: hsl(210, 40%, 95%); cursor: pointer;">▶️</button>
                                        <button style="background: none; border: none; color: hsl(210, 40%, 95%); cursor: pointer; margin-left: 8px;">✏️</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue Configuration -->
        <div id="config" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>⚙️ Configuration</h2>
                    <div>
                        <button onclick="saveAllSettings()">💾 Sauvegarder Tout</button>
                        <button onclick="resetToDefaults()">🔄 Réinitialiser</button>
                    </div>
                </div>
                <div style="display: flex; flex: 1; overflow: hidden;">
                    <div style="width: 250px; background: hsl(222, 84%, 8%); border-right: 1px solid hsl(217, 19%, 20%); overflow-y: auto;">
                        <div style="padding: 16px;">
                            <h4>Sections</h4>
                            <div id="config-sections">
                                <div class="config-section-btn active" data-section="general" style="padding: 8px; margin: 4px 0; background: hsl(188, 95%, 42%); color: hsl(222, 84%, 5%); border-radius: 4px; cursor: pointer;">🎯 Général</div>
                                <div class="config-section-btn" data-section="appearance" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">🎨 Apparence</div>
                                <div class="config-section-btn" data-section="editor" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">📝 Éditeur</div>
                                <div class="config-section-btn" data-section="server" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">🖥️ Serveur</div>
                                <div class="config-section-btn" data-section="security" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">🔒 Sécurité</div>
                                <div class="config-section-btn" data-section="performance" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">⚡ Performance</div>
                                <div class="config-section-btn" data-section="backup" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">💾 Sauvegarde</div>
                                <div class="config-section-btn" data-section="advanced" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">🔧 Avancé</div>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1; padding: 20px; overflow: auto;">
                        <div id="config-content">
                            <!-- Section Général -->
                            <div id="config-general" class="config-section">
                                <h3 style="color: hsl(188, 95%, 42%); margin-bottom: 20px;">🎯 Configuration Générale</h3>
                                <div style="display: grid; gap: 16px;">
                                    <div>
                                        <label style="display: block; margin-bottom: 6px; font-weight: 500;">Nom de l'application</label>
                                        <input type="text" value="SGC-AgentOne" style="width: 100%; padding: 8px 12px; background: hsl(222, 84%, 8%); border: 1px solid hsl(217, 19%, 20%); border-radius: 6px; color: hsl(210, 40%, 95%); outline: none;">
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 6px; font-weight: 500;">Langue</label>
                                        <select style="width: 100%; padding: 8px 12px; background: hsl(222, 84%, 8%); border: 1px solid hsl(217, 19%, 20%); border-radius: 6px; color: hsl(210, 40%, 95%); outline: none;">
                                            <option value="fr">Français</option>
                                            <option value="en">English</option>
                                            <option value="es">Español</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <input type="checkbox" id="auto-save" checked>
                                        <label for="auto-save">Sauvegarde automatique</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue Aide -->
        <div id="help" class="view">
            <div class="view-container">
                <div class="view-header">
                    <h2>❓ Centre d'Aide</h2>
                    <div>
                        <input type="text" placeholder="Rechercher..." style="padding: 6px 12px; background: hsl(222, 84%, 8%); border: 1px solid hsl(217, 19%, 20%); border-radius: 6px; color: hsl(210, 40%, 95%); outline: none;">
                    </div>
                </div>
                <div style="display: flex; flex: 1; overflow: hidden;">
                    <div style="width: 250px; background: hsl(222, 84%, 8%); border-right: 1px solid hsl(217, 19%, 20%); overflow-y: auto;">
                        <div style="padding: 16px;">
                            <h4>Sections</h4>
                            <div id="help-sections">
                                <div class="help-section-btn active" data-section="getting-started" style="padding: 8px; margin: 4px 0; background: hsl(188, 95%, 42%); color: hsl(222, 84%, 5%); border-radius: 4px; cursor: pointer;">🚀 Démarrage</div>
                                <div class="help-section-btn" data-section="commands" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">💬 Commandes</div>
                                <div class="help-section-btn" data-section="features" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">✨ Fonctionnalités</div>
                                <div class="help-section-btn" data-section="troubleshooting" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">🔧 Dépannage</div>
                                <div class="help-section-btn" data-section="api" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">🔌 API</div>
                                <div class="help-section-btn" data-section="examples" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">📚 Exemples</div>
                                <div class="help-section-btn" data-section="faq" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">❓ FAQ</div>
                                <div class="help-section-btn" data-section="about" style="padding: 8px; margin: 4px 0; background: hsl(215, 16%, 25%); border-radius: 4px; cursor: pointer;">ℹ️ À propos</div>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1; padding: 20px; overflow: auto;">
                        <div id="help-content">
                            <div id="help-getting-started" class="help-section">
                                <h3 style="color: hsl(188, 95%, 42%); margin-bottom: 20px;">🚀 Guide de Démarrage</h3>
                                <div style="background: hsl(222, 84%, 8%); padding: 20px; border-radius: 8px; border: 1px solid hsl(217, 19%, 20%); margin-bottom: 16px;">
                                    <h4 style="margin-bottom: 12px;">Bienvenue dans SGC-AgentOne !</h4>
                                    <p style="line-height: 1.6; margin-bottom: 12px;">SGC-AgentOne est un assistant universel qui vous permet de gérer vos projets, fichiers, et développement directement depuis votre navigateur.</p>
                                    <h5 style="margin: 16px 0 8px 0; color: hsl(188, 95%, 42%);">Première utilisation :</h5>
                                    <ol style="padding-left: 20px; line-height: 1.6;">
                                        <li>Explorez les différentes vues via le menu de navigation</li>
                                        <li>Utilisez le Chat pour interagir avec l'assistant</li>
                                        <li>Gérez vos fichiers via l'Explorateur</li>
                                        <li>Configurez l'application dans les Paramètres</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                } else {
                    showSettingsStatus(`❌ ${result.error}`, 'error');
                }
            } catch (error) {
                showSettingsStatus(`🔌 Erreur de sauvegarde: ${error.message}`, 'error');
            }
        <span>SGC-AgentOne v2.1 • Serveur: <span id="footer-server-status">🔴 Arrêté</span> • Projet: <span id="current-project">Aucun</span></span>
        
        function exportSettings() {
            loadAllSettings().then(() => {
                const settings = {
        }
        
        // Variables globales
        let token = localStorage.getItem('auth_token') || '';
        let terminalHistory = [];
        let terminalHistoryIndex = -1;
        
        // Charger le token au démarrage
        fetch('/api/auth', { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.token) {
                    token = data.token;
                    localStorage.setItem('auth_token', token);
                }
            })
            .catch(() => {});
        
        // Navigation entre les vues
        function showView(viewName) {
            document.querySelectorAll('.view').forEach(view => {
                view.classList.remove('active');
            });
            document.querySelectorAll('#nav-menu button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(viewName).classList.add('active');
            document.querySelector(`[data-view="${viewName}"]`).classList.add('active');
            
            localStorage.setItem('lastView', viewName);
        }
        
        // Gestion des clics sur la navigation
        document.querySelectorAll('#nav-menu button').forEach(btn => {
            btn.addEventListener('click', () => {
                showView(btn.dataset.view);
            });
        });
        
        // Charger la dernière vue
        const lastView = localStorage.getItem('lastView') || 'chat';
        showView(lastView);
        
        // === FONCTIONS CHAT ===
        const messagesContainer = document.getElementById('messages');
        const inputField = document.getElementById('input');
        const sendButton = document.getElementById('send');
        
        function appendMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            messageDiv.textContent = text;
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        async function sendMessage() {
            const text = inputField.value.trim();
            if (!text) return;
            
            appendMessage(text, 'user');
            inputField.value = '';
            
            sendButton.disabled = true;
            sendButton.textContent = 'Envoi...';
            
            try {
                const response = await fetch('/api/chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                const result = await response.json();
                
                if (result.error) {
                    appendMessage('❌ ' + result.error, 'ai');
                } else if (result.success && result.result) {
                    appendMessage(result.result, 'ai');
                } else {
                    appendMessage('🤖 Réponse inattendue du serveur.', 'ai');
                }
            } catch (error) {
                appendMessage('🔌 Erreur de connexion au serveur.', 'ai');
            } finally {
                sendButton.disabled = false;
                sendButton.textContent = 'Envoyer';
            }
        }
        
        sendButton.addEventListener('click', sendMessage);
        inputField.addEventListener('keypress', e => {
            if (e.key === 'Enter') sendMessage();
        });
        
        // === FONCTIONS FICHIERS ===
        function createNewFile() {
            const filename = prompt('Nom du fichier :');
            if (filename) {
                appendMessage(`createFile ${filename} : // Nouveau fichier`, 'user');
                sendMessage();
            }
        }
        
        function createNewFolder() {
            const foldername = prompt('Nom du dossier :');
            if (foldername) {
                appendMessage(`createDir ${foldername}`, 'user');
                sendMessage();
            }
        }
        
        // === FONCTIONS ÉDITEUR ===
        function saveCurrentFile() {
            const content = document.getElementById('code-editor').value;
            if (content.trim()) {
                alert('Fichier sauvegardé !');
            }
        }
        
        function openFile() {
            const input = document.createElement('input');
            input.type = 'file';
            input.onchange = (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('code-editor').value = e.target.result;
                        document.getElementById('open-files-list').innerHTML = `<div style="padding: 8px; background: hsl(215, 16%, 25%); border-radius: 4px; margin-bottom: 4px;">${file.name}</div>`;
                    };
                    reader.readAsText(file);
                }
            };
            input.click();
        }
        
        // === FONCTIONS TERMINAL ===
        const terminalOutput = document.getElementById('terminal-output');
        const terminalInput = document.getElementById('terminal-input');
        
        function addTerminalLine(text, type = 'output') {
            const line = document.createElement('div');
            if (type === 'command') {
                line.innerHTML = `<span style="color: hsl(188, 95%, 42%);">sgc@localhost:~$</span> ${text}`;
            } else if (type === 'error') {
                line.style.color = 'hsl(310, 100%, 75%)';
                line.textContent = text;
            } else {
                line.textContent = text;
            }
            terminalOutput.appendChild(line);
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
        }
        
        function executeTerminalCommand(command) {
            addTerminalLine(command, 'command');
            
            switch (command.toLowerCase().trim()) {
                case 'help':
                    addTerminalLine('Commandes disponibles:');
                    addTerminalLine('  ls        - Lister les fichiers');
                    addTerminalLine('  pwd       - Afficher le répertoire courant');
                    addTerminalLine('  status    - Statut du serveur');
                    addTerminalLine('  clear     - Effacer l\'écran');
                    addTerminalLine('  help      - Afficher cette aide');
                    break;
                case 'ls':
                    addTerminalLine('index.php  core/  extensions/  api/');
                    break;
                case 'pwd':
                    addTerminalLine('/home/sgc-agentone');
                    break;
                case 'status':
                    addTerminalLine('SGC-AgentOne v2.1 - Status: Running');
                    addTerminalLine('Port: 5000');
                    addTerminalLine('Uptime: 00:15:32');
                    break;
                case 'clear':
                    clearTerminal();
                    return;
                default:
                    addTerminalLine(`Commande non reconnue: ${command}`, 'error');
                    addTerminalLine('Tapez "help" pour voir les commandes disponibles');
            }
            
            addTerminalLine('');
        }
        
        function clearTerminal() {
            terminalOutput.innerHTML = `
                <div style="color: hsl(113, 54%, 73%);">SGC-AgentOne Terminal v2.1</div>
                <div style="color: hsl(217, 10%, 58%);">Tapez 'help' pour voir les commandes disponibles</div>
                <div style="margin-top: 8px;"><span style="color: hsl(188, 95%, 42%);">sgc@localhost:~$</span> <span id="terminal-cursor">_</span></div>
            `;
        }
        
        function showTerminalHelp() {
            executeTerminalCommand('help');
        }
        
        terminalInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const command = terminalInput.value.trim();
                if (command) {
                    terminalHistory.push(command);
                    terminalHistoryIndex = terminalHistory.length;
                    executeTerminalCommand(command);
                }
                terminalInput.value = '';
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
        
        // === FONCTIONS SERVEUR ===
        function updateServerStatus() {
            // Simulation du statut serveur
            const isRunning = Math.random() > 0.3;
            const statusElement = document.getElementById('server-status');
            const footerStatusElement = document.getElementById('footer-server-status');
            
            if (isRunning) {
                statusElement.innerHTML = '🟢 En marche';
                footerStatusElement.innerHTML = '🟢 En marche';
            } else {
                statusElement.innerHTML = '🔴 Arrêté';
                footerStatusElement.innerHTML = '🔴 Arrêté';
            }
        }
        
        // === FONCTIONS NAVIGATEUR ===
        function browserBack() {
            document.getElementById('browser-frame').contentWindow.history.back();
        }
        
        function browserForward() {
            document.getElementById('browser-frame').contentWindow.history.forward();
        }
        
        function browserRefresh() {
            document.getElementById('browser-frame').contentWindow.location.reload();
        }
        
        function browserGo() {
            const url = document.getElementById('browser-url').value;
            document.getElementById('browser-frame').src = url;
        }
        
        // === FONCTIONS BASE DE DONNÉES ===
        function executeQuery() {
            const query = document.getElementById('sql-editor').value;
            const resultsDiv = document.getElementById('query-results');
            
            if (query.trim()) {
                resultsDiv.innerHTML = `
                    <div style="color: hsl(113, 54%, 73%); margin-bottom: 8px;">✅ Requête exécutée avec succès</div>
                    <div style="color: hsl(217, 10%, 58%);">Résultats simulés pour: ${query.substring(0, 50)}...</div>
                `;
            }
        }
        
        function formatQuery() {
            const editor = document.getElementById('sql-editor');
            // Formatage basique SQL
            let query = editor.value;
            query = query.replace(/select/gi, 'SELECT');
            query = query.replace(/from/gi, 'FROM');
            query = query.replace(/where/gi, 'WHERE');
            query = query.replace(/order by/gi, 'ORDER BY');
            editor.value = query;
        }
        
        function saveQuery() {
            const query = document.getElementById('sql-editor').value;
            if (query.trim()) {
                alert('Requête sauvegardée !');
            }
        }
        
        // === FONCTIONS PROJETS ===
        function createNewProject() {
            const name = prompt('Nom du projet :');
            if (name) {
                alert(`Projet "${name}" créé !`);
            }
        }
        
        function importProject() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.zip';
            input.onchange = () => {
                alert('Projet importé !');
            };
            input.click();
        }
        
        function exportProjects() {
            alert('Projets exportés !');
        }
        
        // === FONCTIONS PROMPTS ===
        function createNewPrompt() {
            const name = prompt('Nom du prompt :');
            if (name) {
                alert(`Prompt "${name}" créé !`);
            }
        }
        
        function importPrompts() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            input.onchange = () => {
                alert('Prompts importés !');
            };
            input.click();
        }
        
        function exportPrompts() {
            alert('Prompts exportés !');
        }
        
        // === FONCTIONS CONFIGURATION ===
        function saveAllSettings() {
            alert('Tous les paramètres sauvegardés !');
        }
        
        function resetToDefaults() {
            if (confirm('Réinitialiser tous les paramètres ?')) {
                alert('Paramètres réinitialisés !');
            }
        }
        
        // Navigation des sections de configuration
        document.querySelectorAll('.config-section-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.config-section-btn').forEach(b => {
                    b.style.background = 'hsl(215, 16%, 25%)';
                    b.style.color = 'hsl(210, 40%, 95%)';
                });
                btn.style.background = 'hsl(188, 95%, 42%)';
                btn.style.color = 'hsl(222, 84%, 5%)';
            });
        });
        
        // Navigation des sections d'aide
        document.querySelectorAll('.help-section-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.help-section-btn').forEach(b => {
                    b.style.background = 'hsl(215, 16%, 25%)';
                    b.style.color = 'hsl(210, 40%, 95%)';
                });
                btn.style.background = 'hsl(188, 95%, 42%)';
                btn.style.color = 'hsl(222, 84%, 5%)';
            });
        });
        
        // === MISE À JOUR TEMPS RÉEL ===
        function updateTimestamp() {
            const now = new Date();
            document.getElementById('timestamp').textContent = now.toISOString().replace('T', ' ').substring(0, 19);
        }
        
        // Mise à jour automatique
        setInterval(updateTimestamp, 1000);
        setInterval(updateServerStatus, 5000);
        
        // Initialisation
        updateTimestamp();
        updateServerStatus();
    </script>
</body>
</html>