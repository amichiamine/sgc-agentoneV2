<?php
/**
 * Vue Serveur - Contrôle du serveur PHP intégré
 */
?>

<div class="server-view">
    <div class="server-header">
        <div class="server-status" id="server-status">
            <div class="status-indicator" id="status-indicator"></div>
            <div class="status-info">
                <h3 id="status-title">Serveur Arrêté</h3>
                <p id="status-details">Le serveur PHP n'est pas en cours d'exécution</p>
            </div>
        </div>
        <div class="server-controls">
            <button class="btn btn-primary" id="start-server-btn">▶️ Démarrer</button>
            <button class="btn btn-secondary" id="stop-server-btn" disabled>⏹️ Arrêter</button>
            <button class="btn btn-secondary" id="restart-server-btn" disabled>🔄 Redémarrer</button>
        </div>
    </div>
    
    <div class="server-content">
        <div class="server-config card">
            <div class="card-header">
                <h4>⚙️ Configuration</h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="server-port">Port</label>
                    <input type="number" id="server-port" class="form-control" value="5000" min="1" max="65535">
                </div>
                <div class="form-group">
                    <label for="server-host">Hôte</label>
                    <input type="text" id="server-host" class="form-control" value="0.0.0.0">
                </div>
                <div class="form-group">
                    <label for="document-root">Racine du document</label>
                    <input type="text" id="document-root" class="form-control" value="." readonly>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="debug-mode">
                        <span class="checkmark"></span>
                        Mode debug (logs détaillés)
                    </label>
                </div>
                <button class="btn btn-primary" id="save-config-btn">💾 Sauvegarder la configuration</button>
            </div>
        </div>
        
        <div class="server-info card">
            <div class="card-header">
                <h4>📊 Informations</h4>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Version PHP :</span>
                        <span class="info-value" id="php-version">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Temps de fonctionnement :</span>
                        <span class="info-value" id="uptime">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">URL d'accès :</span>
                        <span class="info-value" id="server-url">-</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">PID :</span>
                        <span class="info-value" id="server-pid">-</span>
                    </div>
                </div>
                <div class="server-actions">
                    <button class="btn btn-secondary" id="open-browser-btn" disabled>🌐 Ouvrir dans le navigateur</button>
                    <button class="btn btn-secondary" id="copy-url-btn" disabled>📋 Copier l'URL</button>
                </div>
            </div>
        </div>
        
        <div class="server-logs card">
            <div class="card-header">
                <h4>📝 Logs du serveur</h4>
                <div class="logs-controls">
                    <button class="btn btn-secondary btn-sm" id="refresh-logs-btn">🔄 Actualiser</button>
                    <button class="btn btn-secondary btn-sm" id="clear-logs-btn">🗑️ Effacer</button>
                    <button class="btn btn-secondary btn-sm" id="download-logs-btn">⬇️ Télécharger</button>
                </div>
            </div>
            <div class="card-body">
                <div class="logs-container" id="logs-container">
                    <div class="no-logs">Aucun log disponible</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.server-view {
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 16px;
    gap: 16px;
    overflow-y: auto;
}

.server-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: var(--bg-secondary);
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.server-status {
    display: flex;
    align-items: center;
    gap: 16px;
}

.status-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--error-color);
    position: relative;
    animation: pulse-red 2s infinite;
}

.status-indicator.running {
    background: var(--success-color);
    animation: pulse-green 2s infinite;
}

.status-indicator.starting {
    background: var(--warning-color);
    animation: pulse-yellow 2s infinite;
}

