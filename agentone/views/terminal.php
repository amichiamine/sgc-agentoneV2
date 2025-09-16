<?php
/**
 * Vue Terminal - Émulateur de terminal web
 */
?>

<div class="terminal-view">
    <div class="terminal-header">
        <div class="terminal-tabs" id="terminal-tabs">
            <div class="terminal-tab active" data-id="1">
                <span>Terminal 1</span>
                <button class="close-tab">×</button>
            </div>
            <button class="new-tab-btn" id="new-tab-btn">+</button>
        </div>
        <div class="terminal-controls">
            <button class="btn btn-secondary btn-sm" id="clear-terminal">🗑️ Effacer</button>
            <button class="btn btn-secondary btn-sm" id="save-session">💾 Sauver</button>
        </div>
    </div>
    
    <div class="terminal-container" id="terminal-container">
        <div class="terminal-output" id="terminal-output">
            <div class="welcome-message">
                <div class="ascii-art">
   _____ _____ _____            _                 _    ____             
  / ____/ ____/ ____|          / \   __ _  ___ _ | |_ / __ \  _ __   ___ 
 | (___| |  _| |     ______   / _ \ / _` |/ _ \ '_ \| __| |  | || '_ \ / _ \
  \___ \ | |_| |    |______| / ___ \ (_| |  __/ | | | |_| |__| || | | |  __/
  ____) | |___\____|        /_/   \_\__, |\___|_| |_|\__|\____/|_| |_|\___|
 |_____/ \____|                     |___/                                 
                </div>
                <p>Terminal Web SGC-AgentOne v3.0</p>
                <p>Tapez <code>help</code> pour voir les commandes disponibles.</p>
            </div>
        </div>
        
        <div class="terminal-input-line">
            <span class="terminal-prompt" id="terminal-prompt">sgc@agentone:~$ </span>
            <input type="text" id="terminal-input" class="terminal-input" autocomplete="off" spellcheck="false">
        </div>
    </div>
</div>

<style>
.terminal-view {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #000;
    color: #00ff00;
    font-family: 'JetBrains Mono', 'Courier New', monospace;
}

.terminal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #1a1a1a;
    border-bottom: 1px solid #333;
}

.terminal-tabs {
    display: flex;
    align-items: center;
    gap: 4px;
}

.terminal-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: #333;
    border-radius: 4px 4px 0 0;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.2s ease;
}

.terminal-tab.active {
    background: #000;
    color: #00ff00;
}

.terminal-tab:hover:not(.active) {
    background: #444;
}

.close-tab {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-tab:hover {
    color: #ff4444;
}

.new-tab-btn {
    background: #333;
    border: none;
    color: #00ff00;
    cursor: pointer;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 1rem;
    transition: background 0.2s ease;
}

.new-tab-btn:hover {
    background: #444;
}

.terminal-controls {
    display: flex;
    gap: 8px;
}

.terminal-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 16px;
    overflow: hidden;
}

.terminal-output {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 16px;
    line-height: 1.4;
}

.welcome-message {
    margin-bottom: 20px;
}

.ascii-art {
    color: #00ffff;
    font-size: 0.7rem;
    white-space: pre;
    margin-bottom: 10px;
}

.terminal-line {
    margin: 2px 0;
    word-wrap: break-word;
}

.terminal-command {
    color: #00ff00;
}

.terminal-output-text {
    color: #ffffff;
    margin-left: 0;
}

.terminal-error {
    color: #ff4444;
}

.terminal-success {
    color: #44ff44;
}

.terminal-warning {
    color: #ffff44;
}

.terminal-input-line {
    display: flex;
    align-items: center;
    gap: 8px;
}

.terminal-prompt {
    color: #00ff00;
    font-weight: bold;
    white-space: nowrap;
}

.terminal-input {
    flex: 1;
    background: transparent;
    border: none;
    color: #00ff00;
    font-family: inherit;
    font-size: 1rem;
    outline: none;
    caret-color: #00ff00;
}

.terminal-input::selection {
    background: #00ff00;
    color: #000;
}

.cursor {
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0; }
}

@media (max-width: 768px) {
    .terminal-header {
        flex-direction: column;
        gap: 8px;
        align-items: stretch;
    }
    
    .terminal-tabs {
        justify-content: flex-start;
        overflow-x: auto;
    }
    
    .terminal-controls {
        justify-content: center;
    }
    
    .ascii-art {
        font-size: 0.5rem;
    }
}
</style>

<script>
window.initView = function() {
    const terminalOutput = document.getElementById('terminal-output');
    const terminalInput = document.getElementById('terminal-input');
    const terminalPrompt = document.getElementById('terminal-prompt');
    const clearBtn = document.getElementById('clear-terminal');
    const saveBtn = document.getElementById('save-session');
    const newTabBtn = document.getElementById('new-tab-btn');
    const terminalTabs = document.getElementById('terminal-tabs');
    
    let currentDirectory = '~';
    let commandHistory = [];
    let historyIndex = -1;
    let tabCounter = 1;
    
    // Commandes disponibles
    const commands = {
        help: () => {
            return `Commandes disponibles :
  help          - Afficher cette aide
  clear         - Effacer l'écran
  ls            - Lister les fichiers
  pwd           - Afficher le répertoire courant
  cd [dir]      - Changer de répertoire
  mkdir [dir]   - Créer un répertoire
  touch [file]  - Créer un fichier
  cat [file]    - Afficher le contenu d'un fichier
  echo [text]   - Afficher du texte
  date          - Afficher la date
  whoami        - Afficher l'utilisateur
  uname         - Informations système
  ps            - Processus en cours
  history       - Historique des commandes
  exit          - Fermer le terminal`;
        },
        
        clear: () => {
            terminalOutput.innerHTML = '';
            return '';
        },
        
        ls: async () => {
            try {
                const result = await AgentOne.api.post('files.php', {
                    action: 'listDir',
                    path: currentDirectory === '~' ? '.' : currentDirectory
                });
                
                if (result.success) {
                    return result.data.items.map(item => {
                        const icon = item.type === 'directory' ? '📁' : '📄';
                        const color = item.type === 'directory' ? '#00ffff' : '#ffffff';
                        return `<span style="color: ${color}">${icon} ${item.name}</span>`;
                    }).join('\n');
                } else {
                    return `<span class="terminal-error">Erreur : ${result.error}</span>`;
                }
            } catch (error) {
                return `<span class="terminal-error">Erreur de connexion</span>`;
            }
        },
        
        pwd: () => {
            return `/home/sgc${currentDirectory === '~' ? '' : '/' + currentDirectory}`;
        },
        
        cd: (args) => {
            const dir = args[0] || '~';
            if (dir === '~' || dir === '/') {
                currentDirectory = '~';
                updatePrompt();
                return '';
            } else if (dir === '..') {
                if (currentDirectory !== '~') {
                    const parts = currentDirectory.split('/');
                    parts.pop();
                    currentDirectory = parts.length > 0 ? parts.join('/') : '~';
                    updatePrompt();
                }
                return '';
            } else {
                currentDirectory = currentDirectory === '~' ? dir : currentDirectory + '/' + dir;
                updatePrompt();
                return '';
            }
        },
        
        mkdir: async (args) => {
            if (!args[0]) return '<span class="terminal-error">Usage: mkdir [nom_dossier]</span>';
            
            try {
                const result = await AgentOne.api.post('chat.php', {
                    message: `createDir ${args[0]}`
                });
                
                if (result.success) {
                    return `<span class="terminal-success">Dossier '${args[0]}' créé</span>`;
                } else {
                    return `<span class="terminal-error">Erreur : ${result.error}</span>`;
                }
            } catch (error) {
                return `<span class="terminal-error">Erreur de connexion</span>`;
            }
        },
        
        touch: async (args) => {
            if (!args[0]) return '<span class="terminal-error">Usage: touch [nom_fichier]</span>';
            
            try {
                const result = await AgentOne.api.post('chat.php', {
                    message: `createFile ${args[0]} : `
                });
                
                if (result.success) {
                    return `<span class="terminal-success">Fichier '${args[0]}' créé</span>`;
                } else {
                    return `<span class="terminal-error">Erreur : ${result.error}</span>`;
                }
            } catch (error) {
                return `<span class="terminal-error">Erreur de connexion</span>`;
            }
        },
        
        cat: async (args) => {
            if (!args[0]) return '<span class="terminal-error">Usage: cat [nom_fichier]</span>';
            
            try {
                const result = await AgentOne.api.post('chat.php', {
                    message: `readFile ${args[0]}`
                });
                
                if (result.success && result.data && result.data.content) {
                    return `<pre>${result.data.content}</pre>`;
                } else {
                    return `<span class="terminal-error">Erreur : ${result.error || 'Fichier non trouvé'}</span>`;
                }
            } catch (error) {
                return `<span class="terminal-error">Erreur de connexion</span>`;
            }
        },
        
        echo: (args) => {
            return args.join(' ');
        },
        
        date: () => {
            return new Date().toLocaleString('fr-FR');
        },
        
        whoami: () => {
            return 'sgc-user';
        },
        
        uname: () => {
            return 'SGC-AgentOne v3.0 (Web Terminal)';
        },
        
        ps: () => {
            return `PID  COMMAND
1    sgc-agentone
2    php-server
3    web-terminal`;
        },
        
        history: () => {
            return commandHistory.map((cmd, i) => `${i + 1}  ${cmd}`).join('\n');
        },
        
        exit: () => {
            return '<span class="terminal-warning">Fermeture du terminal...</span>';
        }
    };
    
    // Mettre à jour le prompt
    function updatePrompt() {
        const dir = currentDirectory === '~' ? '~' : currentDirectory;
        terminalPrompt.textContent = `sgc@agentone:${dir}$ `;
    }
    
    // Ajouter une ligne au terminal
    function addLine(content, className = '') {
        const line = document.createElement('div');
        line.className = `terminal-line ${className}`;
        line.innerHTML = content;
        terminalOutput.appendChild(line);
        terminalOutput.scrollTop = terminalOutput.scrollHeight;
    }
    
    // Exécuter une commande
    async function executeCommand(input) {
        const trimmed = input.trim();
        if (!trimmed) return;
        
        // Ajouter à l'historique
        commandHistory.push(trimmed);
        historyIndex = -1;
        
        // Afficher la commande
        addLine(`<span class="terminal-prompt">${terminalPrompt.textContent}</span><span class="terminal-command">${trimmed}</span>`);
        
        // Parser la commande
        const parts = trimmed.split(' ');
        const command = parts[0].toLowerCase();
        const args = parts.slice(1);
        
        // Exécuter
        if (commands[command]) {
            try {
                const result = await commands[command](args);
                if (result) {
                    addLine(result, 'terminal-output-text');
                }
            } catch (error) {
                addLine(`<span class="terminal-error">Erreur : ${error.message}</span>`);
            }
        } else {
            addLine(`<span class="terminal-error">Commande inconnue : ${command}. Tapez 'help' pour voir les commandes disponibles.</span>`);
        }
    }
    
    // Gestion des événements
    terminalInput.addEventListener('keydown', async (e) => {
        if (e.key === 'Enter') {
            const input = terminalInput.value;
            terminalInput.value = '';
            await executeCommand(input);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (historyIndex < commandHistory.length - 1) {
                historyIndex++;
                terminalInput.value = commandHistory[commandHistory.length - 1 - historyIndex];
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (historyIndex > 0) {
                historyIndex--;
                terminalInput.value = commandHistory[commandHistory.length - 1 - historyIndex];
            } else if (historyIndex === 0) {
                historyIndex = -1;
                terminalInput.value = '';
            }
        } else if (e.key === 'Tab') {
            e.preventDefault();
            // Auto-complétion basique
            const input = terminalInput.value;
            const matches = Object.keys(commands).filter(cmd => cmd.startsWith(input));
            if (matches.length === 1) {
                terminalInput.value = matches[0] + ' ';
            }
        }
    });
    
    // Effacer le terminal
    clearBtn.addEventListener('click', () => {
        commands.clear();
    });
    
    // Sauvegarder la session
    saveBtn.addEventListener('click', () => {
        const content = terminalOutput.innerHTML;
        const blob = new Blob([content], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `terminal-session-${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.html`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        addLine('<span class="terminal-success">Session sauvegardée</span>');
    });
    
    // Nouveau terminal (onglet)
    newTabBtn.addEventListener('click', () => {
        tabCounter++;
        const newTab = document.createElement('div');
        newTab.className = 'terminal-tab';
        newTab.dataset.id = tabCounter;
        newTab.innerHTML = `
            <span>Terminal ${tabCounter}</span>
            <button class="close-tab">×</button>
        `;
        
        // Désactiver les autres onglets
        document.querySelectorAll('.terminal-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        newTab.classList.add('active');
        terminalTabs.insertBefore(newTab, newTabBtn);
        
        // Effacer le terminal pour le nouvel onglet
        commands.clear();
        addLine('<div class="welcome-message"><p>Nouveau terminal ouvert</p></div>');
    });
    
    // Gestion des onglets
    terminalTabs.addEventListener('click', (e) => {
        if (e.target.classList.contains('close-tab')) {
            const tab = e.target.closest('.terminal-tab');
            const tabs = document.querySelectorAll('.terminal-tab');
            
            if (tabs.length > 1) {
                tab.remove();
                // Activer le premier onglet restant si celui fermé était actif
                if (tab.classList.contains('active')) {
                    document.querySelector('.terminal-tab').classList.add('active');
                }
            }
        } else if (e.target.closest('.terminal-tab')) {
            const tab = e.target.closest('.terminal-tab');
            document.querySelectorAll('.terminal-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
        }
    });
    
    // Focus automatique sur l'input
    terminalInput.focus();
    
    // Maintenir le focus
    document.addEventListener('click', (e) => {
        if (e.target.closest('.terminal-container')) {
            terminalInput.focus();
        }
    });
    
    // Initialisation
    updatePrompt();
};
</script>