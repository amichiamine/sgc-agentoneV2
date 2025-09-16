<?php
/**
 * Vue Paramètres - Configuration de l'application
 */
?>

<div class="settings-view">
    <div class="settings-header">
        <h2>⚙️ Paramètres et Configuration</h2>
        <p class="settings-subtitle">Personnalisez SGC-AgentOne selon vos préférences</p>
    </div>
    
    <div class="settings-content">
        <div class="settings-tabs" id="settings-tabs">
            <button class="settings-tab active" data-tab="appearance">🎨 Apparence</button>
            <button class="settings-tab" data-tab="server">🖥️ Serveur</button>
            <button class="settings-tab" data-tab="editor">📝 Éditeur</button>
            <button class="settings-tab" data-tab="security">🔒 Sécurité</button>
            <button class="settings-tab" data-tab="advanced">⚙️ Avancé</button>
        </div>
        
        <div class="settings-panels">
            <!-- Panneau Apparence -->
            <div class="settings-panel active" id="appearance-panel">
                <div class="panel-section">
                    <h3>🎨 Personnalisation de l'interface</h3>
                    
                    <div class="form-group">
                        <label for="app-title">Titre de l'application</label>
                        <input type="text" id="app-title" class="form-control" value="SGC-AgentOne">
                    </div>
                    
                    <div class="form-group">
                        <label for="app-subtitle">Sous-titre</label>
                        <input type="text" id="app-subtitle" class="form-control" value="v3.0 - Architecture Modulaire">
                    </div>
                    
                    <div class="form-group">
                        <label>Mode sombre</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="dark-mode" checked>
                            <label for="dark-mode" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Couleurs du thème</label>
                        <div class="color-palette">
                            <div class="color-item">
                                <label>Primaire</label>
                                <input type="color" id="primary-color" value="#1ab8b8">
                            </div>
                            <div class="color-item">
                                <label>Secondaire</label>
                                <input type="color" id="secondary-color" value="#2d3a45">
                            </div>
                            <div class="color-item">
                                <label>Accent</label>
                                <input type="color" id="accent-color" value="#1ab8b8">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="font-size">Taille de police</label>
                        <select id="font-size" class="form-control">
                            <option value="small">Petite</option>
                            <option value="medium" selected>Moyenne</option>
                            <option value="large">Grande</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Panneau Serveur -->
            <div class="settings-panel" id="server-panel">
                <div class="panel-section">
                    <h3>🖥️ Configuration du serveur</h3>
                    
                    <div class="form-group">
                        <label for="server-port">Port du serveur</label>
                        <input type="number" id="server-port" class="form-control" value="5000" min="1" max="65535">
                        <small class="form-help">Port sur lequel le serveur PHP sera démarré</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="server-host">Adresse d'écoute</label>
                        <select id="server-host" class="form-control">
                            <option value="0.0.0.0">0.0.0.0 (Toutes les interfaces)</option>
                            <option value="127.0.0.1">127.0.0.1 (Local uniquement)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Démarrage automatique</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="auto-start">
                            <label for="auto-start" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <small class="form-help">Démarre automatiquement le serveur au chargement</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Mode debug</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="debug-mode">
                            <label for="debug-mode" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <small class="form-help">Active les logs détaillés pour le débogage</small>
                    </div>
                </div>
            </div>
            
            <!-- Panneau Éditeur -->
            <div class="settings-panel" id="editor-panel">
                <div class="panel-section">
                    <h3>📝 Configuration de l'éditeur</h3>
                    
                    <div class="form-group">
                        <label for="editor-theme">Thème de l'éditeur</label>
                        <select id="editor-theme" class="form-control">
                            <option value="dark">Sombre</option>
                            <option value="light">Clair</option>
                            <option value="monokai">Monokai</option>
                            <option value="github">GitHub</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="editor-font-size">Taille de police</label>
                        <input type="range" id="editor-font-size" min="12" max="24" value="14" class="range-input">
                        <span class="range-value">14px</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="tab-size">Taille des tabulations</label>
                        <select id="tab-size" class="form-control">
                            <option value="2">2 espaces</option>
                            <option value="4" selected>4 espaces</option>
                            <option value="8">8 espaces</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Numérotation des lignes</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="line-numbers" checked>
                            <label for="line-numbers" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Sauvegarde automatique</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="auto-save" checked>
                            <label for="auto-save" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <small class="form-help">Sauvegarde automatiquement après 2 secondes d'inactivité</small>
                    </div>
                </div>
            </div>
            
            <!-- Panneau Sécurité -->
            <div class="settings-panel" id="security-panel">
                <div class="panel-section">
                    <h3>🔒 Paramètres de sécurité</h3>
                    
                    <div class="form-group">
                        <label>Mode Blind-Exec</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="blind-exec">
                            <label for="blind-exec" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <small class="form-help">⚠️ Permet l'exécution automatique des commandes sans confirmation</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="max-file-size">Taille maximale des fichiers (MB)</label>
                        <input type="number" id="max-file-size" class="form-control" value="50" min="1" max="500">
                    </div>
                    
                    <div class="form-group">
                        <label>Journalisation des actions</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="action-logging" checked>
                            <label for="action-logging" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <small class="form-help">Enregistre toutes les actions dans les logs</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="session-timeout">Timeout de session (minutes)</label>
                        <input type="number" id="session-timeout" class="form-control" value="60" min="5" max="1440">
                    </div>
                </div>
            </div>
            
            <!-- Panneau Avancé -->
            <div class="settings-panel" id="advanced-panel">
                <div class="panel-section">
                    <h3>⚙️ Paramètres avancés</h3>
                    
                    <div class="form-group">
                        <label for="memory-limit">Limite mémoire PHP (MB)</label>
                        <input type="number" id="memory-limit" class="form-control" value="256" min="64" max="1024">
                    </div>
                    
                    <div class="form-group">
                        <label for="execution-time">Temps d'exécution max (secondes)</label>
                        <input type="number" id="execution-time" class="form-control" value="300" min="30" max="3600">
                    </div>
                    
                    <div class="form-group">
                        <label>Cache des fichiers</label>
                        <div class="toggle-switch">
                            <input type="checkbox" id="file-cache" checked>
                            <label for="file-cache" class="toggle-label">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <small class="form-help">Met en cache les fichiers fréquemment utilisés</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="backup-frequency">Fréquence de sauvegarde</label>
                        <select id="backup-frequency" class="form-control">
                            <option value="never">Jamais</option>
                            <option value="daily">Quotidienne</option>
                            <option value="weekly" selected>Hebdomadaire</option>
                            <option value="monthly">Mensuelle</option>
                        </select>
                    </div>
                </div>
                
                <div class="panel-section">
                    <h3>🗑️ Maintenance</h3>
                    
                    <div class="maintenance-actions">
                        <button class="btn btn-secondary" id="clear-cache-btn">🗑️ Vider le cache</button>
                        <button class="btn btn-secondary" id="clear-logs-btn">📝 Effacer les logs</button>
                        <button class="btn btn-secondary" id="reset-settings-btn">🔄 Réinitialiser</button>
                        <button class="btn btn-danger" id="factory-reset-btn">⚠️ Reset complet</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="settings-footer">
            <button class="btn btn-secondary" id="cancel-settings-btn">Annuler</button>
            <button class="btn btn-secondary" id="export-settings-btn">📤 Exporter</button>
            <button class="btn btn-secondary" id="import-settings-btn">📥 Importer</button>
            <button class="btn btn-primary" id="save-settings-btn">💾 Sauvegarder</button>
        </div>
    </div>
