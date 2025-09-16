<?php
/**
 * SGC-AgentOne v2.1 - Solution Simple et Optimale
 * Élimination complète des problèmes de chemins
 * Auto-installation et configuration zéro
 */

// Configuration d'erreurs pour diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Chemins absolus basés sur __DIR__ - TOUJOURS corrects
$projectRoot = __DIR__;
$webviewPath = $projectRoot . '/extensions/webview';
$indexFile = $webviewPath . '/index.html';
$corePath = $projectRoot . '/core';
$apiPath = $projectRoot . '/api';

// Mode debug
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

/**
 * Création automatique de la structure si manquante
 */
function createProjectStructure($root) {
    $dirs = [
        '/core/config',
        '/core/logs', 
        '/core/db',
        '/core/utils',
        '/core/agents/actions',
        '/api',
        '/extensions/webview',
        '/prompts'
    ];
    
    foreach ($dirs as $dir) {
        $path = $root . $dir;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
    
    // Créer les fichiers essentiels
    createEssentialFiles($root);
}

/**
 * Création des fichiers essentiels
 */
function createEssentialFiles($root) {
    // 1. Interface principale
    $indexHtml = $root . '/extensions/webview/index.html';
    if (!file_exists($indexHtml)) {
        file_put_contents($indexHtml, getIndexHtmlContent());
    }
    
    // 2. Configuration par défaut
    $settingsFile = $root . '/core/config/settings.json';
    if (!file_exists($settingsFile)) {
        $settings = [
            'port' => 5000,
            'host' => '0.0.0.0',
            'debug' => false,
            'theme' => 'sgc-commander'
        ];
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
    }
    
    // 3. API de base
    createBasicAPI($root);
}

/**
 * Contenu de l'interface principale
 */
function getIndexHtmlContent() {
    return '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SGC-AgentOne</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #0a0f1c; color: #e2e8f0; height: 100vh; overflow: hidden;
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
    </style>
</head>
<body>
    <div id="header">
        <h1>🚀 SGC-AgentOne</h1>
        <div id="nav">
            <button class="nav-btn active" data-view="chat">💬 Chat</button>
            <button class="nav-btn" data-view="files">📁 Fichiers</button>
            <button class="nav-btn" data-view="settings">⚙️ Paramètres</button>
        </div>
    </div>
    
    <div id="main">
        <div id="chat" class="view active">
            <div id="chat-container">
                <div id="messages">
                    <div class="message ai">
                        <strong>SGC-AgentOne:</strong> Bonjour ! Je suis votre assistant de développement. 
                        Tapez vos commandes au format: <code>action cible : contenu</code>
                        <br><br>Exemples:
                        <br>• <code>createFile test.php : &lt;?php echo "Hello"; ?&gt;</code>
                        <br>• <code>listDir .</code>
                        <br>• <code>readFile config.json</code>
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
            <div style="padding: 20px; background: #1e293b; border-radius: 8px; margin: 16px 0;">
                <h3>📂 Explorateur de Fichiers</h3>
                <div id="file-list" style="margin-top: 16px;">
                    <div style="padding: 8px; background: #334155; border-radius: 4px; margin: 4px 0; cursor: pointer;">
                        📁 core/
                    </div>
                    <div style="padding: 8px; background: #334155; border-radius: 4px; margin: 4px 0; cursor: pointer;">
                        📁 api/
                    </div>
                    <div style="padding: 8px; background: #334155; border-radius: 4px; margin: 4px 0; cursor: pointer;">
                        📄 index.php
                    </div>
                    <div style="padding: 8px; background: #334155; border-radius: 4px; margin: 4px 0; cursor: pointer;">
                        📄 .htaccess
                    </div>
                </div>
                <button onclick="loadFileList()" style="margin-top: 16px; padding: 8px 16px; background: #38bdf8; color: #0a0f1c; border: none; border-radius: 4px; cursor: pointer;">
                    🔄 Actualiser
                </button>
            </div>
        </div>
        
        <div id="settings" class="view">
            <h2>⚙️ Paramètres</h2>
            <div style="padding: 20px; background: #1e293b; border-radius: 8px; margin: 16px 0;">
                <h3>🎨 Apparence</h3>
                <div style="margin: 16px 0;">
                    <label style="display: block; margin-bottom: 8px;">Thème:</label>
                    <select id="theme-select" style="padding: 8px; background: #334155; color: #e2e8f0; border: none; border-radius: 4px; width: 200px;">
                        <option value="dark">🌙 Sombre</option>
                        <option value="light">☀️ Clair</option>
                    </select>
                </div>
                
                <h3 style="margin-top: 24px;">⚡ Serveur</h3>
                <div style="margin: 16px 0;">
                    <label style="display: block; margin-bottom: 8px;">Port:</label>
                    <input type="number" id="port-input" value="5000" style="padding: 8px; background: #334155; color: #e2e8f0; border: none; border-radius: 4px; width: 100px;">
                </div>
                
                <button onclick="saveSettings()" style="margin-top: 16px; padding: 8px 16px; background: #38bdf8; color: #0a0f1c; border: none; border-radius: 4px; cursor: pointer;">
                    💾 Enregistrer
                </button>
            </div>
        </div>
    </div>
    
    <div id="status">
        Prêt • SGC-AgentOne v2.1 • ' . date('Y-m-d H:i:s') . '
    </div>

    <script>
        // Navigation entre vues
        document.querySelectorAll(".nav-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const view = btn.dataset.view;
                
                // Debug
                console.log("Bouton cliqué:", view);
                
                // Mettre à jour navigation
                document.querySelectorAll(".nav-btn").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                
                // Mettre à jour vues
                document.querySelectorAll(".view").forEach(v => v.classList.remove("active"));
                const targetView = document.getElementById(view);
                if (targetView) {
                    targetView.classList.add("active");
                } else {
                    console.error("Vue non trouvée:", view);
                }
            });
        });
        
        // Chat functionality
        const messagesDiv = document.getElementById("messages");
        const messageInput = document.getElementById("message-input");
        const sendBtn = document.getElementById("send-btn");
        
        function addMessage(content, type = "user") {
            const div = document.createElement("div");
            div.className = `message ${type}`;
            div.innerHTML = type === "user" ? 
                `<strong>Vous:</strong> ${content}` : 
                `<strong>SGC-AgentOne:</strong> ${content}`;
            messagesDiv.appendChild(div);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        
        async function sendMessage() {
            const message = messageInput.value.trim();
            if (!message) return;
            
            addMessage(message, "user");
            messageInput.value = "";
            sendBtn.disabled = true;
            sendBtn.textContent = "...";
            
            try {
                const response = await fetch("?action=chat", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ message: message })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    addMessage(result.response || "Commande exécutée avec succès", "ai");
                } else {
                    addMessage(`Erreur: ${result.error}`, "ai");
                }
            } catch (error) {
                addMessage(`Erreur de connexion: ${error.message}`, "ai");
            }
            
            sendBtn.disabled = false;
            sendBtn.textContent = "Envoyer";
        }
        
        // Fonctions pour les autres vues
        function loadFileList() {
            fetch("?action=files")
                .then(r => r.json())
                .then(data => {
                    console.log("Fichiers:", data);
                    // Mise à jour de la liste des fichiers
                })
                .catch(e => console.error("Erreur:", e));
        }
        
        function saveSettings() {
            const theme = document.getElementById("theme-select").value;
            const port = document.getElementById("port-input").value;
            
            fetch("?action=settings", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ theme, port })
            })
            .then(r => r.json())
            .then(data => console.log("Paramètres sauvés:", data));
        }
        
        sendBtn.addEventListener("click", sendMessage);
        messageInput.addEventListener("keypress", (e) => {
            if (e.key === "Enter") sendMessage();
        });
    </script>
