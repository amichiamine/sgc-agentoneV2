<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGC-AgentOne v2.1</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --bg-primary: #0a0f1c;
            --bg-secondary: #1a2332;
            --bg-tertiary: #2a3441;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --accent: #00d4aa;
            --accent-hover: #00b894;
            --border: #334155;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            height: 100vh;
            overflow: hidden;
        }
        
        /* Header */
        .header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--accent);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bg-primary);
            font-weight: bold;
        }
        
        .nav-tabs {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
        }
        
        .nav-tab {
            padding: 0.5rem 1rem;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        .nav-tab:hover {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }
        
        .nav-tab.active {
            background: var(--accent);
            color: var(--bg-primary);
        }
        
        .status-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
        }
        
        /* Main Content */
        .main-content {
            height: calc(100vh - 60px);
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
        
        /* Chat View */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .message {
            max-width: 80%;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            word-wrap: break-word;
        }
        
        .message.user {
            align-self: flex-end;
            background: var(--accent);
            color: var(--bg-primary);
        }
        
        .message.assistant {
            align-self: flex-start;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
        }
        
        .chat-input-container {
            padding: 1rem;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border);
            display: flex;
            gap: 0.5rem;
        }
        
        .chat-input {
            flex: 1;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            outline: none;
        }
        
        .chat-send {
            padding: 0.75rem 1.5rem;
            background: var(--accent);
            color: var(--bg-primary);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }
        
        /* Files View */
        .files-container {
            display: flex;
            height: 100%;
        }
        
        .files-sidebar {
            width: 300px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }
        
        .files-toolbar {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .files-tree {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem;
        }
        
        .tree-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            cursor: pointer;
            border-radius: 4px;
            margin: 1px 0;
        }
        
        .tree-item:hover {
            background: var(--bg-tertiary);
        }
        
        .tree-item.selected {
            background: var(--accent);
            color: var(--bg-primary);
        }
        
        .files-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .files-header {
            padding: 1rem;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .files-main {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        
        /* Editor View */
        .editor-container {
            display: flex;
            height: 100%;
        }
        
        .editor-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }
        
        .editor-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .editor-toolbar {
            padding: 0.75rem 1rem;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .editor-textarea {
            flex: 1;
            background: var(--bg-primary);
            color: var(--text-primary);
            border: none;
            padding: 1rem;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
            line-height: 1.5;
            resize: none;
            outline: none;
        }
        
        /* Terminal View */
        .terminal-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #000;
            color: #00ff00;
            font-family: 'Consolas', 'Monaco', monospace;
        }
        
        .terminal-header {
            padding: 0.5rem 1rem;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .terminal-output {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .terminal-input-container {
            padding: 0.5rem 1rem;
            background: #111;
            border-top: 1px solid #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .terminal-prompt {
            color: #00ff00;
            font-weight: bold;
        }
        
        .terminal-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #00ff00;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }
        
        /* Server View */
        .server-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 1rem;
            gap: 1rem;
        }
        
        .server-status {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .server-controls {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .server-logs {
            flex: 1;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .logs-content {
            flex: 1;
            overflow-y: auto;
            background: #000;
            color: #fff;
            padding: 1rem;
            border-radius: 4px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 12px;
            line-height: 1.4;
        }
        
        /* Database View */
        .database-container {
            display: flex;
            height: 100%;
        }
        
        .database-sidebar {
            width: 300px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }
        
        .database-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .sql-editor {
            height: 200px;
            background: var(--bg-primary);
            color: var(--text-primary);
            border: none;
            padding: 1rem;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
            resize: none;
            outline: none;
        }
        
        .sql-results {
            flex: 1;
            overflow: auto;
            padding: 1rem;
            background: var(--bg-tertiary);
        }
        
        /* Browser View */
        .browser-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .browser-toolbar {
            padding: 0.75rem 1rem;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .browser-url {
            flex: 1;
            padding: 0.5rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 4px;
            color: var(--text-primary);
            outline: none;
        }
        
        .browser-frame {
            flex: 1;
            border: none;
            background: white;
        }
        
        /* Projects View */
        .projects-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 1rem;
            gap: 1rem;
        }
        
        .projects-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .projects-grid {
            flex: 1;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }
        
        .project-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .project-card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }
        
        /* Prompts View */
        .prompts-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 1rem;
            gap: 1rem;
        }
        
        .prompts-list {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .prompt-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .prompt-card:hover {
            border-color: var(--accent);
        }
        
        /* Settings View */
        .settings-container {
            display: flex;
            height: 100%;
        }
        
        .settings-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            padding: 1rem;
        }
        
        .settings-main {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }
        
        .settings-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 4px;
            color: var(--text-primary);
            outline: none;
        }
        
        .form-input:focus {
            border-color: var(--accent);
        }
        
        /* Help View */
        .help-container {
            display: flex;
            height: 100%;
        }
        
        .help-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            padding: 1rem;
        }
        
        .help-main {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }
        
        .help-section {
            margin-bottom: 2rem;
        }
        
        .help-section h2 {
            color: var(--accent);
            margin-bottom: 1rem;
        }
        
        .help-section h3 {
            margin: 1.5rem 0 0.5rem 0;
        }
        
        .help-section p {
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .help-section code {
            background: var(--bg-tertiary);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Consolas', 'Monaco', monospace;
        }
        
        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            background: var(--accent);
            color: var(--bg-primary);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn:hover {
            background: var(--accent-hover);
        }
        
        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--border);
        }
        
        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-tabs {
                overflow-x: auto;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            
            .nav-tabs::-webkit-scrollbar {
                display: none;
            }
            
            .files-container,
            .editor-container,
            .database-container,
            .settings-container,
            .help-container {
                flex-direction: column;
            }
            
            .files-sidebar,
            .editor-sidebar,
            .database-sidebar,
            .settings-sidebar,
            .help-sidebar {
                width: 100%;
                height: 200px;
            }
            
            .projects-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-icon">S</div>
            <div>
                <div>SGC-AgentOne</div>
                <div style="font-size: 0.7rem; color: var(--text-secondary);">By AMICHI Amine</div>
            </div>
        </div>
        
        <div class="nav-tabs">
            <button class="nav-tab active" data-view="chat">💬 Chat</button>
            <button class="nav-tab" data-view="files">📁 Fichiers</button>
            <button class="nav-tab" data-view="editor">📝 Éditeur</button>
            <button class="nav-tab" data-view="terminal">⚡ Terminal</button>
            <button class="nav-tab" data-view="server">🖥️ Serveur</button>
            <button class="nav-tab" data-view="database">🗄️ Base</button>
            <button class="nav-tab" data-view="browser">🌐 Navigateur</button>
            <button class="nav-tab" data-view="projects">📂 Projets</button>
            <button class="nav-tab" data-view="prompts">📝 Prompts</button>
            <button class="nav-tab" data-view="settings">⚙️ Config</button>
            <button class="nav-tab" data-view="help">❓ Aide</button>
        </div>
        
        <div class="status-bar">
            <div class="status-indicator">
                <div class="status-dot"></div>
                <span>Connecté</span>
            </div>
            <span id="current-time"></span>
        </div>
    </div>
    
    <div class="main-content">
        <!-- Chat View -->
        <div class="view active" id="chat-view">
            <div class="chat-container">
                <div class="chat-messages" id="chat-messages">
                    <div class="message assistant">
                        <strong>SGC-AgentOne:</strong> Bonjour ! Je suis votre assistant universel. Tapez vos commandes ci-dessous.
                        <br><br>
                        <strong>Commandes disponibles:</strong><br>
                        • <code>createFile nom.txt : contenu</code><br>
                        • <code>readFile nom.txt</code><br>
                        • <code>listDir dossier</code><br>
                        • <code>createDir nouveau_dossier</code><br>
                        • <code>deleteFile nom.txt</code>
                    </div>
                </div>
                <div class="chat-input-container">
                    <input type="text" class="chat-input" id="chat-input" placeholder="Tapez votre commande...">
                    <button class="chat-send" id="chat-send">Envoyer</button>
                </div>
            </div>
        </div>
        
        <!-- Files View -->
        <div class="view" id="files-view">
            <div class="files-container">
                <div class="files-sidebar">
                    <div class="files-toolbar">
                        <button class="btn btn-small" id="new-file">📄 Nouveau</button>
                        <button class="btn btn-small" id="new-folder">📁 Dossier</button>
                        <button class="btn btn-small" id="upload-file">⬆️ Upload</button>
                        <button class="btn btn-small" id="refresh-files">🔄 Actualiser</button>
                    </div>
                    <div class="files-tree" id="files-tree">
                        <div class="tree-item">📁 Racine du projet</div>
                        <div class="tree-item" style="padding-left: 1rem;">📄 index.php</div>
                        <div class="tree-item" style="padding-left: 1rem;">📄 README.md</div>
                        <div class="tree-item" style="padding-left: 1rem;">📁 core</div>
                        <div class="tree-item" style="padding-left: 2rem;">📁 agents</div>
                        <div class="tree-item" style="padding-left: 2rem;">📁 config</div>
                    </div>
                </div>
                <div class="files-content">
                    <div class="files-header">
                        <h3>Explorateur de Fichiers</h3>
                        <div>
                            <button class="btn btn-small">📋 Copier</button>
                            <button class="btn btn-small">✂️ Couper</button>
                            <button class="btn btn-small">📋 Coller</button>
                            <button class="btn btn-small">🗑️ Supprimer</button>
                        </div>
                    </div>
                    <div class="files-main">
                        <p>Sélectionnez un fichier ou dossier dans l'arborescence pour voir son contenu.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Editor View -->
        <div class="view" id="editor-view">
            <div class="editor-container">
                <div class="editor-sidebar">
                    <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <h4>Fichiers Ouverts</h4>
                    </div>
                    <div style="flex: 1; padding: 0.5rem;">
                        <div class="tree-item selected">📄 index.php</div>
                        <div class="tree-item">📄 README.md</div>
                    </div>
                </div>
                <div class="editor-main">
                    <div class="editor-toolbar">
                        <div>
                            <strong>index.php</strong>
                            <span style="color: var(--text-secondary); margin-left: 1rem;">PHP</span>
                        </div>
                        <div>
                            <button class="btn btn-small">💾 Sauvegarder</button>
                            <button class="btn btn-small">🔍 Rechercher</button>
                            <button class="btn btn-small">🔄 Formater</button>
                        </div>
                    </div>
                    <textarea class="editor-textarea" id="editor-content" placeholder="Ouvrez un fichier pour commencer l'édition..."><?php
// Contenu du fichier index.php
echo "Hello World!";
?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Terminal View -->
        <div class="view" id="terminal-view">
            <div class="terminal-container">
                <div class="terminal-header">
                    <h4>Terminal SGC-AgentOne</h4>
                    <div>
                        <button class="btn btn-small">🗑️ Effacer</button>
                        <button class="btn btn-small">📋 Historique</button>
                    </div>
                </div>
                <div class="terminal-output" id="terminal-output">
                    <div>SGC-AgentOne Terminal v2.1</div>
                    <div>Tapez 'help' pour voir les commandes disponibles.</div>
                    <div style="margin-top: 1rem;">
                        <span style="color: #00ff00;">sgc@localhost:~$</span> help
                    </div>
                    <div style="margin-left: 1rem; color: #ffff00;">
                        Commandes disponibles:<br>
                        • ls - Lister les fichiers<br>
                        • cat [fichier] - Afficher le contenu<br>
                        • mkdir [nom] - Créer un dossier<br>
                        • touch [nom] - Créer un fichier<br>
                        • rm [fichier] - Supprimer<br>
                        • clear - Effacer l'écran<br>
                        • status - Statut du serveur
                    </div>
                </div>
                <div class="terminal-input-container">
                    <span class="terminal-prompt">sgc@localhost:~$</span>
                    <input type="text" class="terminal-input" id="terminal-input" placeholder="Tapez votre commande...">
                </div>
            </div>
        </div>
        
        <!-- Server View -->
        <div class="view" id="server-view">
            <div class="server-container">
                <div class="server-status">
                    <h3>Statut du Serveur</h3>
                    <div style="display: flex; gap: 2rem; margin-top: 1rem;">
                        <div>
                            <div style="color: var(--text-secondary);">État</div>
                            <div style="color: var(--success); font-weight: bold;">🟢 En ligne</div>
                        </div>
                        <div>
                            <div style="color: var(--text-secondary);">Port</div>
                            <div>5000</div>
                        </div>
                        <div>
                            <div style="color: var(--text-secondary);">Uptime</div>
                            <div>2h 34m</div>
                        </div>
                        <div>
                            <div style="color: var(--text-secondary);">Mémoire</div>
                            <div>45.2 MB</div>
                        </div>
                    </div>
                </div>
                
                <div class="server-controls">
                    <button class="btn">🚀 Démarrer</button>
                    <button class="btn">⏹️ Arrêter</button>
                    <button class="btn">🔄 Redémarrer</button>
                    <button class="btn btn-secondary">📊 Monitoring</button>
                    <button class="btn btn-secondary">⚙️ Configuration</button>
                </div>
                
                <div class="server-logs">
                    <h4 style="margin-bottom: 1rem;">Logs du Serveur</h4>
                    <div class="logs-content" id="server-logs">
                        [2024-01-15 14:30:25] INFO: Serveur démarré sur le port 5000<br>
                        [2024-01-15 14:30:26] INFO: Interface SGC-AgentOne chargée<br>
                        [2024-01-15 14:32:15] INFO: Connexion client depuis 127.0.0.1<br>
                        [2024-01-15 14:35:42] INFO: Commande exécutée: listDir<br>
                        [2024-01-15 14:36:18] INFO: Fichier créé: test.txt<br>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Database View -->
        <div class="view" id="database-view">
            <div class="database-container">
                <div class="database-sidebar">
                    <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <h4>Base de Données</h4>
                        <button class="btn btn-small" style="margin-top: 0.5rem; width: 100%;">🔗 Connecter</button>
                    </div>
                    <div style="flex: 1; padding: 0.5rem;">
                        <div style="margin-bottom: 1rem;">
                            <strong>Tables</strong>
                        </div>
                        <div class="tree-item">📊 users</div>
                        <div class="tree-item">📊 projects</div>
                        <div class="tree-item">📊 settings</div>
                        <div class="tree-item">📊 logs</div>
                    </div>
                </div>
                <div class="database-main">
                    <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <h3>Éditeur SQL</h3>
                        <div style="margin-top: 0.5rem;">
                            <button class="btn btn-small">▶️ Exécuter</button>
                            <button class="btn btn-small">💾 Sauvegarder</button>
                            <button class="btn btn-small">📋 Formater</button>
                            <button class="btn btn-small">📊 Expliquer</button>
                        </div>
                    </div>
                    <textarea class="sql-editor" placeholder="-- Tapez votre requête SQL ici
SELECT * FROM users WHERE active = 1;"></textarea>
                    <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <strong>Résultats</strong>
                    </div>
                    <div class="sql-results">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: var(--bg-secondary);">
                                    <th style="padding: 0.5rem; border: 1px solid var(--border);">ID</th>
                                    <th style="padding: 0.5rem; border: 1px solid var(--border);">Nom</th>
                                    <th style="padding: 0.5rem; border: 1px solid var(--border);">Email</th>
                                    <th style="padding: 0.5rem; border: 1px solid var(--border);">Actif</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 0.5rem; border: 1px solid var(--border);">1</td>
                                    <td style="padding: 0.5rem; border: 1px solid var(--border);">Admin</td>
                                    <td style="padding: 0.5rem; border: 1px solid var(--border);">admin@sgc.local</td>
                                    <td style="padding: 0.5rem; border: 1px solid var(--border);">✅</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Browser View -->
        <div class="view" id="browser-view">
            <div class="browser-container">
                <div class="browser-toolbar">
                    <button class="btn btn-small">◀️</button>
                    <button class="btn btn-small">▶️</button>
                    <button class="btn btn-small">🔄</button>
                    <input type="text" class="browser-url" value="http://localhost:5000" placeholder="Entrez une URL...">
                    <button class="btn btn-small">🔍</button>
                    <button class="btn btn-small">🏠</button>
                </div>
                <iframe class="browser-frame" src="about:blank"></iframe>
            </div>
        </div>
        
        <!-- Projects View -->
        <div class="view" id="projects-view">
            <div class="projects-container">
                <div class="projects-header">
                    <h2>Gestionnaire de Projets</h2>
                    <div>
                        <button class="btn">➕ Nouveau Projet</button>
                        <button class="btn btn-secondary">📂 Ouvrir Projet</button>
                        <button class="btn btn-secondary">📤 Importer</button>
                    </div>
                </div>
                <div class="projects-grid">
                    <div class="project-card">
                        <h4>SGC-AgentOne</h4>
                        <p style="color: var(--text-secondary); margin: 0.5rem 0;">Assistant universel PHP</p>
                        <div style="display: flex; gap: 0.5rem; margin: 1rem 0;">
                            <span style="background: var(--accent); color: var(--bg-primary); padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">PHP</span>
                            <span style="background: var(--warning); color: var(--bg-primary); padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">HTML</span>
                            <span style="background: var(--success); color: var(--bg-primary); padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">CSS</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--text-secondary); font-size: 0.8rem;">Modifié il y a 2h</span>
                            <div>
                                <button class="btn btn-small">📝 Ouvrir</button>
                                <button class="btn btn-small btn-secondary">⚙️</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="project-card">
                        <h4>Site Web Portfolio</h4>
                        <p style="color: var(--text-secondary); margin: 0.5rem 0;">Portfolio personnel responsive</p>
                        <div style="display: flex; gap: 0.5rem; margin: 1rem 0;">
                            <span style="background: var(--warning); color: var(--bg-primary); padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">HTML</span>
                            <span style="background: var(--success); color: var(--bg-primary); padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">CSS</span>
                            <span style="background: var(--error); color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">JS</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--text-secondary); font-size: 0.8rem;">Modifié il y a 1 jour</span>
                            <div>
                                <button class="btn btn-small">📝 Ouvrir</button>
                                <button class="btn btn-small btn-secondary">⚙️</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Prompts View -->
        <div class="view" id="prompts-view">
            <div class="prompts-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2>Gestionnaire de Prompts</h2>
                    <div>
                        <button class="btn">➕ Nouveau Prompt</button>
                        <button class="btn btn-secondary">📤 Importer</button>
                        <button class="btn btn-secondary">📁 Dossier</button>
                    </div>
                </div>
                <div class="prompts-list">
                    <div class="prompt-card">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h4>Créer Structure PHP</h4>
                                <p style="color: var(--text-secondary); margin: 0.5rem 0;">Génère une structure de projet PHP complète avec MVC</p>
                            </div>
                            <div>
                                <button class="btn btn-small">▶️ Exécuter</button>
                                <button class="btn btn-small btn-secondary">✏️ Éditer</button>
                            </div>
                        </div>
                        <div style="background: var(--bg-tertiary); padding: 0.75rem; border-radius: 4px; margin-top: 1rem; font-family: monospace; font-size: 0.8rem;">
                            createDir src<br>
                            createDir src/controllers<br>
                            createDir src/models<br>
                            createDir src/views<br>
                            createFile src/index.php : &lt;?php echo "Hello World"; ?&gt;
                        </div>
                    </div>
                    
                    <div class="prompt-card">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h4>Configuration Apache</h4>
                                <p style="color: var(--text-secondary); margin: 0.5rem 0;">Crée les fichiers .htaccess et configuration Apache</p>
                            </div>
                            <div>
                                <button class="btn btn-small">▶️ Exécuter</button>
                                <button class="btn btn-small btn-secondary">✏️ Éditer</button>
                            </div>
                        </div>
                        <div style="background: var(--bg-tertiary); padding: 0.75rem; border-radius: 4px; margin-top: 1rem; font-family: monospace; font-size: 0.8rem;">
                            createFile .htaccess : RewriteEngine On<br>
                            createFile apache.conf : &lt;VirtualHost *:80&gt;...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings View -->
        <div class="view" id="settings-view">
            <div class="settings-container">
                <div class="settings-sidebar">
                    <h4 style="margin-bottom: 1rem;">Configuration</h4>
                    <div class="tree-item selected">⚙️ Général</div>
                    <div class="tree-item">🎨 Apparence</div>
                    <div class="tree-item">🔒 Sécurité</div>
                    <div class="tree-item">🚀 Performance</div>
                    <div class="tree-item">🔌 Extensions</div>
                    <div class="tree-item">📊 Monitoring</div>
                    <div class="tree-item">🗄️ Base de Données</div>
                    <div class="tree-item">🌐 Réseau</div>
                </div>
                <div class="settings-main">
                    <div class="settings-section">
                        <h3>Paramètres Généraux</h3>
                        <div class="form-group">
                            <label class="form-label">Nom de l'application</label>
                            <input type="text" class="form-input" value="SGC-AgentOne" placeholder="Nom de l'application">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Auteur</label>
                            <input type="text" class="form-input" value="AMICHI Amine" placeholder="Nom de l'auteur">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Langue par défaut</label>
                            <select class="form-input">
                                <option value="fr">Français</option>
                                <option value="en">English</option>
                                <option value="es">Español</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Répertoire de travail</label>
                            <input type="text" class="form-input" value="/storage/emulated/0/htdocs/sgc-agentone" placeholder="Chemin du répertoire">
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Serveur</h3>
                        <div class="form-group">
                            <label class="form-label">Port d'écoute</label>
                            <input type="number" class="form-input" value="5000" min="1" max="65535">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adresse IP</label>
                            <input type="text" class="form-input" value="0.0.0.0" placeholder="0.0.0.0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mode Debug</label>
                            <select class="form-input">
                                <option value="0">Désactivé</option>
                                <option value="1">Activé</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button class="btn">💾 Sauvegarder</button>
                        <button class="btn btn-secondary">🔄 Réinitialiser</button>
                        <button class="btn btn-secondary">📤 Exporter Config</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Help View -->
        <div class="view" id="help-view">
            <div class="help-container">
                <div class="help-sidebar">
                    <h4 style="margin-bottom: 1rem;">Documentation</h4>
                    <div class="tree-item selected">🚀 Démarrage</div>
                    <div class="tree-item">💬 Commandes Chat</div>
                    <div class="tree-item">📁 Gestion Fichiers</div>
                    <div class="tree-item">⚡ Terminal</div>
                    <div class="tree-item">🖥️ Serveur</div>
                    <div class="tree-item">🗄️ Base de Données</div>
                    <div class="tree-item">📝 Éditeur</div>
                    <div class="tree-item">🔧 Configuration</div>
                    <div class="tree-item">❓ FAQ</div>
                    <div class="tree-item">🐛 Dépannage</div>
                </div>
                <div class="help-main">
                    <div class="help-section">
                        <h2>Guide de Démarrage Rapide</h2>
                        <p>Bienvenue dans SGC-AgentOne, votre assistant universel de développement !</p>
                        
                        <h3>Première utilisation</h3>
                        <p>SGC-AgentOne est prêt à l'emploi. Voici comment commencer :</p>
                        <ol>
                            <li>Utilisez l'onglet <strong>Chat</strong> pour interagir avec l'assistant</li>
                            <li>Explorez vos fichiers avec l'onglet <strong>Fichiers</strong></li>
                            <li>Éditez votre code dans l'onglet <strong>Éditeur</strong></li>
                            <li>Contrôlez le serveur via l'onglet <strong>Serveur</strong></li>
                        </ol>
                        
                        <h3>Commandes de base</h3>
                        <p>Dans le chat, vous pouvez utiliser ces commandes :</p>
                        <ul>
                            <li><code>createFile nom.txt : contenu du fichier</code> - Créer un fichier</li>
                            <li><code>readFile nom.txt</code> - Lire un fichier</li>
                            <li><code>listDir dossier</code> - Lister le contenu d'un dossier</li>
                            <li><code>createDir nouveau_dossier</code> - Créer un dossier</li>
                            <li><code>deleteFile nom.txt</code> - Supprimer un fichier</li>
                        </ul>
                        
                        <h3>Interface</h3>
                        <p>L'interface est divisée en plusieurs sections :</p>
                        <ul>
                            <li><strong>Header</strong> : Navigation entre les vues et statut</li>
                            <li><strong>Chat</strong> : Communication avec l'assistant</li>
                            <li><strong>Fichiers</strong> : Explorateur de fichiers complet</li>
                            <li><strong>Éditeur</strong> : Édition de code avec coloration syntaxique</li>
                            <li><strong>Terminal</strong> : Console de commandes</li>
                            <li><strong>Serveur</strong> : Contrôle et monitoring du serveur</li>
                        </ul>
                        
                        <h3>Conseils</h3>
                        <ul>
                            <li>Utilisez le mode debug pour voir plus d'informations</li>
                            <li>Sauvegardez régulièrement vos paramètres</li>
                            <li>Consultez les logs en cas de problème</li>
                            <li>L'interface s'adapte automatiquement aux écrans mobiles</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let currentView = 'chat';
        let chatHistory = [];
        let terminalHistory = [];
        let settings = {
            theme: 'dark',
            language: 'fr',
            autoSave: true,
            debugMode: false
        };

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeInterface();
            loadSettings();
            updateTime();
            setInterval(updateTime, 1000);
        });

        // Initialisation de l'interface
        function initializeInterface() {
            // Navigation entre les vues
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    switchView(this.dataset.view);
                });
            });

            // Chat
            initializeChat();
            
            // Terminal
            initializeTerminal();
            
            // Files
            initializeFiles();
            
            // Editor
            initializeEditor();
            
            // Server
            initializeServer();
        }

        // Basculer entre les vues
        function switchView(viewName) {
            // Mettre à jour les onglets
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-view="${viewName}"]`).classList.add('active');

            // Mettre à jour les vues
            document.querySelectorAll('.view').forEach(view => {
                view.classList.remove('active');
            });
            document.getElementById(`${viewName}-view`).classList.add('active');

            currentView = viewName;
        }

        // Initialisation du chat
        function initializeChat() {
            const chatInput = document.getElementById('chat-input');
            const chatSend = document.getElementById('chat-send');
            const chatMessages = document.getElementById('chat-messages');

            function sendMessage() {
                const message = chatInput.value.trim();
                if (!message) return;

                // Ajouter le message utilisateur
                addChatMessage(message, 'user');
                chatInput.value = '';

                // Traiter la commande
                processCommand(message);
            }

            chatSend.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }

        // Ajouter un message au chat
        function addChatMessage(content, sender) {
            const chatMessages = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            if (sender === 'user') {
                messageDiv.innerHTML = `<strong>Vous:</strong> ${content}`;
            } else {
                messageDiv.innerHTML = `<strong>SGC-AgentOne:</strong> ${content}`;
            }
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Traitement des commandes
        function processCommand(command) {
            // Simulation du traitement des commandes
            setTimeout(() => {
                if (command.startsWith('createFile')) {
                    const parts = command.split(' : ');
                    const fileName = parts[0].replace('createFile ', '');
                    const content = parts[1] || '';
                    addChatMessage(`✅ Fichier "${fileName}" créé avec succès !`, 'assistant');
                } else if (command.startsWith('readFile')) {
                    const fileName = command.replace('readFile ', '');
                    addChatMessage(`📄 Contenu du fichier "${fileName}":\n\n<code>Contenu du fichier...</code>`, 'assistant');
                } else if (command.startsWith('listDir')) {
                    const dirName = command.replace('listDir ', '') || '.';
                    addChatMessage(`📁 Contenu du dossier "${dirName}":\n\n• index.php\n• README.md\n• core/\n• extensions/`, 'assistant');
                } else if (command.startsWith('createDir')) {
                    const dirName = command.replace('createDir ', '');
                    addChatMessage(`✅ Dossier "${dirName}" créé avec succès !`, 'assistant');
                } else if (command.startsWith('deleteFile')) {
                    const fileName = command.replace('deleteFile ', '');
                    addChatMessage(`🗑️ Fichier "${fileName}" supprimé avec succès !`, 'assistant');
                } else {
                    addChatMessage(`❓ Commande non reconnue. Tapez "help" pour voir les commandes disponibles.`, 'assistant');
                }
            }, 500);
        }

        // Initialisation du terminal
        function initializeTerminal() {
            const terminalInput = document.getElementById('terminal-input');
            const terminalOutput = document.getElementById('terminal-output');

            terminalInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const command = this.value.trim();
                    if (command) {
                        executeTerminalCommand(command);
                        terminalHistory.push(command);
                        this.value = '';
                    }
                }
            });
        }

        // Exécution des commandes terminal
        function executeTerminalCommand(command) {
            const terminalOutput = document.getElementById('terminal-output');
            const commandDiv = document.createElement('div');
            commandDiv.innerHTML = `<span style="color: #00ff00;">sgc@localhost:~$</span> ${command}`;
            terminalOutput.appendChild(commandDiv);

            const resultDiv = document.createElement('div');
            resultDiv.style.marginLeft = '1rem';
            resultDiv.style.marginBottom = '1rem';

            switch (command.toLowerCase()) {
                case 'ls':
                    resultDiv.innerHTML = 'index.php  README.md  core/  extensions/  api/';
                    break;
                case 'pwd':
                    resultDiv.innerHTML = '/storage/emulated/0/htdocs/sgc-agentone';
                    break;
                case 'status':
                    resultDiv.innerHTML = '<span style="color: #00ff00;">✅ Serveur en ligne - Port 5000</span>';
                    break;
                case 'clear':
                    terminalOutput.innerHTML = '';
                    return;
                case 'help':
                    resultDiv.innerHTML = `<span style="color: #ffff00;">Commandes disponibles:</span><br>
                        • ls - Lister les fichiers<br>
                        • pwd - Répertoire courant<br>
                        • cat [fichier] - Afficher le contenu<br>
                        • mkdir [nom] - Créer un dossier<br>
                        • touch [nom] - Créer un fichier<br>
                        • rm [fichier] - Supprimer<br>
                        • clear - Effacer l'écran<br>
                        • status - Statut du serveur`;
                    break;
                default:
                    resultDiv.innerHTML = `<span style="color: #ff0000;">Commande non trouvée: ${command}</span>`;
            }

            terminalOutput.appendChild(resultDiv);
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
        }

        // Initialisation des fichiers
        function initializeFiles() {
            const newFileBtn = document.getElementById('new-file');
            const newFolderBtn = document.getElementById('new-folder');
            const uploadFileBtn = document.getElementById('upload-file');
            const refreshFilesBtn = document.getElementById('refresh-files');

            newFileBtn.addEventListener('click', function() {
                const fileName = prompt('Nom du nouveau fichier:');
                if (fileName) {
                    addChatMessage(`✅ Fichier "${fileName}" créé !`, 'assistant');
                }
            });

            newFolderBtn.addEventListener('click', function() {
                const folderName = prompt('Nom du nouveau dossier:');
                if (folderName) {
                    addChatMessage(`✅ Dossier "${folderName}" créé !`, 'assistant');
                }
            });

            uploadFileBtn.addEventListener('click', function() {
                const input = document.createElement('input');
                input.type = 'file';
                input.multiple = true;
                input.onchange = function(e) {
                    const files = Array.from(e.target.files);
                    addChatMessage(`✅ ${files.length} fichier(s) uploadé(s) !`, 'assistant');
                };
                input.click();
            });

            refreshFilesBtn.addEventListener('click', function() {
                addChatMessage(`🔄 Arborescence actualisée !`, 'assistant');
            });
        }

        // Initialisation de l'éditeur
        function initializeEditor() {
            const editorContent = document.getElementById('editor-content');
            
            // Auto-sauvegarde
            let saveTimeout;
            editorContent.addEventListener('input', function() {
                if (settings.autoSave) {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => {
                        console.log('Auto-sauvegarde...');
                    }, 2000);
                }
            });
        }

        // Initialisation du serveur
        function initializeServer() {
            // Mise à jour des logs du serveur
            setInterval(updateServerLogs, 5000);
        }

        // Mise à jour des logs du serveur
        function updateServerLogs() {
            const serverLogs = document.getElementById('server-logs');
            if (serverLogs && currentView === 'server') {
                const now = new Date();
                const timestamp = now.toISOString().replace('T', ' ').substring(0, 19);
                const logEntry = `[${timestamp}] INFO: Heartbeat - Système opérationnel<br>`;
                serverLogs.innerHTML += logEntry;
                serverLogs.scrollTop = serverLogs.scrollHeight;
            }
        }

        // Chargement des paramètres
        function loadSettings() {
            const savedSettings = localStorage.getItem('sgc-settings');
            if (savedSettings) {
                settings = { ...settings, ...JSON.parse(savedSettings) };
            }
        }

        // Sauvegarde des paramètres
        function saveSettings() {
            localStorage.setItem('sgc-settings', JSON.stringify(settings));
        }

        // Mise à jour de l'heure
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('fr-FR');
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Gestion des erreurs globales
        window.addEventListener('error', function(e) {
            console.error('Erreur JavaScript:', e.error);
            addChatMessage(`❌ Erreur: ${e.message}`, 'assistant');
        });

        // API PHP intégrée (simulation)
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            header('Content-Type: application/json');
            
            $action = $_POST['action'];
            $response = ['success' => false, 'message' => 'Action non reconnue'];
            
            switch ($action) {
                case 'createFile':
                    $filename = $_POST['filename'] ?? '';
                    $content = $_POST['content'] ?? '';
                    if ($filename) {
                        file_put_contents($filename, $content);
                        $response = ['success' => true, 'message' => "Fichier $filename créé"];
                    }
                    break;
                    
                case 'readFile':
                    $filename = $_POST['filename'] ?? '';
                    if ($filename && file_exists($filename)) {
                        $content = file_get_contents($filename);
                        $response = ['success' => true, 'content' => $content];
                    }
                    break;
                    
                case 'listDir':
                    $dirname = $_POST['dirname'] ?? '.';
                    if (is_dir($dirname)) {
                        $files = array_diff(scandir($dirname), ['.', '..']);
                        $response = ['success' => true, 'files' => array_values($files)];
                    }
                    break;
                    
                case 'createDir':
                    $dirname = $_POST['dirname'] ?? '';
                    if ($dirname) {
                        mkdir($dirname, 0755, true);
                        $response = ['success' => true, 'message' => "Dossier $dirname créé"];
                    }
                    break;
                    
                case 'deleteFile':
                    $filename = $_POST['filename'] ?? '';
                    if ($filename && file_exists($filename)) {
                        unlink($filename);
                        $response = ['success' => true, 'message' => "Fichier $filename supprimé"];
                    }
                    break;
            }
            
            echo json_encode($response);
            exit;
        }
        ?>
    </script>
</body>
</html>