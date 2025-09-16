<?php
/**
 * Vue Éditeur - Éditeur de code avec coloration syntaxique
 */
?>

<div class="editor-view">
    <div class="editor-sidebar sidebar">
        <div class="sidebar-header">
            <h3>📁 Explorateur</h3>
            <div class="sidebar-actions">
                <button class="btn btn-primary btn-sm" id="new-file-btn" title="Nouveau fichier">📄</button>
                <button class="btn btn-primary btn-sm" id="new-folder-btn" title="Nouveau dossier">📁</button>
                <button class="btn btn-secondary btn-sm" id="refresh-tree-btn" title="Actualiser">🔄</button>
            </div>
        </div>
        <div class="sidebar-content">
            <div class="file-tree" id="file-tree">
                <div class="loading">Chargement de l'arborescence...</div>
            </div>
        </div>
    </div>
    
    <div class="editor-main flex-1">
        <div class="editor-tabs" id="editor-tabs">
            <div class="tabs-container" id="tabs-container">
                <!-- Onglets dynamiques -->
            </div>
            <div class="tabs-actions">
                <button class="btn btn-secondary btn-sm" id="close-all-btn">✖️ Tout fermer</button>
            </div>
        </div>
        
        <div class="editor-toolbar">
            <div class="toolbar-left">
                <button class="btn btn-primary" id="save-btn" disabled>💾 Sauvegarder</button>
                <button class="btn btn-secondary" id="save-as-btn" disabled>💾 Sauvegarder sous...</button>
            </div>
            <div class="toolbar-center">
                <span class="file-info" id="file-info">Aucun fichier ouvert</span>
            </div>
            <div class="toolbar-right">
                <select id="language-select" class="form-control" disabled>
                    <option value="text">Texte</option>
                    <option value="php">PHP</option>
                    <option value="javascript">JavaScript</option>
                    <option value="html">HTML</option>
                    <option value="css">CSS</option>
                    <option value="json">JSON</option>
                    <option value="markdown">Markdown</option>
                </select>
                <button class="btn btn-secondary" id="format-btn" disabled>🎨 Formater</button>
            </div>
        </div>
        
        <div class="editor-container" id="editor-container">
            <div class="editor-welcome" id="editor-welcome">
                <div class="welcome-content">
                    <h3>📝 Éditeur de Code SGC-AgentOne</h3>
                    <p>Sélectionnez un fichier dans l'explorateur pour commencer à éditer</p>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" id="create-new-file">📄 Créer un nouveau fichier</button>
                        <button class="btn btn-secondary" id="open-existing-file">📂 Ouvrir un fichier existant</button>
                    </div>
                </div>
            </div>
            
            <div class="editor-workspace" id="editor-workspace" style="display: none;">
                <div class="line-numbers" id="line-numbers"></div>
                <textarea id="code-editor" class="code-editor" spellcheck="false"></textarea>
            </div>
        </div>
        
        <div class="editor-status">
            <div class="status-left">
                <span id="cursor-position">Ligne 1, Colonne 1</span>
                <span id="selection-info"></span>
            </div>
            <div class="status-right">
                <span id="file-encoding">UTF-8</span>
                <span id="file-size">0 octets</span>
                <span id="modification-status">Non modifié</span>
            </div>
        </div>
    </div>
</div>

<style>
.editor-view {
    height: 100%;
    display: flex;
}

.editor-sidebar {
    width: 280px;
    border-right: 1px solid var(--border-color);
}

