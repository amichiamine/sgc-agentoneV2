m        }
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
.            position: fixed; bottom: 0; left: 0; right: 0; background: #1e293b;
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
            <div class="settings-group">
                <button class="btn" onclick="loadFileList()">🔄 Actualiser la liste</button>
                <button class="btn btn-secondary" onclick="createNewFile()">➕ Nouveau fichier</button>
            </div>
            <div id="file-list" class="file-list">
                <p>Cliquez sur "Actualiser" pour voir les fichiers...</p>
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
                <button class="btn" onclick="saveSettings()">💾 Enregistrer</button>
                <button class="btn btn-secondary" onclick="resetSettings()">🔄 Réinitialiser</button>
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
                
                // Mettre à jour navigation
                document.querySelectorAll(".nav-btn").forEach(b => b.classList.remove("active"));
                btn.classList.add("active");
                
                // Mettre à jour vues
                document.querySelectorAll(".view").forEach(v => v.classList.remove("active"));
                document.getElementById(view).classList.add("active");
            });
            
            // Fonctions pour la gestion des fichiers
            window.loadFileList = async function() {
                const fileListDiv = document.getElementById("file-list");
                fileListDiv.innerHTML = '<p class="loading">Chargement des fichiers...</p>';
                
                try {
                    const response = await fetch("?action=listFiles");
                    const result = await response.json();
                    
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
                    fileListDiv.innerHTML = '<p class="error">Erreur de connexion</p>';
                }
            };
            
            window.createNewFile = function() {
                const filename = prompt("Nom du nouveau fichier :");
                if (filename) {
                    addMessage(`createFile ${filename} : // Nouveau fichier`, "user");
                    sendMessage();
                }
            };
            
            window.selectFile = function(filename) {
                addMessage(`readFile ${filename}`, "user");
                sendMessage();
            };
            
            // Fonctions pour les paramètres
            window.saveSettings = function() {
                const title = document.getElementById("app-title").value;
                const theme = document.getElementById("theme-mode").value;
                const port = document.getElementById("server-port").value;
                
                // Sauvegarder dans localStorage
                localStorage.setItem("sgc-settings", JSON.stringify({
                    title: title,
                    theme: theme,
                    port: port
                }));
                
                alert("Paramètres sauvegardés !");
                
                // Appliquer le titre
                document.querySelector("#header h1").textContent = title;
            };
            
            window.resetSettings = function() {
                if (confirm("Réinitialiser tous les paramètres ?")) {
                    localStorage.removeItem("sgc-settings");
                    document.getElementById("app-title").value = "SGC-AgentOne";
                    document.getElementById("theme-mode").value = "dark";
                    document.getElementById("server-port").value = "5000";
                    alert("Paramètres réinitialisés !");
                }
            };
            
            // Charger les paramètres sauvegardés
            const savedSettings = localStorage.getItem("sgc-settings");
            if (savedSettings) {
                const settings = JSON.parse(savedSettings);
                document.getElementById("app-title").value = settings.title || "SGC-AgentOne";
                document.getElementById("theme-mode").value = settings.theme || "dark";
                document.getElementById("server-port").value = settings.port || "5000";
                document.querySelector("#header h1").textContent = settings.title || "SGC-AgentOne";
            }
        });
        
    $parts = explode(" ", trim($actionTarget), 2);
    $action = $parts[0];
    // Fonctions pour la gestion des fichiers
    window.loadFileList = async function() {
        const fileListDiv = document.getElementById("file-list");
        fileListDiv.innerHTML = '<p class="loading">Chargement des fichiers...</p>';
        
        try {
            const response = await fetch("?action=listFiles");
            const result = await response.json();
            
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
            fileListDiv.innerHTML = '<p class="error">Erreur de connexion</p>';
        }
    };
    
    window.createNewFile = function() {
        const filename = prompt("Nom du nouveau fichier :");
        if (filename) {
            addMessage(`createFile ${filename} : // Nouveau fichier`, "user");
            sendMessage();
        }
    };
    
    window.selectFile = function(filename) {
        addMessage(`readFile ${filename}`, "user");
        sendMessage();
    };
    
    // Fonctions pour les paramètres
    window.saveSettings = function() {
        const title = document.getElementById("app-title").value;
        const theme = document.getElementById("theme-mode").value;
        const port = document.getElementById("server-port").value;
        
        // Sauvegarder dans localStorage
        localStorage.setItem("sgc-settings", JSON.stringify({
            title: title,
            theme: theme,
            port: port
        }));
        
        alert("Paramètres sauvegardés !");
        
        // Appliquer le titre
        document.querySelector("#header h1").textContent = title;
    };
    
    window.resetSettings = function() {
        if (confirm("Réinitialiser tous les paramètres ?")) {
            localStorage.removeItem("sgc-settings");
            document.getElementById("app-title").value = "SGC-AgentOne";
            document.getElementById("theme-mode").value = "dark";
            document.getElementById("server-port").value = "5000";
            alert("Paramètres réinitialisés !");
        }
    };
    
    // Charger les paramètres sauvegardés
    const savedSettings = localStorage.getItem("sgc-settings");
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        document.getElementById("app-title").value = settings.title || "SGC-AgentOne";
        document.getElementById("theme-mode").value = settings.theme || "dark";
        document.getElementById("server-port").value = settings.port || "5000";
        document.querySelector("#header h1").textContent = settings.title || "SGC-AgentOne";
    }
    
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
            case 'listFiles':
                header('Content-Type: application/json');
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