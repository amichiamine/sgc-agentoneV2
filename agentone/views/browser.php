<?php
/**
 * Vue Navigateur - Navigateur web intégré avec onglets
 */
?>

<div class="browser-view">
    <div class="browser-header">
        <div class="browser-nav">
            <button class="nav-btn" id="back-btn" disabled>◀️</button>
            <button class="nav-btn" id="forward-btn" disabled>▶️</button>
            <button class="nav-btn" id="refresh-btn">🔄</button>
            <button class="nav-btn" id="home-btn">🏠</button>
        </div>
        
        <div class="address-bar-container">
            <input type="text" id="address-bar" class="address-bar" placeholder="Entrez une URL ou recherchez...">
            <button class="nav-btn" id="go-btn">🔍</button>
        </div>
        
        <div class="browser-tools">
            <button class="nav-btn" id="new-tab-btn" title="Nouvel onglet">➕</button>
            <button class="nav-btn" id="devtools-btn" title="Outils développeur">🔧</button>
        </div>
    </div>
    
    <div class="tabs-container" id="tabs-container">
        <div class="tab active" data-id="1">
            <span class="tab-title">Accueil</span>
            <button class="tab-close">×</button>
        </div>
    </div>
    
    <div class="browser-content">
        <iframe id="browser-frame" src="http://localhost:5000" sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-downloads"></iframe>
    </div>
    
    <div class="browser-footer">
        <div class="status-info">
            <span id="loading-status">Prêt</span>
            <span id="security-status">🔒 Sécurisé</span>
        </div>
        <div class="page-info">
            <span id="current-url">http://localhost:5000</span>
            <span id="page-size">-</span>
        </div>
    </div>
</div>

<!-- Modal des outils développeur -->
<div id="devtools-modal" class="modal" style="display: none;">
    <div class="modal-content devtools-content">
        <div class="modal-header">
            <h3>🔧 Outils Développeur</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="devtools-tabs">
            <button class="devtools-tab active" data-tab="console">Console</button>
            <button class="devtools-tab" data-tab="network">Réseau</button>
            <button class="devtools-tab" data-tab="storage">Stockage</button>
        </div>
        <div class="devtools-content-area">
            <div class="devtools-panel active" id="console-panel">
                <div class="console-output" id="console-output">
                    <div class="console-message">Console JavaScript - SGC-AgentOne</div>
                </div>
                <div class="console-input-container">
                    <input type="text" id="console-input" placeholder="Tapez du JavaScript...">
                    <button id="console-execute">▶️</button>
                </div>
            </div>
            <div class="devtools-panel" id="network-panel">
                <div class="network-requests" id="network-requests">
                    <div class="no-requests">Aucune requête réseau</div>
                </div>
            </div>
            <div class="devtools-panel" id="storage-panel">
                <div class="storage-info">
                    <h4>LocalStorage</h4>
                    <div id="localstorage-content">Vide</div>
                    <h4>SessionStorage</h4>
                    <div id="sessionstorage-content">Vide</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.browser-view {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: var(--bg-primary);
}

.browser-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.browser-nav {
    display: flex;
    gap: 4px;
}

.nav-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    background: var(--accent-secondary);
    color: var(--text-primary);
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.nav-btn:hover:not(:disabled) {
    background: var(--bg-tertiary);
    transform: translateY(-1px);
}

.nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.address-bar-container {
    flex: 1;
    display: flex;
    gap: 8px;
    max-width: 600px;
}

.address-bar {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 20px;
    background: var(--bg-tertiary);
    color: var(--text-primary);
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s ease;
}

.address-bar:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 2px rgba(26, 184, 184, 0.2);
}

.browser-tools {
    display: flex;
    gap: 4px;
}

.tabs-container {
    display: flex;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    overflow-x: auto;
    padding: 0 8px;
}

.tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--accent-secondary);
    border-radius: 8px 8px 0 0;
    cursor: pointer;
    font-size: 0.9rem;
    margin-right: 4px;
    min-width: 120px;
    max-width: 200px;
    transition: all 0.2s ease;
}

.tab.active {
    background: var(--bg-primary);
    color: var(--accent-primary);
    border-bottom: 2px solid var(--accent-primary);
}

.tab:hover:not(.active) {
    background: var(--bg-tertiary);
}

.tab-title {
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
    width: 20px;
    height: 20px;
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

.browser-content {
    flex: 1;
    position: relative;
    overflow: hidden;
}

#browser-frame {
    width: 100%;
    height: 100%;
    border: none;
    background: white;
}

.browser-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 12px;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-color);
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.status-info,
.page-info {
    display: flex;
    gap: 16px;
}

.devtools-content {
    width: 90%;
    max-width: 1000px;
    height: 80vh;
    max-height: 600px;
}

.devtools-tabs {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 16px;
}

.devtools-tab {
    padding: 8px 16px;
    border: none;
    background: none;
    color: var(--text-secondary);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
}

.devtools-tab.active {
    color: var(--accent-primary);
    border-bottom-color: var(--accent-primary);
}