</div>

<input type="file" id="import-file-input" accept=".json" style="display: none;">

<style>
.settings-view {
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 16px;
    gap: 16px;
    overflow: hidden;
}

.settings-header {
    text-align: center;
    padding: 20px;
    background: var(--bg-secondary);
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.settings-header h2 {
    margin: 0 0 8px 0;
    color: var(--accent-primary);
    font-size: 1.8rem;
}

.settings-subtitle {
    margin: 0;
    color: var(--text-secondary);
    font-size: 1rem;
}

.settings-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.settings-tabs {
    display: flex;
    background: var(--bg-secondary);
    border-radius: 8px 8px 0 0;
    border: 1px solid var(--border-color);
    border-bottom: none;
    overflow-x: auto;
}

.settings-tab {
    flex: 1;
    padding: 12px 16px;
    border: none;
    background: none;
    color: var(--text-secondary);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
}

.settings-tab:hover {
    color: var(--text-primary);
    background: var(--accent-secondary);
}

.settings-tab.active {
    color: var(--accent-primary);
    border-bottom-color: var(--accent-primary);
    background: var(--bg-primary);
}

.settings-panels {
    flex: 1;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 0 0 8px 8px;
    overflow-y: auto;
}

.settings-panel {
    display: none;
    padding: 24px;
}

.settings-panel.active {
    display: block;
}

.panel-section {
    margin-bottom: 32px;
}

.panel-section h3 {
    margin: 0 0 20px 0;
    color: var(--accent-primary);
    font-size: 1.3rem;
    font-weight: 600;
    border-bottom: 2px solid var(--accent-primary);
    padding-bottom: 8px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-tertiary);
    color: var(--text-primary);
    font-size: 0.9rem;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 2px rgba(26, 184, 184, 0.2);
}