.file-tree {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.tree-item {
    display: flex;
    align-items: center;
    padding: 6px 12px;
    margin: 1px 0;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    position: relative;
}

.tree-item:hover {
    background: var(--accent-secondary);
}

.tree-item.active {
    background: var(--accent-primary);
    color: var(--bg-primary);
}

.tree-item.modified::after {
    content: '●';
    position: absolute;
    right: 8px;
    color: var(--warning-color);
}

.tree-icon {
    margin-right: 8px;
    font-size: 1rem;
}

.tree-label {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.editor-main {
    display: flex;
    flex-direction: column;
}

.editor-tabs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    padding: 4px 8px;
}

.tabs-container {
    display: flex;
    overflow-x: auto;
    gap: 2px;
}

.editor-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: var(--accent-secondary);
    border-radius: 6px 6px 0 0;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    min-width: 120px;
    max-width: 200px;
    position: relative;
}

.editor-tab.active {
    background: var(--bg-primary);
    color: var(--accent-primary);
}

.editor-tab:hover:not(.active) {
    background: var(--bg-tertiary);
}

.editor-tab.modified::before {
    content: '●';
    position: absolute;
    top: 4px;
    right: 4px;
    color: var(--warning-color);
    font-size: 0.8rem;
}

.tab-label {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.tab-close {
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    font-size: 1.2rem;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.tab-close:hover {
    background: var(--error-color);
    color: var(--bg-primary);
}

.editor-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 16px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    gap: 16px;
}

.toolbar-left,
.toolbar-right {
    display: flex;
    gap: 8px;
    align-items: center;
}

.toolbar-center {
    flex: 1;
    text-align: center;
}

.file-info {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-family: 'JetBrains Mono', monospace;
}

.editor-container {
    flex: 1;
    position: relative;
    overflow: hidden;
}

.editor-welcome {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-tertiary);
}

.welcome-content {
    text-align: center;
    max-width: 400px;
}

.welcome-content h3 {
    margin: 0 0 16px 0;
    color: var(--accent-primary);
    font-size: 1.5rem;
}

.welcome-content p {
    margin: 0 0 24px 0;
    color: var(--text-secondary);
    line-height: 1.5;
}

.welcome-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.editor-workspace {
    height: 100%;
    display: flex;
    background: var(--bg-tertiary);
}

.line-numbers {
    width: 60px;
    background: var(--bg-secondary);
    border-right: 1px solid var(--border-color);
    padding: 16px 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    color: var(--text-secondary);
    text-align: right;
    user-select: none;
    overflow: hidden;
}

.code-editor {
    flex: 1;
    padding: 16px;
    border: none;
    background: transparent;
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    resize: none;
    outline: none;
    tab-size: 4;
    white-space: pre;
    overflow-wrap: normal;
    overflow-x: auto;
}

.code-editor::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

.code-editor::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

.code-editor::-webkit-scrollbar-thumb {
    background: var(--accent-secondary);
    border-radius: 6px;
}

.code-editor::-webkit-scrollbar-thumb:hover {
    background: var(--accent-primary);
}

.editor-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 16px;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-color);
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.status-left,
.status-right {
    display: flex;
    gap: 16px;
}

