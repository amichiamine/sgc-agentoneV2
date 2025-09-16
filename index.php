<?php
/**
 * SGC-AgentOne v2.1 - Solution Simple et Optimale
 * Point d'entrée universel avec auto-installation
 * Configuration Zéro - Fonctionne partout
 */

// === CONFIGURATION ===
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';
$projectRoot = __DIR__;

// === FONCTIONS D'AUTO-INSTALLATION ===

function createProjectStructure($root) {
    // Créer les dossiers nécessaires
    $dirs = [
        'core/config',
        'core/logs', 
        'core/agents/actions',
        'api',
        'extensions/webview',
        'prompts'
    ];
    
    foreach ($dirs as $dir) {
        $path = $root . '/' . $dir;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
    
    // Créer les fichiers de configuration
    $configPath = $root . '/core/config/settings.json';
    if (!file_exists($configPath)) {
        $defaultConfig = [
            'port' => 5000,
            'host' => '0.0.0.0',
            'debug' => false,
            'theme' => 'sgc-commander',
            'blind_exec_enabled' => false
        ];
        file_put_contents($configPath, json_encode($defaultConfig, JSON_PRETTY_PRINT));
    }
    
    // Créer l'API de chat
    $apiPath = $root . '/api';
    $chatAPI = $apiPath . '/chat.php';
    if (!file_exists($chatAPI)) {
        $content = '<?php
header("Content-Type: application/json");
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input["message"])) {
    echo json_encode(["error" => "Message manquant"]);
    exit;
}

$message = trim($input["message"]);

// Parser simple : action cible : contenu
if (strpos($message, ":") !== false) {
    list($actionTarget, $content) = explode(":", $message, 2);
    $actionTarget = trim($actionTarget);
    $content = trim($content);
    
    $parts = explode(" ", trim($actionTarget), 2);
    $action = $parts[0];
    $target = isset($parts[1]) ? trim($parts[1]) : "";
    $content = trim($content);
    
    switch ($action) {
        case "createFile":
            if ($target && $content) {
                file_put_contents($target, $content);
                echo json_encode(["success" => true, "response" => "Fichier créé: $target"]);
            } else {
                echo json_encode(["error" => "Cible ou contenu manquant"]);
            }
            break;
            
        case "readFile":
            if ($target && file_exists($target)) {
                $fileContent = file_get_contents($target);
                echo json_encode(["success" => true, "response" => "Contenu de $target:<br><pre>" . htmlspecialchars($fileContent) . "</pre>"]);
            } else {
                echo json_encode(["error" => "Fichier introuvable: $target"]);
            }
            break;
            
        case "listDir":
            $dir = $target ?: ".";
            if (is_dir($dir)) {
                $files = array_diff(scandir($dir), [".", ".."]);
                $list = implode("<br>", array_map(function($f) use ($dir) {
                    return (is_dir("$dir/$f") ? "📁 " : "📄 ") . $f;
                }, $files));
                echo json_encode(["success" => true, "response" => "Contenu de $dir:<br>$list"]);
            } else {
                echo json_encode(["error" => "Dossier introuvable: $dir"]);
            }
            break;
            
        default:
            echo json_encode(["error" => "Action inconnue: $action"]);
    }
} else {
    echo json_encode(["error" => "Format invalide. Utilisez: action cible : contenu"]);
}
?>';
        file_put_contents($chatAPI, $content);
    }
}

// === LOGIQUE PRINCIPALE ===