.form-help {
    display: block;
    margin-top: 4px;
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-style: italic;
}

.toggle-switch {
    display: inline-flex;
    align-items: center;
    margin-top: 4px;
}

.toggle-switch input[type="checkbox"] {
    display: none;
}

.toggle-label {
    position: relative;
    width: 50px;
    height: 24px;
    background: var(--accent-secondary);
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.toggle-slider {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s ease;
}

.toggle-switch input:checked + .toggle-label {
    background: var(--accent-primary);
}

.toggle-switch input:checked + .toggle-label .toggle-slider {
    transform: translateX(26px);
}

.color-palette {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
    margin-top: 8px;
}

.color-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.color-item label {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin: 0;
}

.color-item input[type="color"] {
    width: 60px;
    height: 40px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: none;
}

.range-input {
    width: 100%;
    margin: 8px 0;
}

.range-value {
    display: inline-block;
    margin-left: 8px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--accent-primary);
    font-size: 0.9rem;
}

.maintenance-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}

.settings-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

@media (max-width: 768px) {
    .settings-view {
        padding: 8px;
    }
    
    .settings-tabs {
        flex-direction: column;
    }
    
    .settings-tab {
        text-align: left;
        border-bottom: none;
        border-left: 3px solid transparent;
    }
    
    .settings-tab.active {
        border-left-color: var(--accent-primary);
        border-bottom-color: transparent;
    }
    
    .settings-panels {
        border-radius: 8px;
    }
    
    .settings-panel {
        padding: 16px;
    }
    
    .color-palette {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .maintenance-actions {
        grid-template-columns: 1fr;
    }
    
    .settings-footer {
        flex-direction: column;
    }
}
</style>

<script>
window.initView = function() {
    const settingsTabs = document.getElementById('settings-tabs');
    const saveBtn = document.getElementById('save-settings-btn');
    const cancelBtn = document.getElementById('cancel-settings-btn');
    const exportBtn = document.getElementById('export-settings-btn');
    const importBtn = document.getElementById('import-settings-btn');
    const importFileInput = document.getElementById('import-file-input');
    
    let currentSettings = {};
    let originalSettings = {};
    
    // Charger les paramètres
    async function loadSettings() {
        try {
            const result = await AgentOne.api.post('settings.php', {
                action: 'getSettings'
            });
            
            if (result.success) {
                currentSettings = result.data.settings;
                originalSettings = { ...currentSettings };
                applySettingsToForm();
            } else {
                // Paramètres par défaut
                currentSettings = getDefaultSettings();
                applySettingsToForm();
            }
        } catch (error) {
            console.error('Erreur chargement paramètres:', error);
            currentSettings = getDefaultSettings();
            applySettingsToForm();
        }
    }
    
    // Paramètres par défaut
    function getDefaultSettings() {
        return {
            appearance: {
                title: 'SGC-AgentOne',
                subtitle: 'v3.0 - Architecture Modulaire',
                darkMode: true,
                primaryColor: '#1ab8b8',
                secondaryColor: '#2d3a45',
                accentColor: '#1ab8b8',
                fontSize: 'medium'
            },
            server: {
                port: 5000,
                host: '0.0.0.0',
                autoStart: false,
                debugMode: false
            },
            editor: {
                theme: 'dark',
                fontSize: 14,
                tabSize: 4,
                lineNumbers: true,
                autoSave: true
            },
            security: {
                blindExec: false,
                maxFileSize: 50,
                actionLogging: true,
                sessionTimeout: 60
            },
            advanced: {
                memoryLimit: 256,
                executionTime: 300,
                fileCache: true,
                backupFrequency: 'weekly'
            }
        };
    }
    
    // Appliquer les paramètres au formulaire
    function applySettingsToForm() {
        // Apparence
        document.getElementById('app-title').value = currentSettings.appearance?.title || 'SGC-AgentOne';
        document.getElementById('app-subtitle').value = currentSettings.appearance?.subtitle || 'v3.0 - Architecture Modulaire';
        document.getElementById('dark-mode').checked = currentSettings.appearance?.darkMode !== false;
        document.getElementById('primary-color').value = currentSettings.appearance?.primaryColor || '#1ab8b8';
        document.getElementById('secondary-color').value = currentSettings.appearance?.secondaryColor || '#2d3a45';
        document.getElementById('accent-color').value = currentSettings.appearance?.accentColor || '#1ab8b8';
        document.getElementById('font-size').value = currentSettings.appearance?.fontSize || 'medium';
        
        // Serveur
        document.getElementById('server-port').value = currentSettings.server?.port || 5000;
        document.getElementById('server-host').value = currentSettings.server?.host || '0.0.0.0';
        document.getElementById('auto-start').checked = currentSettings.server?.autoStart || false;
        document.getElementById('debug-mode').checked = currentSettings.server?.debugMode || false;
        
        // Éditeur
        document.getElementById('editor-theme').value = currentSettings.editor?.theme || 'dark';
        document.getElementById('editor-font-size').value = currentSettings.editor?.fontSize || 14;
        document.getElementById('tab-size').value = currentSettings.editor?.tabSize || 4;
        document.getElementById('line-numbers').checked = currentSettings.editor?.lineNumbers !== false;
        document.getElementById('auto-save').checked = currentSettings.editor?.autoSave !== false;
        
        // Sécurité
        document.getElementById('blind-exec').checked = currentSettings.security?.blindExec || false;
        document.getElementById('max-file-size').value = currentSettings.security?.maxFileSize || 50;
        document.getElementById('action-logging').checked = currentSettings.security?.actionLogging !== false;
        document.getElementById('session-timeout').value = currentSettings.security?.sessionTimeout || 60;
        
        // Avancé
        document.getElementById('memory-limit').value = currentSettings.advanced?.memoryLimit || 256;
        document.getElementById('execution-time').value = currentSettings.advanced?.executionTime || 300;
        document.getElementById('file-cache').checked = currentSettings.advanced?.fileCache !== false;
        document.getElementById('backup-frequency').value = currentSettings.advanced?.backupFrequency || 'weekly';
        
        // Mettre à jour l'affichage des ranges
        updateRangeDisplays();
    }
    
    // Récupérer les paramètres du formulaire
    function getSettingsFromForm() {
        return {
            appearance: {
                title: document.getElementById('app-title').value,
                subtitle: document.getElementById('app-subtitle').value,
                darkMode: document.getElementById('dark-mode').checked,
                primaryColor: document.getElementById('primary-color').value,
                secondaryColor: document.getElementById('secondary-color').value,
                accentColor: document.getElementById('accent-color').value,
                fontSize: document.getElementById('font-size').value
            },
            server: {
                port: parseInt(document.getElementById('server-port').value),
                host: document.getElementById('server-host').value,
                autoStart: document.getElementById('auto-start').checked,
                debugMode: document.getElementById('debug-mode').checked
            },
            editor: {
                theme: document.getElementById('editor-theme').value,
                fontSize: parseInt(document.getElementById('editor-font-size').value),
                tabSize: parseInt(document.getElementById('tab-size').value),
                lineNumbers: document.getElementById('line-numbers').checked,
                autoSave: document.getElementById('auto-save').checked
            },
            security: {
                blindExec: document.getElementById('blind-exec').checked,
                maxFileSize: parseInt(document.getElementById('max-file-size').value),
                actionLogging: document.getElementById('action-logging').checked,
                sessionTimeout: parseInt(document.getElementById('session-timeout').value)
            },
            advanced: {
                memoryLimit: parseInt(document.getElementById('memory-limit').value),
                executionTime: parseInt(document.getElementById('execution-time').value),
                fileCache: document.getElementById('file-cache').checked,
                backupFrequency: document.getElementById('backup-frequency').value
            }
        };
    }
    
    // Sauvegarder les paramètres
    async function saveSettings() {
        try {
            const settings = getSettingsFromForm();
            
            const result = await AgentOne.api.post('settings.php', {
                action: 'saveSettings',
                settings: settings
            });
            
            if (result.success) {
                currentSettings = settings;
                originalSettings = { ...settings };
                AgentOne.ui.showNotification('Paramètres sauvegardés avec succès', 'success');
                
                // Appliquer les changements d'apparence immédiatement
                applyAppearanceChanges(settings.appearance);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Appliquer les changements d'apparence
    function applyAppearanceChanges(appearance) {
        // Mettre à jour les variables CSS
        document.documentElement.style.setProperty('--accent-primary', appearance.primaryColor);
        document.documentElement.style.setProperty('--accent-secondary', appearance.secondaryColor);
        
        // Mettre à jour le titre
        const headerTitle = document.querySelector('#header h1');
        if (headerTitle) {
            headerTitle.textContent = appearance.title;
        }
        
        // Mettre à jour le sous-titre
        const headerSubtitle = document.querySelector('#header small');
        if (headerSubtitle) {
            headerSubtitle.textContent = appearance.subtitle;
        }
        
        // Mode sombre/clair
        document.body.classList.toggle('light-mode', !appearance.darkMode);
    }
    
    // Mettre à jour l'affichage des ranges
    function updateRangeDisplays() {
        const editorFontSize = document.getElementById('editor-font-size');
        const rangeValue = document.querySelector('.range-value');
        
        if (editorFontSize && rangeValue) {
            rangeValue.textContent = editorFontSize.value + 'px';
            
            editorFontSize.addEventListener('input', () => {
                rangeValue.textContent = editorFontSize.value + 'px';
            });
        }
    }
    
    // Gestion des onglets
    settingsTabs.addEventListener('click', (e) => {
        if (e.target.classList.contains('settings-tab')) {
            const tabName = e.target.dataset.tab;
            
            // Activer l'onglet
            document.querySelectorAll('.settings-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            e.target.classList.add('active');
            
            // Afficher le panneau
            document.querySelectorAll('.settings-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            document.getElementById(tabName + '-panel').classList.add('active');
        }
    });
    
    // Événements des boutons
    saveBtn.addEventListener('click', saveSettings);
    
    cancelBtn.addEventListener('click', () => {
        currentSettings = { ...originalSettings };
        applySettingsToForm();
        AgentOne.ui.showNotification('Modifications annulées', 'info');
    });
    
    exportBtn.addEventListener('click', () => {
        const settings = getSettingsFromForm();
        const blob = new Blob([JSON.stringify(settings, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `sgc-agentone-settings-${new Date().toISOString().slice(0, 10)}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        AgentOne.ui.showNotification('Paramètres exportés', 'success');
    });
    
    importBtn.addEventListener('click', () => {
        importFileInput.click();
    });
    
    importFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const importedSettings = JSON.parse(e.target.result);
                currentSettings = importedSettings;
                applySettingsToForm();
                AgentOne.ui.showNotification('Paramètres importés', 'success');
            } catch (error) {
                AgentOne.ui.showNotification('Fichier de paramètres invalide', 'error');
            }
        };
        reader.readAsText(file);
        
        // Réinitialiser l'input
        importFileInput.value = '';
    });
    
    // Actions de maintenance
    document.getElementById('clear-cache-btn').addEventListener('click', async () => {
        if (confirm('Vider le cache de l\'application ?')) {
            localStorage.clear();
            sessionStorage.clear();
            AgentOne.ui.showNotification('Cache vidé', 'success');
        }
    });
    
    document.getElementById('clear-logs-btn').addEventListener('click', async () => {
        if (confirm('Effacer tous les logs ? Cette action est irréversible.')) {
            try {
                const result = await AgentOne.api.post('settings.php', {
                    action: 'clearLogs'
                });
                
                if (result.success) {
                    AgentOne.ui.showNotification('Logs effacés', 'success');
                } else {
                    throw new Error(result.error);
                }
            } catch (error) {
                AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
            }
        }
    });
    
    document.getElementById('reset-settings-btn').addEventListener('click', () => {
        if (confirm('Réinitialiser tous les paramètres aux valeurs par défaut ?')) {
            currentSettings = getDefaultSettings();
            applySettingsToForm();
            AgentOne.ui.showNotification('Paramètres réinitialisés', 'info');
        }
    });
    
    document.getElementById('factory-reset-btn').addEventListener('click', async () => {
        if (confirm('⚠️ ATTENTION ⚠️\n\nCette action va :\n- Réinitialiser tous les paramètres\n- Effacer tous les logs\n- Supprimer tous les projets enregistrés\n\nContinuer ?')) {
            try {
                const result = await AgentOne.api.post('settings.php', {
                    action: 'factoryReset'
                });
                
                if (result.success) {
                    localStorage.clear();
                    sessionStorage.clear();
                    currentSettings = getDefaultSettings();
                    applySettingsToForm();
                    AgentOne.ui.showNotification('Reset complet effectué', 'success');
                } else {
                    throw new Error(result.error);
                }
            } catch (error) {
                AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
            }
        }
    });
    
    // Initialisation
    loadSettings();
};
</script>