@keyframes pulse-red {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes pulse-green {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

@keyframes pulse-yellow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.status-info h3 {
    margin: 0;
    font-size: 1.2rem;
    color: var(--text-primary);
}

.status-info p {
    margin: 4px 0 0 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.server-controls {
    display: flex;
    gap: 8px;
}

.server-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto 1fr;
    gap: 16px;
    flex: 1;
}

.server-config {
    grid-column: 1;
}

.server-info {
    grid-column: 2;
}

.server-logs {
    grid-column: 1 / -1;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color);
}

.info-label {
    font-weight: 500;
    color: var(--text-secondary);
}

.info-value {
    font-family: 'JetBrains Mono', monospace;
    color: var(--accent-primary);
    font-size: 0.9rem;
}

.server-actions {
    display: flex;
    gap: 8px;
}

.logs-controls {
    display: flex;
    gap: 4px;
}

.logs-container {
    height: 300px;
    overflow-y: auto;
    background: var(--bg-tertiary);
    border-radius: 6px;
    padding: 12px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.85rem;
    line-height: 1.4;
}

.no-logs {
    text-align: center;
    color: var(--text-secondary);
    font-style: italic;
    padding: 40px;
}

.log-entry {
    margin-bottom: 4px;
    word-wrap: break-word;
}

.log-timestamp {
    color: var(--text-secondary);
}

.log-level-info {
    color: var(--accent-primary);
}

.log-level-warning {
    color: var(--warning-color);
}

.log-level-error {
    color: var(--error-color);
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 0.9rem;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 8px;
}

@media (max-width: 768px) {
    .server-header {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
    
    .server-content {
        grid-template-columns: 1fr;
    }
    
    .server-config,
    .server-info {
        grid-column: 1;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .server-controls,
    .server-actions {
        flex-direction: column;
    }
}
</style>

<script>
window.initView = function() {
    const statusIndicator = document.getElementById('status-indicator');
    const statusTitle = document.getElementById('status-title');
    const statusDetails = document.getElementById('status-details');
    const startBtn = document.getElementById('start-server-btn');
    const stopBtn = document.getElementById('stop-server-btn');
    const restartBtn = document.getElementById('restart-server-btn');
    const serverPort = document.getElementById('server-port');
    const serverHost = document.getElementById('server-host');
    const debugMode = document.getElementById('debug-mode');
    const saveConfigBtn = document.getElementById('save-config-btn');
    const phpVersion = document.getElementById('php-version');
    const uptime = document.getElementById('uptime');
    const serverUrl = document.getElementById('server-url');
    const serverPid = document.getElementById('server-pid');
    const openBrowserBtn = document.getElementById('open-browser-btn');
    const copyUrlBtn = document.getElementById('copy-url-btn');
    const logsContainer = document.getElementById('logs-container');
    const refreshLogsBtn = document.getElementById('refresh-logs-btn');
    const clearLogsBtn = document.getElementById('clear-logs-btn');
    const downloadLogsBtn = document.getElementById('download-logs-btn');
    
    let serverStartTime = null;
    let uptimeInterval = null;
    
    // Vérifier le statut du serveur
    async function checkServerStatus() {
        try {
            const result = await AgentOne.api.post('server.php', {
                action: 'serverStatus'
            });
            
            if (result.success) {
                updateServerStatus(result.data);
            } else {
                updateServerStatus({ status: 'stopped' });
            }
        } catch (error) {
            updateServerStatus({ status: 'error', error: error.message });
        }
    }
    
    // Mettre à jour l'affichage du statut
    function updateServerStatus(data) {
        const isRunning = data.status === 'running';
        const isStarting = data.status === 'starting';
        const isError = data.status === 'error';
        
        // Indicateur visuel
        statusIndicator.className = 'status-indicator';
        if (isRunning) {
            statusIndicator.classList.add('running');
            statusTitle.textContent = 'Serveur En Marche';
            statusDetails.textContent = `Serveur PHP actif sur ${data.host || '0.0.0.0'}:${data.port || 5000}`;
        } else if (isStarting) {
            statusIndicator.classList.add('starting');
            statusTitle.textContent = 'Démarrage...';
            statusDetails.textContent = 'Le serveur PHP est en cours de démarrage';
        } else if (isError) {
            statusTitle.textContent = 'Erreur Serveur';
            statusDetails.textContent = data.error || 'Erreur inconnue';
        } else {
            statusTitle.textContent = 'Serveur Arrêté';
            statusDetails.textContent = 'Le serveur PHP n\'est pas en cours d\'exécution';
        }
        
        // Boutons
        startBtn.disabled = isRunning || isStarting;
        stopBtn.disabled = !isRunning;
        restartBtn.disabled = !isRunning;
        openBrowserBtn.disabled = !isRunning;
        copyUrlBtn.disabled = !isRunning;
        
        // Informations
        if (isRunning) {
            const url = `http://${data.host === '0.0.0.0' ? 'localhost' : data.host}:${data.port}`;
            serverUrl.textContent = url;
            serverPid.textContent = data.pid || 'N/A';
            
            if (!serverStartTime) {
                serverStartTime = new Date();
                startUptimeCounter();
            }
        } else {
            serverUrl.textContent = '-';
            serverPid.textContent = '-';
            uptime.textContent = '-';
            
            if (uptimeInterval) {
                clearInterval(uptimeInterval);
                uptimeInterval = null;
                serverStartTime = null;
            }
        }
        
        // Configuration
        if (data.port) serverPort.value = data.port;
        if (data.host) serverHost.value = data.host;
        if (data.debug !== undefined) debugMode.checked = data.debug;
    }
    
    // Compteur de temps de fonctionnement
    function startUptimeCounter() {
        if (uptimeInterval) clearInterval(uptimeInterval);
        
        uptimeInterval = setInterval(() => {
            if (serverStartTime) {
                const now = new Date();
                const diff = now - serverStartTime;
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                uptime.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }
    
    // Démarrer le serveur
    async function startServer() {
        try {
            startBtn.disabled = true;
            startBtn.textContent = '⏳ Démarrage...';
            
            const result = await AgentOne.api.post('server.php', {
                action: 'startServer',
                port: parseInt(serverPort.value),
                host: serverHost.value,
                debug: debugMode.checked
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Serveur démarré avec succès', 'success');
                setTimeout(checkServerStatus, 2000);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        } finally {
            startBtn.disabled = false;
            startBtn.textContent = '▶️ Démarrer';
        }
    }
    
    // Arrêter le serveur
    async function stopServer() {
        try {
            stopBtn.disabled = true;
            stopBtn.textContent = '⏳ Arrêt...';
            
            const result = await AgentOne.api.post('server.php', {
                action: 'stopServer'
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Serveur arrêté', 'success');
                setTimeout(checkServerStatus, 1000);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        } finally {
            stopBtn.disabled = false;
            stopBtn.textContent = '⏹️ Arrêter';
        }
    }
    
    // Redémarrer le serveur
    async function restartServer() {
        try {
            restartBtn.disabled = true;
            restartBtn.textContent = '⏳ Redémarrage...';
            
            const result = await AgentOne.api.post('server.php', {
                action: 'restartServer'
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Serveur redémarré', 'success');
                setTimeout(checkServerStatus, 3000);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        } finally {
            restartBtn.disabled = false;
            restartBtn.textContent = '🔄 Redémarrer';
        }
    }
    
    // Sauvegarder la configuration
    async function saveConfig() {
        try {
            const result = await AgentOne.api.post('server.php', {
                action: 'saveConfig',
                config: {
                    port: parseInt(serverPort.value),
                    host: serverHost.value,
                    debug: debugMode.checked
                }
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Configuration sauvegardée', 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Charger les logs
    async function loadLogs() {
        try {
            const result = await AgentOne.api.post('server.php', {
                action: 'serverLogs'
            });
            
            if (result.success && result.data.logs) {
                renderLogs(result.data.logs);
            } else {
                logsContainer.innerHTML = '<div class="no-logs">Aucun log disponible</div>';
            }
        } catch (error) {
            logsContainer.innerHTML = `<div class="no-logs">Erreur : ${error.message}</div>`;
        }
    }
    
    // Rendu des logs
    function renderLogs(logs) {
        if (logs.length === 0) {
            logsContainer.innerHTML = '<div class="no-logs">Aucun log disponible</div>';
            return;
        }
        
        logsContainer.innerHTML = logs.map(log => {
            const parts = log.match(/^\[([^\]]+)\]\s*(\w+):\s*(.+)$/);
            if (parts) {
                const [, timestamp, level, message] = parts;
                return `<div class="log-entry">
                    <span class="log-timestamp">[${timestamp}]</span>
                    <span class="log-level-${level.toLowerCase()}">${level}:</span>
                    <span class="log-message">${message}</span>
                </div>`;
            } else {
                return `<div class="log-entry">${log}</div>`;
            }
        }).join('');
        
        logsContainer.scrollTop = logsContainer.scrollHeight;
    }
    
    // Ouvrir dans le navigateur
    function openInBrowser() {
        const url = serverUrl.textContent;
        if (url && url !== '-') {
            window.open(url, '_blank');
        }
    }
    
    // Copier l'URL
    async function copyUrl() {
        const url = serverUrl.textContent;
        if (url && url !== '-') {
            try {
                await AgentOne.utils.copyToClipboard(url);
            } catch (error) {
                AgentOne.ui.showNotification('Erreur lors de la copie', 'error');
            }
        }
    }
    
    // Télécharger les logs
    function downloadLogs() {
        const content = logsContainer.textContent;
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `server-logs-${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
    
    // Événements
    startBtn.addEventListener('click', startServer);
    stopBtn.addEventListener('click', stopServer);
    restartBtn.addEventListener('click', restartServer);
    saveConfigBtn.addEventListener('click', saveConfig);
    openBrowserBtn.addEventListener('click', openInBrowser);
    copyUrlBtn.addEventListener('click', copyUrl);
    refreshLogsBtn.addEventListener('click', loadLogs);
    clearLogsBtn.addEventListener('click', () => {
        logsContainer.innerHTML = '<div class="no-logs">Logs effacés</div>';
    });
    downloadLogsBtn.addEventListener('click', downloadLogs);
    
    // Obtenir la version PHP
    phpVersion.textContent = '<?= PHP_VERSION ?>';
    
    // Initialisation
    checkServerStatus();
    loadLogs();
    
    // Vérification périodique du statut
    setInterval(checkServerStatus, 5000);
};
</script>