try {
    // Mode debug
    if ($debug) {
        echo "<!DOCTYPE html><html><head><title>🔍 Debug SGC-AgentOne</title>";
        echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#0a0f1c;color:#e2e8f0;}</style></head><body>";
        echo "<h1>🔍 Debug SGC-AgentOne v2.1</h1>";
        echo "<p><strong>Racine du projet:</strong> " . htmlspecialchars($projectRoot) . "</p>";
        echo "<p><strong>Structure créée:</strong> ✅ OUI</p>";
        echo "<p><a href='?' style='color:#38bdf8;'>🚀 Accéder à SGC-AgentOne</a></p>";
        echo "</body></html>";
        exit;
    }
    
    // Gestion API
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'chat':
                header("Content-Type: application/json");
                $input = json_decode(file_get_contents("php://input"), true);
                
                if (!$input || !isset($input["message"])) {
                    echo json_encode(["error" => "Message manquant"]);
                    exit;
                }
                
                $message = trim($input["message"]);
                
                // Parser simple : action cible : contenu
                if (strpos($message, ":") !== false) {
                    list($actionTarget, $content) = explode(":", $message, 2);
                    $actionTarget = trim($actionTarget);
                    $content = trim($content);
                    
                    $parts = explode(" ", trim($actionTarget), 2);
                    $action = $parts[0];
                    $target = isset($parts[1]) ? trim($parts[1]) : "";
                    
                    switch ($action) {
                        case "createFile":
                            if ($target && $content) {
                                $filePath = $projectRoot . '/' . $target;
                                $dir = dirname($filePath);
                                if (!is_dir($dir)) mkdir($dir, 0755, true);
                                file_put_contents($filePath, $content);
                                echo json_encode(["success" => true, "result" => "✅ Fichier créé: $target"]);
                            } else {
                                echo json_encode(["error" => "Cible ou contenu manquant"]);
                            }
                            break;
                            
                        case "readFile":
                            $filePath = $projectRoot . '/' . $target;
                            if ($target && file_exists($filePath)) {
                                $fileContent = file_get_contents($filePath);
                                echo json_encode(["success" => true, "result" => "📄 Contenu de $target:\n\n" . $fileContent]);
                            } else {
                                echo json_encode(["error" => "Fichier introuvable: $target"]);
                            }
                            break;
                            
                        case "listDir":
                            $dir = $target ?: ".";
                            $dirPath = $projectRoot . '/' . $dir;
                            if (is_dir($dirPath)) {
                                $files = array_diff(scandir($dirPath), [".", ".."]);
                                $list = [];
                                foreach ($files as $f) {
                                    $icon = is_dir("$dirPath/$f") ? "📁" : "📄";
                                    $list[] = "$icon $f";
                                }
                                $result = "📂 Contenu de $dir:\n\n" . implode("\n", $list);
                                echo json_encode(["success" => true, "result" => $result]);
                            } else {
                                echo json_encode(["error" => "Dossier introuvable: $dir"]);
                            }
                            break;
                            
                        case "createDir":
                            if ($target) {
                                $dirPath = $projectRoot . '/' . $target;
                                if (!is_dir($dirPath)) {
                                    mkdir($dirPath, 0755, true);
                                    echo json_encode(["success" => true, "result" => "📁 Dossier créé: $target"]);
                                } else {
                                    echo json_encode(["error" => "Le dossier existe déjà: $target"]);
                                }
                            } else {
                                echo json_encode(["error" => "Nom du dossier manquant"]);
                            }
                            break;
                            
                        case "deleteFile":
                            $filePath = $projectRoot . '/' . $target;
                            if ($target && file_exists($filePath)) {
                                unlink($filePath);
                                echo json_encode(["success" => true, "result" => "🗑️ Fichier supprimé: $target"]);
                            } else {
                                echo json_encode(["error" => "Fichier introuvable: $target"]);
                            }
                            break;
                            
                        default:
                            echo json_encode(["error" => "Action inconnue: $action. Actions disponibles: createFile, readFile, listDir, createDir, deleteFile"]);
                    }
                } else {
                    echo json_encode(["error" => "Format invalide. Utilisez: action cible : contenu\n\nExemples:\n• createFile test.txt : Hello World\n• listDir .\n• readFile index.php"]);
                }
                exit;
                
            case 'listFiles':
                header("Content-Type: application/json");
                $files = [];
                $items = scandir($projectRoot);
                foreach ($items as $item) {
                    if ($item !== '.' && $item !== '..') {
                        $files[] = [
                            'name' => $item,
                            'type' => is_dir($projectRoot . '/' . $item) ? 'dir' : 'file'
                        ];
                    }
                }
                echo json_encode(['success' => true, 'files' => $files]);
                exit;
                
            case 'saveSettings':
                header("Content-Type: application/json");
                $input = json_decode(file_get_contents("php://input"), true);
                
                if ($input) {
                    $settingsPath = $projectRoot . '/core/config/settings.json';
                    $settings = [
                        'title' => $input['title'] ?? 'SGC-AgentOne',
                        'theme' => $input['theme'] ?? 'dark',
                        'port' => $input['port'] ?? 5000,
                        'debug' => $input['debug'] ?? false
                    ];
                    
                    file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
                    echo json_encode(['success' => true, 'message' => 'Paramètres sauvegardés']);
                } else {
                    echo json_encode(['error' => 'Données invalides']);
                }
                exit;
                
            case 'loadSettings':
                header("Content-Type: application/json");
                $settingsPath = $projectRoot . '/core/config/settings.json';
                
                if (file_exists($settingsPath)) {
                    $settings = json_decode(file_get_contents($settingsPath), true);
                    echo json_encode(['success' => true, 'settings' => $settings]);
                } else {
                    $defaultSettings = [
                        'title' => 'SGC-AgentOne',
                        'theme' => 'dark',
                        'port' => 5000,
                        'debug' => false
                    ];
                    echo json_encode(['success' => true, 'settings' => $defaultSettings]);
                }
                exit;
        }
    }
    
    // Vérification et création automatique de la structure
    createProjectStructure($projectRoot);
    
    // Servir l'interface principale
    header('Content-Type: text/html; charset=utf-8');
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGC-AgentOne v2.1</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0f1c; color: #e2e8f0; line-height: 1.6;
        }
        #header { 
            background: #1e293b; padding: 12px 20px; border-bottom: 1px solid #334155;
            display: flex; justify-content: space-between; align-items: center;
        }
        #header h1 { font-size: 1.2rem; color: #38bdf8; }
        #nav { display: flex; gap: 8px; }
        #nav button { 
            background: #334155; border: none; color: #e2e8f0; padding: 8px 16px;
            border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: all 0.2s;
        }
        #nav button:hover { background: #475569; }
        #nav button.active { background: #38bdf8; color: #0a0f1c; }
        #main { height: calc(100vh - 60px); display: flex; flex-direction: column; }
        .view { display: none; flex: 1; padding: 20px; }
        .view.active { display: flex; flex-direction: column; }
        #chat-container { flex: 1; display: flex; flex-direction: column; }
        #messages { flex: 1; overflow-y: auto; padding: 16px; background: #1e293b; border-radius: 8px; margin-bottom: 16px; }
        .message { margin-bottom: 12px; padding: 12px; border-radius: 8px; }
        .user { background: #334155; margin-left: 20%; }
        .ai { background: #0f172a; margin-right: 20%; border-left: 3px solid #38bdf8; }
        #input-area { display: flex; gap: 12px; }
        #message-input { 
            flex: 1; padding: 12px; background: #1e293b; border: 1px solid #334155;
            border-radius: 6px; color: #e2e8f0; font-size: 1rem; outline: none;
        }
        #send-btn { 
            padding: 12px 24px; background: #38bdf8; color: #0a0f1c; border: none;
            border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.2s;
        }
        #send-btn:hover { background: #0ea5e9; }
        #status { 
            position: fixed; bottom: 0; left: 0; right: 0; background: #1e293b;
            padding: 8px 20px; font-size: 0.8rem; color: #94a3b8; border-top: 1px solid #334155;
        }
        .success { color: #22c55e; }
        .error { color: #ef4444; }
        .loading { color: #f59e0b; }
        .settings-group { margin-bottom: 20px; }
        .settings-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .settings-group input, .settings-group select { 
            width: 100%; padding: 10px; background: #1e293b; border: 1px solid #334155;
            border-radius: 6px; color: #e2e8f0; font-size: 1rem;
        }
        .btn { 
            padding: 10px 20px; background: #38bdf8; color: #0a0f1c; border: none;
            border-radius: 6px; cursor: pointer; font-weight: 600; margin-right: 10px;
        }
        .btn-secondary { background: #334155; color: #e2e8f0; }
        .file-list { margin-top: 20px; }
        .file-item { 
            padding: 10px; background: #1e293b; margin-bottom: 8px; border-radius: 6px;
            cursor: pointer; transition: background 0.2s;
        }
        .file-item:hover { background: #334155; }
    </style>
</head>
<body>
    <div id="header">
        <h1>🚀 SGC-AgentOne</h1>
        <div id="nav">
            <button class="nav-btn active" data-view="chat">💬 Chat</button>
            <button class="nav-btn" data-view="files">📁 Fichiers</button>
            <button class="nav-btn" data-view="editor">📝 Éditeur</button>
            <button class="nav-btn" data-view="terminal">⚡ Terminal</button>
            <button class="nav-btn" data-view="settings">⚙️ Paramètres</button>
            <button class="nav-btn" data-view="help">❓ Aide</button>
        </div>
    </div>
    
    <div id="main">
        <div id="chat" class="view active">
            <div id="chat-container">
                <div id="messages">
                    <div class="message ai">
                        <strong>SGC-AgentOne:</strong> Bonjour ! Je suis votre assistant de développement. 
                        Tapez vos commandes au format: <strong>action cible : contenu</strong>
                        <br><br>Exemples:
                        <br>• <strong>createFile test.php : &lt;?php echo "Hello"; ?&gt;</strong>
                        <br>• <strong>listDir .</strong>
                        <br>• <strong>readFile index.php</strong>
                        <br>• <strong>createDir mon-dossier</strong>
                        <br>• <strong>deleteFile test.txt</strong>
                    </div>
                </div>
                <div id="input-area">
                    <input type="text" id="message-input" placeholder="Tapez votre commande..." autocomplete="off">
                    <button id="send-btn">Envoyer</button>
                </div>
            </div>
        </div>
        
        <div id="files" class="view">
            <h2>📁 Gestionnaire de Fichiers</h2>
            <div class="settings-group">
                <button class="btn" onclick="loadFileList()">🔄 Actualiser la liste</button>
                <button class="btn btn-secondary" onclick="createNewFile()">➕ Nouveau fichier</button>
            </div>
            <div id="file-list" class="file-list">
                <p>Cliquez sur "Actualiser" pour voir les fichiers...</p>
            </div>
        </div>
        
        <div id="editor" class="view">
            <h2>📝 Éditeur de Code</h2>
            <div class="settings-group">
                <label for="editor-file">Fichier à éditer</label>
                <input type="text" id="editor-file" placeholder="Nom du fichier (ex: index.php)">
                <button class="btn" onclick="loadFileInEditor()">📂 Charger</button>
                <button class="btn" onclick="saveFileFromEditor()">💾 Sauvegarder</button>
            </div>
            <div class="settings-group">
                <textarea id="code-editor" style="width: 100%; height: 400px; font-family: 'Courier New', monospace; background: #1e293b; color: #e2e8f0; border: 1px solid #334155; padding: 10px;" placeholder="Contenu du fichier..."></textarea>
            </div>
        </div>
        
        <div id="terminal" class="view">
            <h2>⚡ Terminal de Commandes</h2>
            <div class="settings-group">
                <p>Commandes rapides :</p>
                <button class="btn" onclick="runQuickCommand('listDir .')">📂 Lister fichiers</button>
                <button class="btn" onclick="runQuickCommand('readFile index.php')">📄 Lire index.php</button>
                <button class="btn btn-secondary" onclick="clearTerminal()">🗑️ Effacer</button>
            </div>
            <div id="terminal-output" style="background: #1e293b; padding: 15px; border-radius: 6px; height: 300px; overflow-y: auto; font-family: 'Courier New', monospace; white-space: pre-wrap;"></div>
            <div class="settings-group" style="margin-top: 10px;">
                <input type="text" id="terminal-input" placeholder="Tapez votre commande..." style="width: 80%; margin-right: 10px;">
                <button class="btn" onclick="runTerminalCommand()">▶️ Exécuter</button>
            </div>
        </div>
        
        <div id="settings" class="view">
            <h2>⚙️ Paramètres</h2>
            <div class="settings-group">
                <label for="app-title">Titre de l'application</label>
                <input type="text" id="app-title" value="SGC-AgentOne" placeholder="Nom de l'application">
            </div>
            <div class="settings-group">
                <label for="theme-mode">Thème</label>
                <select id="theme-mode">
                    <option value="dark">Sombre</option>
                    <option value="light">Clair</option>
                </select>
            </div>
            <div class="settings-group">
                <label for="server-port">Port du serveur</label>
                <input type="number" id="server-port" value="5000" min="1000" max="65535">
            </div>
            <div class="settings-group">
                <label for="debug-mode">Mode Debug</label>
                <select id="debug-mode">
                    <option value="false">Désactivé</option>
                    <option value="true">Activé</option>
                </select>
            </div>
            <div class="settings-group">
                <label for="auto-save">Sauvegarde automatique</label>
                <select id="auto-save">
                    <option value="true">Activée</option>
                    <option value="false">Désactivée</option>
                </select>
            </div>
            <div class="settings-group">
                <button class="btn" onclick="saveSettings()">💾 Enregistrer</button>
                <button class="btn btn-secondary" onclick="resetSettings()">🔄 Réinitialiser</button>
                <button class="btn btn-secondary" onclick="loadSettings()">📂 Charger</button>
            </div>
            <div id="settings-status" style="margin-top: 10px; padding: 10px; border-radius: 6px; display: none;"></div>
        </div>
        
        <div id="help" class="view">
            <h2>❓ Guide d'Aide</h2>
            <div style="background: #1e293b; padding: 20px; border-radius: 8px;">
                <h3>🚀 Commandes Disponibles</h3>
                <ul>
                    <li><strong>createFile nom.ext : contenu</strong> - Créer un fichier</li>
                    <li><strong>readFile nom.ext</strong> - Lire un fichier</li>
                    <li><strong>listDir dossier</strong> - Lister le contenu d'un dossier</li>
                    <li><strong>createDir nom-dossier</strong> - Créer un dossier</li>
                    <li><strong>deleteFile nom.ext</strong> - Supprimer un fichier</li>
                </ul>
                
                <h3>💡 Exemples Pratiques</h3>
                <ul>
                    <li><code>createFile hello.php : &lt;?php echo "Hello World!"; ?&gt;</code></li>
                    <li><code>createFile style.css : body { background: #000; }</code></li>
                    <li><code>listDir .</code> (lister la racine)</li>
                    <li><code>readFile index.php</code></li>
                    <li><code>createDir assets/images</code></li>
                </ul>
                
                <h3>🔧 Fonctionnalités</h3>
                <ul>
                    <li><strong>Chat</strong> : Interface de commandes naturelles</li>
                    <li><strong>Fichiers</strong> : Explorateur de fichiers</li>
                    <li><strong>Éditeur</strong> : Édition de code intégrée</li>
                    <li><strong>Terminal</strong> : Commandes rapides</li>
                    <li><strong>Paramètres</strong> : Configuration personnalisée</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div id="status">
        Prêt • SGC-AgentOne v2.1 • <?php echo date('Y-m-d H:i:s'); ?>
    </div>

    <script>
        // Variables globales
        let currentView = 'chat';
        
        // Navigation entre vues
        document.querySelectorAll(".nav-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                console.log('Clic sur bouton:', btn.dataset.view);
                
                const view = btn.dataset.view;
                
                // Mettre à jour navigation
                document.querySelectorAll(".nav-btn").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                
                // Mettre à jour vues
                document.querySelectorAll(".view").forEach(v => v.classList.remove("active"));
                const targetView = document.getElementById(view);
                if (targetView) {
                    targetView.classList.add("active");
                    currentView = view;
                    console.log('Vue activée:', view);
                } else {
                    console.error('Vue non trouvée:', view);
                }
            });
        });
        
        // Fonctions pour la gestion des fichiers
        async function loadFileList() {
            console.log('Chargement de la liste des fichiers...');
            const fileListDiv = document.getElementById("file-list");
            fileListDiv.innerHTML = '<p class="loading">Chargement des fichiers...</p>';
            
            try {
                const response = await fetch("?action=listFiles");
                const result = await response.json();
                console.log('Réponse API:', result);
                
                if (result.success && result.files) {
                    let html = '<h3>Fichiers du projet :</h3>';
                    result.files.forEach(file => {
                        const icon = file.type === 'dir' ? '📁' : '📄';
                        html += `<div class="file-item" onclick="selectFile('${file.name}')">${icon} ${file.name}</div>`;
                    });
                    fileListDiv.innerHTML = html;
                } else {
                    fileListDiv.innerHTML = '<p class="error">Erreur lors du chargement des fichiers</p>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                fileListDiv.innerHTML = '<p class="error">Erreur de connexion</p>';
            }
        }
        
        function createNewFile() {
            const filename = prompt("Nom du nouveau fichier :");
            if (filename) {
                addMessage(`createFile ${filename} : // Nouveau fichier`, "user");
                sendMessage();
            }
        }
        
        function selectFile(filename) {
            addMessage(`readFile ${filename}`, "user");
            sendMessage();
        }
        
        // Fonctions pour les paramètres
        async function saveSettings() {
            const title = document.getElementById("app-title").value;
            const theme = document.getElementById("theme-mode").value;
            const port = document.getElementById("server-port").value;
            const debug = document.getElementById("debug-mode").value;
            const autoSave = document.getElementById("auto-save").value;
            
            const settings = {
                title: title,
                theme: theme,
                port: parseInt(port),
                debug: debug === 'true',
                autoSave: autoSave === 'true'
            };
            
            try {
                const response = await fetch("?action=saveSettings", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(settings)
                });
                
                const result = await response.json();
                const statusDiv = document.getElementById("settings-status");
                
                if (result.success) {
                    statusDiv.className = "success";
                    statusDiv.textContent = "✅ " + result.message;
                    statusDiv.style.display = "block";
                    
                    // Appliquer le titre
                    document.querySelector("#header h1").textContent = title;
                    
                    // Sauvegarder aussi dans localStorage
                    localStorage.setItem("sgc-settings", JSON.stringify(settings));
                } else {
                    statusDiv.className = "error";
                    statusDiv.textContent = "❌ Erreur: " + result.error;
                    statusDiv.style.display = "block";
                }
                
                setTimeout(() => {
                    statusDiv.style.display = "none";
                }, 3000);
                
            } catch (error) {
                alert("Erreur de connexion: " + error.message);
            }
        }
        
        async function loadSettings() {
            try {
                const response = await fetch("?action=loadSettings");
                const result = await response.json();
                
                if (result.success) {
                    const settings = result.settings;
                    document.getElementById("app-title").value = settings.title || "SGC-AgentOne";
                    document.getElementById("theme-mode").value = settings.theme || "dark";
                    document.getElementById("server-port").value = settings.port || 5000;
                    document.getElementById("debug-mode").value = settings.debug ? 'true' : 'false';
                    document.getElementById("auto-save").value = settings.autoSave !== false ? 'true' : 'false';
                    
                    document.querySelector("#header h1").textContent = settings.title || "SGC-AgentOne";
                    
                    const statusDiv = document.getElementById("settings-status");
                    statusDiv.className = "success";
                    statusDiv.textContent = "✅ Paramètres chargés";
                    statusDiv.style.display = "block";
                    
                    setTimeout(() => {
                        statusDiv.style.display = "none";
                    }, 2000);
                }
            } catch (error) {
                alert("Erreur de chargement: " + error.message);
            }
        }
        
        function resetSettings() {
            if (confirm("Réinitialiser tous les paramètres ?")) {
                document.getElementById("app-title").value = "SGC-AgentOne";
                document.getElementById("theme-mode").value = "dark";
                document.getElementById("server-port").value = "5000";
                document.getElementById("debug-mode").value = "false";
                document.getElementById("auto-save").value = "true";
                
                localStorage.removeItem("sgc-settings");
                document.querySelector("#header h1").textContent = "SGC-AgentOne";
                
                const statusDiv = document.getElementById("settings-status");
                statusDiv.className = "success";
                statusDiv.textContent = "✅ Paramètres réinitialisés";
                statusDiv.style.display = "block";
                
                setTimeout(() => {
                    statusDiv.style.display = "none";
                }, 2000);
            }
        }
        
        // Fonctions pour l'éditeur
        async function loadFileInEditor() {
            const filename = document.getElementById("editor-file").value.trim();
            if (!filename) {
                alert("Veuillez entrer un nom de fichier");
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
                } else {
                    alert("Erreur: " + result.error);
                }
            } catch (error) {
                alert("Erreur de connexion: " + error.message);
            }
        }
        
        async function saveFileFromEditor() {
            const filename = document.getElementById("editor-file").value.trim();
            const content = document.getElementById("code-editor").value;
            
            if (!filename) {
                alert("Veuillez entrer un nom de fichier");
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
                    alert("✅ Fichier sauvegardé: " + filename);
                } else {
                    alert("Erreur: " + result.error);
                }
            } catch (error) {
                alert("Erreur de connexion: " + error.message);
            }
        }
        
        // Fonctions pour le terminal
        async function runQuickCommand(command) {
            document.getElementById("terminal-input").value = command;
            await runTerminalCommand();
        }
        
        async function runTerminalCommand() {
            const command = document.getElementById("terminal-input").value.trim();
            if (!command) return;
            
            const output = document.getElementById("terminal-output");
            output.textContent += `> ${command}\n`;
            
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
                output.textContent += "❌ Erreur de connexion: " + error.message + "\n\n";
            }
            
            output.scrollTop = output.scrollHeight;
            document.getElementById("terminal-input").value = "";
        }
        
        function clearTerminal() {
            document.getElementById("terminal-output").textContent = "";
        }
        
        // Charger les paramètres sauvegardés
        
        // === CHAT FUNCTIONALITY ===
        const messagesContainer = document.getElementById('messages');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-btn');
        
        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            messageDiv.innerHTML = `<strong>${sender === 'user' ? 'Vous' : 'SGC-AgentOne'}:</strong> ${text}`;
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        async function sendMessage() {
            const text = messageInput.value.trim();
            if (!text) return;
            
            addMessage(text, 'user');
            messageInput.value = '';
            
            sendButton.disabled = true;
            sendButton.textContent = 'Envoi...';
            
            try {
                const response = await fetch('?action=chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                
                const result = await response.json();
                
                if (result.error) {
                    addMessage(result.error, 'ai');
                } else if (result.success && result.response) {
                    addMessage(result.response, 'ai');
                } else {
                    addMessage("Réponse inattendue.", 'ai');
                }
            } catch (error) {
                addMessage("Erreur de connexion au serveur.", 'ai');
            } finally {
                sendButton.disabled = false;
                sendButton.textContent = 'Envoyer';
            }
        }
        
        // Événements chat
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', e => {
            if (e.key === 'Enter') sendMessage();
        });
        
        // Événements terminal
        document.getElementById('terminal-input').addEventListener('keypress', e => {
            if (e.key === 'Enter') runTerminalCommand();
        });
        
        // Charger les paramètres au démarrage
        loadSettings();
        
        console.log('SGC-AgentOne initialisé avec succès');
    </script>
</body>
</html>
    <?php
    
} catch (Exception $e) {
    // Page d'erreur simple
    http_response_code(500);
    echo "<!DOCTYPE html><html><head><title>❌ Erreur SGC-AgentOne</title>";
    echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#0a0f1c;color:#e2e8f0;}</style></head><body>";
    echo "<h1>❌ Erreur SGC-AgentOne</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Vérifiez les permissions du dossier</li>";
    echo "<li>Essayez le mode debug: <a href='?debug=1' style='color:#38bdf8;'>?debug=1</a></li>";
    echo "<li>Contactez le support technique</li>";
    echo "</ul>";
    echo "<p><strong>Informations système:</strong></p>";
    echo "<pre>PHP: " . PHP_VERSION . "\nRacine: " . htmlspecialchars($projectRoot) . "\nHeure: " . date('Y-m-d H:i:s') . "</pre>";
    echo "</body></html>";
}
?>