.devtools-tab:hover:not(.active) {
    color: var(--text-primary);
}

.devtools-content-area {
    height: calc(100% - 60px);
    overflow: hidden;
}

.devtools-panel {
    height: 100%;
    display: none;
    flex-direction: column;
}

.devtools-panel.active {
    display: flex;
}

.console-output {
    flex: 1;
    overflow-y: auto;
    background: var(--bg-tertiary);
    border-radius: 6px;
    padding: 12px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.85rem;
    line-height: 1.4;
    margin-bottom: 12px;
}

.console-message {
    margin-bottom: 4px;
    word-wrap: break-word;
}

.console-error {
    color: var(--error-color);
}

.console-warning {
    color: var(--warning-color);
}

.console-info {
    color: var(--accent-primary);
}

.console-input-container {
    display: flex;
    gap: 8px;
}

.console-input-container input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-tertiary);
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    outline: none;
}

.network-requests {
    height: 100%;
    overflow-y: auto;
    background: var(--bg-tertiary);
    border-radius: 6px;
    padding: 12px;
}

.no-requests {
    text-align: center;
    color: var(--text-secondary);
    font-style: italic;
    padding: 40px;
}

.storage-info {
    height: 100%;
    overflow-y: auto;
    background: var(--bg-tertiary);
    border-radius: 6px;
    padding: 12px;
}

.storage-info h4 {
    margin: 0 0 8px 0;
    color: var(--accent-primary);
}