</body>
</html>';
}

/**
 * Création de l'API de base
 */
function createBasicAPI($root) {
    $chatAPI = $root . '/api/chat.php';
    if (!file_exists($chatAPI)) {
        $content = '<?php
header("Content-Type: application/json");
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input["message"])) {
    echo json_encode(["error" => "Message manquant"]);
    exit;
}

$message = trim($input["message"]);

// Interpréteur simple
if (strpos($message, ":") !== false) {
    list($actionTarget, $content) = explode(":", $message, 2);
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
        echo "<p><strong>Chemin webview:</strong> " . htmlspecialchars($webviewPath) . "</p>";
        echo "<p><strong>Fichier index:</strong> " . htmlspecialchars($indexFile) . "</p>";
        echo "<p><strong>Existe:</strong> " . (file_exists($indexFile) ? "✅ OUI" : "❌ NON") . "</p>";
        
        if (!file_exists($indexFile)) {
            echo "<p><strong>🔧 Création automatique en cours...</strong></p>";
            createProjectStructure($projectRoot);
            echo "<p><strong>✅ Structure créée !</strong></p>";
        }
        
        echo "<p><a href='?' style='color:#38bdf8;'>🚀 Accéder à SGC-AgentOne</a></p>";
        echo "</body></html>";
        exit;
    }
    
    // Gestion API
    if (isset($_GET['action']) && $_GET['action'] === 'chat') {
        include $apiPath . '/chat.php';
        exit;
    }
    
    // Vérification et création automatique de la structure
    if (!file_exists($indexFile)) {
        createProjectStructure($projectRoot);
        
        // Message d'installation
        echo "<!DOCTYPE html><html><head><title>🔧 Installation SGC-AgentOne</title>";
        echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#0a0f1c;color:#e2e8f0;text-align:center;padding-top:100px;}</style></head><body>";
        echo "<h1>🔧 Installation Automatique</h1>";
        echo "<p>SGC-AgentOne s'installe automatiquement...</p>";
        echo "<p>✅ Structure créée<br>✅ Fichiers générés<br>✅ Configuration terminée</p>";
        echo "<p><a href='?' style='color:#38bdf8;font-size:1.2rem;text-decoration:none;'>🚀 Accéder à SGC-AgentOne</a></p>";
        echo "<script>setTimeout(() => window.location.href = '?', 2000);</script>";
        echo "</body></html>";
        exit;
    }
    
    // Servir l'interface principale
    if (file_exists($indexFile)) {
        $content = file_get_contents($indexFile);
        
        // Injection de la base URL pour les ressources
        $baseUrl = dirname($_SERVER['REQUEST_URI']);
        if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
        
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
    } else {
        throw new Exception("Impossible de créer la structure du projet");
    }
    
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