/* Coloration syntaxique basique */
.syntax-keyword { color: #ff79c6; }
.syntax-string { color: #f1fa8c; }
.syntax-comment { color: #6272a4; font-style: italic; }
.syntax-number { color: #bd93f9; }
.syntax-function { color: #50fa7b; }
.syntax-variable { color: #8be9fd; }

@media (max-width: 768px) {
    .editor-view {
        flex-direction: column;
    }
    
    .editor-sidebar {
        width: 100%;
        height: 200px;
        border-right: none;
        border-bottom: 1px solid var(--border-color);
    }
    
    .editor-toolbar {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .toolbar-center {
        order: 3;
        width: 100%;
        text-align: left;
    }
    
    .line-numbers {
        width: 40px;
        padding: 16px 4px;
    }
    
    .welcome-actions {
        flex-direction: column;
    }
}
</style>

<script>
window.initView = function() {
    const fileTree = document.getElementById('file-tree');
    const tabsContainer = document.getElementById('tabs-container');
    const codeEditor = document.getElementById('code-editor');
    const lineNumbers = document.getElementById('line-numbers');
    const editorWelcome = document.getElementById('editor-welcome');
    const editorWorkspace = document.getElementById('editor-workspace');
    const fileInfo = document.getElementById('file-info');
    const saveBtn = document.getElementById('save-btn');
    const saveAsBtn = document.getElementById('save-as-btn');
    const languageSelect = document.getElementById('language-select');
    const formatBtn = document.getElementById('format-btn');
    const cursorPosition = document.getElementById('cursor-position');
    const selectionInfo = document.getElementById('selection-info');
    const fileSize = document.getElementById('file-size');
    const modificationStatus = document.getElementById('modification-status');
    
    let openTabs = [];
    let activeTabId = null;
    let tabCounter = 0;
    let isModified = false;
    
    // Charger l'arborescence
    async function loadFileTree() {
        try {
            AgentOne.ui.showLoader(fileTree, 'Chargement...');
            
            const result = await AgentOne.api.post('files.php', {
                action: 'listDir',
                path: '.'
            });
            
            if (result.success) {
                renderFileTree(result.data.items, '');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            fileTree.innerHTML = `<div class="error-message">Erreur : ${error.message}</div>`;
        }
    }
    
    // Rendu de l'arborescence
    function renderFileTree(items, basePath) {
        const html = items.map(item => {
            const fullPath = basePath ? `${basePath}/${item.name}` : item.name;
            const icon = item.type === 'directory' ? '📁' : getFileIcon(item.name);
            
            return `
                <div class="tree-item" data-path="${fullPath}" data-type="${item.type}">
                    <span class="tree-icon">${icon}</span>
                    <span class="tree-label">${item.name}</span>
                </div>
            `;
        }).join('');
        
        if (basePath === '') {
            fileTree.innerHTML = html;
        } else {
            // Pour les sous-dossiers, on pourrait implémenter une vue arborescente
            fileTree.innerHTML = html;
        }
        
        // Événements
        fileTree.querySelectorAll('.tree-item').forEach(item => {
            item.addEventListener('click', () => {
                const path = item.dataset.path;
                const type = item.dataset.type;
                
                if (type === 'directory') {
                    loadSubDirectory(path);
                } else {
                    openFile(path);
                }
            });
        });
    }
    
    // Icône selon l'extension
    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            'php': '🐘',
            'js': '📜',
            'html': '🌐',
            'css': '🎨',
            'json': '📋',
            'md': '📝',
            'txt': '📄',
            'sql': '🗄️',
            'xml': '📋',
            'yml': '⚙️',
            'yaml': '⚙️'
        };
        return icons[ext] || '📄';
    }
    
    // Ouvrir un fichier
    async function openFile(filePath) {
        // Vérifier si le fichier est déjà ouvert
        const existingTab = openTabs.find(tab => tab.path === filePath);
        if (existingTab) {
            switchToTab(existingTab.id);
            return;
        }
        
        try {
            const result = await AgentOne.api.post('files.php', {
                action: 'readFile',
                path: filePath
            });
            
            if (result.success) {
                const content = result.data.content;
                const fileName = filePath.split('/').pop();
                
                // Créer un nouvel onglet
                const tab = {
                    id: ++tabCounter,
                    path: filePath,
                    name: fileName,
                    content: content,
                    originalContent: content,
                    modified: false,
                    language: detectLanguage(fileName)
                };
                
                openTabs.push(tab);
                renderTabs();
                switchToTab(tab.id);
                
                // Masquer l'écran d'accueil
                editorWelcome.style.display = 'none';
                editorWorkspace.style.display = 'flex';
                
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Détecter le langage
    function detectLanguage(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const languages = {
            'php': 'php',
            'js': 'javascript',
            'html': 'html',
            'css': 'css',
            'json': 'json',
            'md': 'markdown'
        };
        return languages[ext] || 'text';
    }
    
    // Rendu des onglets
    function renderTabs() {
        if (openTabs.length === 0) {
            tabsContainer.innerHTML = '';
            return;
        }
        
        tabsContainer.innerHTML = openTabs.map(tab => `
            <div class="editor-tab ${tab.id === activeTabId ? 'active' : ''} ${tab.modified ? 'modified' : ''}" data-id="${tab.id}">
                <span class="tab-label" title="${tab.path}">${tab.name}</span>
                <button class="tab-close">×</button>
            </div>
        `).join('');
        
        // Événements des onglets
        tabsContainer.querySelectorAll('.editor-tab').forEach(tabEl => {
            const tabId = parseInt(tabEl.dataset.id);
            
            tabEl.addEventListener('click', (e) => {
                if (!e.target.classList.contains('tab-close')) {
                    switchToTab(tabId);
                }
            });
            
            const closeBtn = tabEl.querySelector('.tab-close');
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                closeTab(tabId);
            });
        });
    }
    
    // Basculer vers un onglet
    function switchToTab(tabId) {
        const tab = openTabs.find(t => t.id === tabId);
        if (!tab) return;
        
        activeTabId = tabId;
        
        // Sauvegarder le contenu de l'onglet précédent
        if (codeEditor.value !== undefined) {
            const previousTab = openTabs.find(t => t.id === activeTabId);
            if (previousTab) {
                previousTab.content = codeEditor.value;
                previousTab.modified = previousTab.content !== previousTab.originalContent;
            }
        }
        
        // Charger le contenu du nouvel onglet
        codeEditor.value = tab.content;
        languageSelect.value = tab.language;
        fileInfo.textContent = tab.path;
        
        // Mettre à jour les boutons
        saveBtn.disabled = false;
        saveAsBtn.disabled = false;
        languageSelect.disabled = false;
        formatBtn.disabled = false;
        
        // Mettre à jour l'affichage
        renderTabs();
        updateLineNumbers();
        updateStatus();
        
        // Mettre à jour la sélection dans l'arbre
        document.querySelectorAll('.tree-item').forEach(item => {
            item.classList.remove('active');
        });
        const treeItem = document.querySelector(`[data-path="${tab.path}"]`);
        if (treeItem) {
            treeItem.classList.add('active');
        }
    }
    
    // Fermer un onglet
    function closeTab(tabId) {
        const tabIndex = openTabs.findIndex(tab => tab.id === tabId);
        if (tabIndex === -1) return;
        
        const tab = openTabs[tabIndex];
        
        // Vérifier si le fichier est modifié
        if (tab.modified) {
            if (!confirm(`Le fichier "${tab.name}" a été modifié. Fermer sans sauvegarder ?`)) {
                return;
            }
        }
        
        // Supprimer l'onglet
        openTabs.splice(tabIndex, 1);
        
        // Si c'était l'onglet actif
        if (activeTabId === tabId) {
            if (openTabs.length > 0) {
                // Activer l'onglet précédent ou suivant
                const newActiveIndex = Math.max(0, tabIndex - 1);
                switchToTab(openTabs[newActiveIndex].id);
            } else {
                // Plus d'onglets ouverts
                activeTabId = null;
                editorWelcome.style.display = 'flex';
                editorWorkspace.style.display = 'none';
                fileInfo.textContent = 'Aucun fichier ouvert';
                saveBtn.disabled = true;
                saveAsBtn.disabled = true;
                languageSelect.disabled = true;
                formatBtn.disabled = true;
            }
        }
        
        renderTabs();
    }
    
    // Mettre à jour la numérotation des lignes
    function updateLineNumbers() {
        const lines = codeEditor.value.split('\n');
        const numbers = lines.map((_, index) => index + 1).join('\n');
        lineNumbers.textContent = numbers;
        
        // Synchroniser le scroll
        lineNumbers.scrollTop = codeEditor.scrollTop;
    }
    
    // Mettre à jour le statut
    function updateStatus() {
        const activeTab = openTabs.find(tab => tab.id === activeTabId);
        if (!activeTab) return;
        
        // Position du curseur
        const cursorPos = codeEditor.selectionStart;
        const textBeforeCursor = codeEditor.value.substring(0, cursorPos);
        const line = textBeforeCursor.split('\n').length;
        const column = textBeforeCursor.split('\n').pop().length + 1;
        
        cursorPosition.textContent = `Ligne ${line}, Colonne ${column}`;
        
        // Sélection
        const selectionLength = codeEditor.selectionEnd - codeEditor.selectionStart;
        if (selectionLength > 0) {
            selectionInfo.textContent = `${selectionLength} caractère(s) sélectionné(s)`;
        } else {
            selectionInfo.textContent = '';
        }
        
        // Taille du fichier
        fileSize.textContent = AgentOne.utils.formatFileSize(new Blob([codeEditor.value]).size);
        
        // Statut de modification
        activeTab.modified = codeEditor.value !== activeTab.originalContent;
        modificationStatus.textContent = activeTab.modified ? 'Modifié' : 'Non modifié';
        
        renderTabs();
    }
    
    // Sauvegarder le fichier actif
    async function saveActiveFile() {
        const activeTab = openTabs.find(tab => tab.id === activeTabId);
        if (!activeTab) return;
        
        try {
            const result = await AgentOne.api.post('files.php', {
                action: 'createFile',
                path: activeTab.path,
                content: codeEditor.value
            });
            
            if (result.success) {
                activeTab.originalContent = codeEditor.value;
                activeTab.modified = false;
                updateStatus();
                AgentOne.ui.showNotification(`Fichier "${activeTab.name}" sauvegardé`, 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Événements de l'éditeur
    codeEditor.addEventListener('input', () => {
        updateLineNumbers();
        updateStatus();
    });
    
    codeEditor.addEventListener('scroll', () => {
        lineNumbers.scrollTop = codeEditor.scrollTop;
    });
    
    codeEditor.addEventListener('keyup', updateStatus);
    codeEditor.addEventListener('mouseup', updateStatus);
    
    // Raccourcis clavier
    codeEditor.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveActiveFile();
        }
        
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = codeEditor.selectionStart;
            const end = codeEditor.selectionEnd;
            
            // Insérer des espaces au lieu de tab
            const tabSize = 4; // À récupérer des paramètres
            const spaces = ' '.repeat(tabSize);
            
            codeEditor.value = codeEditor.value.substring(0, start) + spaces + codeEditor.value.substring(end);
            codeEditor.selectionStart = codeEditor.selectionEnd = start + tabSize;
            
            updateLineNumbers();
            updateStatus();
        }
    });
    
    // Événements des boutons
    saveBtn.addEventListener('click', saveActiveFile);
    
    document.getElementById('close-all-btn').addEventListener('click', () => {
        if (openTabs.some(tab => tab.modified)) {
            if (!confirm('Certains fichiers ont été modifiés. Fermer tous les onglets sans sauvegarder ?')) {
                return;
            }
        }
        
        openTabs = [];
        activeTabId = null;
        renderTabs();
        editorWelcome.style.display = 'flex';
        editorWorkspace.style.display = 'none';
        fileInfo.textContent = 'Aucun fichier ouvert';
        saveBtn.disabled = true;
        saveAsBtn.disabled = true;
        languageSelect.disabled = true;
        formatBtn.disabled = true;
    });
    
    document.getElementById('create-new-file').addEventListener('click', () => {
        const fileName = prompt('Nom du nouveau fichier :');
        if (fileName) {
            createNewFile(fileName);
        }
    });
    
    // Créer un nouveau fichier
    async function createNewFile(fileName) {
        try {
            const result = await AgentOne.api.post('files.php', {
                action: 'createFile',
                path: fileName,
                content: ''
            });
            
            if (result.success) {
                loadFileTree();
                openFile(fileName);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Initialisation
    loadFileTree();
};
</script>