@media (max-width: 768px) {
    .browser-header {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .address-bar-container {
        order: 3;
        width: 100%;
        max-width: none;
    }
    
    .browser-nav,
    .browser-tools {
        flex: 1;
    }
    
    .browser-tools {
        justify-content: flex-end;
    }
    
    .tab {
        min-width: 100px;
        padding: 6px 12px;
    }
    
    .browser-footer {
        flex-direction: column;
        gap: 4px;
        text-align: center;
    }
}
</style>

<script>
window.initView = function() {
    const browserFrame = document.getElementById('browser-frame');
    const addressBar = document.getElementById('address-bar');
    const backBtn = document.getElementById('back-btn');
    const forwardBtn = document.getElementById('forward-btn');
    const refreshBtn = document.getElementById('refresh-btn');
    const homeBtn = document.getElementById('home-btn');
    const goBtn = document.getElementById('go-btn');
    const newTabBtn = document.getElementById('new-tab-btn');
    const devtoolsBtn = document.getElementById('devtools-btn');
    const tabsContainer = document.getElementById('tabs-container');
    const loadingStatus = document.getElementById('loading-status');
    const currentUrlEl = document.getElementById('current-url');
    const devtoolsModal = document.getElementById('devtools-modal');
    
    let tabs = [
        { id: 1, title: 'Accueil', url: 'http://localhost:5000', active: true }
    ];
    let tabCounter = 1;
    let currentTabId = 1;
    
    // Charger une URL
    function loadUrl(url) {
        if (!url) return;
        
        // Ajouter http:// si nécessaire
        if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('file://')) {
            if (url.includes('.') && !url.includes(' ')) {
                url = 'http://' + url;
            } else {
                url = 'https://www.google.com/search?q=' + encodeURIComponent(url);
            }
        }
        
        loadingStatus.textContent = 'Chargement...';
        browserFrame.src = url;
        addressBar.value = url;
        currentUrlEl.textContent = url;
        
        // Mettre à jour l'onglet actuel
        const currentTab = tabs.find(tab => tab.id === currentTabId);
        if (currentTab) {
            currentTab.url = url;
            currentTab.title = 'Chargement...';
            renderTabs();
        }
    }
    
    // Rendu des onglets
    function renderTabs() {
        tabsContainer.innerHTML = tabs.map(tab => `
            <div class="tab ${tab.active ? 'active' : ''}" data-id="${tab.id}">
                <span class="tab-title">${tab.title}</span>
                ${tabs.length > 1 ? '<button class="tab-close">×</button>' : ''}
            </div>
        `).join('');
        
        // Événements des onglets
        tabsContainer.querySelectorAll('.tab').forEach(tabEl => {
            const tabId = parseInt(tabEl.dataset.id);
            
            tabEl.addEventListener('click', (e) => {
                if (!e.target.classList.contains('tab-close')) {
                    switchToTab(tabId);
                }
            });
            
            const closeBtn = tabEl.querySelector('.tab-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    closeTab(tabId);
                });
            }
        });
    }
    
    // Basculer vers un onglet
    function switchToTab(tabId) {
        tabs.forEach(tab => tab.active = tab.id === tabId);
        currentTabId = tabId;
        
        const tab = tabs.find(t => t.id === tabId);
        if (tab) {
            loadUrl(tab.url);
        }
        
        renderTabs();
    }
    
    // Fermer un onglet
    function closeTab(tabId) {
        if (tabs.length <= 1) return;
        
        const tabIndex = tabs.findIndex(tab => tab.id === tabId);
        tabs.splice(tabIndex, 1);
        
        // Si l'onglet fermé était actif, activer le précédent ou le suivant
        if (currentTabId === tabId) {
            const newActiveTab = tabs[Math.max(0, tabIndex - 1)];
            switchToTab(newActiveTab.id);
        }
        
        renderTabs();
    }
    
    // Nouvel onglet
    function createNewTab() {
        tabCounter++;
        const newTab = {
            id: tabCounter,
            title: 'Nouvel onglet',
            url: 'about:blank',
            active: false
        };
        
        tabs.push(newTab);
        switchToTab(newTab.id);
    }
    
    // Gestion du chargement de l'iframe
    browserFrame.addEventListener('load', () => {
        loadingStatus.textContent = 'Prêt';
        
        try {
            const frameDoc = browserFrame.contentDocument || browserFrame.contentWindow.document;
            const title = frameDoc.title || 'Page sans titre';
            
            // Mettre à jour le titre de l'onglet
            const currentTab = tabs.find(tab => tab.id === currentTabId);
            if (currentTab) {
                currentTab.title = title.length > 20 ? title.substring(0, 20) + '...' : title;
                renderTabs();
            }
            
            // Mettre à jour l'URL si elle a changé (redirections)
            const currentUrl = browserFrame.contentWindow.location.href;
            if (currentUrl !== 'about:blank') {
                addressBar.value = currentUrl;
                currentUrlEl.textContent = currentUrl;
                if (currentTab) {
                    currentTab.url = currentUrl;
                }
            }
        } catch (e) {
            // Cross-origin, on ne peut pas accéder au contenu
            loadingStatus.textContent = 'Chargé (accès restreint)';
        }
    });
    
    browserFrame.addEventListener('error', () => {
        loadingStatus.textContent = 'Erreur de chargement';
    });
    
    // Événements de navigation
    addressBar.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            loadUrl(addressBar.value);
        }
    });
    
    goBtn.addEventListener('click', () => {
        loadUrl(addressBar.value);
    });
    
    backBtn.addEventListener('click', () => {
        try {
            browserFrame.contentWindow.history.back();
        } catch (e) {
            console.warn('Impossible de naviguer en arrière');
        }
    });
    
    forwardBtn.addEventListener('click', () => {
        try {
            browserFrame.contentWindow.history.forward();
        } catch (e) {
            console.warn('Impossible de naviguer en avant');
        }
    });
    
    refreshBtn.addEventListener('click', () => {
        browserFrame.contentWindow.location.reload();
    });
    
    homeBtn.addEventListener('click', () => {
        loadUrl('http://localhost:5000');
    });
    
    newTabBtn.addEventListener('click', createNewTab);
    
    // Outils développeur
    devtoolsBtn.addEventListener('click', () => {
        devtoolsModal.style.display = 'flex';
    });
    
    // Gestion du modal des outils développeur
    devtoolsModal.addEventListener('click', (e) => {
        if (e.target === devtoolsModal || e.target.classList.contains('modal-close')) {
            devtoolsModal.style.display = 'none';
        }
    });
    
    // Onglets des outils développeur
    document.querySelectorAll('.devtools-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const tabName = tab.dataset.tab;
            
            // Activer l'onglet
            document.querySelectorAll('.devtools-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Afficher le panneau correspondant
            document.querySelectorAll('.devtools-panel').forEach(panel => panel.classList.remove('active'));
            document.getElementById(tabName + '-panel').classList.add('active');
        });
    });
    
    // Console JavaScript
    const consoleInput = document.getElementById('console-input');
    const consoleOutput = document.getElementById('console-output');
    const consoleExecute = document.getElementById('console-execute');
    
    function addConsoleMessage(message, type = 'info') {
        const messageEl = document.createElement('div');
        messageEl.className = `console-message console-${type}`;
        messageEl.textContent = message;
        consoleOutput.appendChild(messageEl);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }
    
    function executeConsoleCommand() {
        const command = consoleInput.value.trim();
        if (!command) return;
        
        addConsoleMessage('> ' + command, 'info');
        
        try {
            // Exécuter dans le contexte de l'iframe si possible
            let result;
            try {
                result = browserFrame.contentWindow.eval(command);
            } catch (e) {
                // Si cross-origin, exécuter dans le contexte local
                result = eval(command);
            }
            
            addConsoleMessage(String(result), 'info');
        } catch (error) {
            addConsoleMessage('Erreur: ' + error.message, 'error');
        }
        
        consoleInput.value = '';
    }
    
    consoleExecute.addEventListener('click', executeConsoleCommand);
    consoleInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            executeConsoleCommand();
        }
    });
    
    // Initialisation
    renderTabs();
    
    // Vérification périodique du serveur
    setInterval(async () => {
        try {
            const response = await fetch('http://localhost:5000', { method: 'HEAD' });
            if (response.ok) {
                loadingStatus.textContent = 'Serveur actif';
            }
        } catch (e) {
            loadingStatus.textContent = 'Serveur inactif';
        }
    }, 10000);
};
</script>