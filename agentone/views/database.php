<?php
/**
 * Vue Base de données - Gestionnaire SQLite intégré
 */
?>

<div class="database-view">
    <div class="database-sidebar sidebar">
        <div class="sidebar-header">
            <h3>🗄️ Base de données</h3>
            <div class="sidebar-actions">
                <button class="btn btn-primary btn-sm" id="new-table-btn" title="Nouvelle table">📊</button>
                <button class="btn btn-primary btn-sm" id="import-sql-btn" title="Importer SQL">📥</button>
                <button class="btn btn-secondary btn-sm" id="refresh-db-btn" title="Actualiser">🔄</button>
            </div>
        </div>
        <div class="sidebar-content">
            <div class="db-info" id="db-info">
                <div class="db-file">
                    <strong>Fichier :</strong> <span id="db-filename">app.db</span>
                </div>
                <div class="db-size">
                    <strong>Taille :</strong> <span id="db-size">0 KB</span>
                </div>
            </div>
            <div class="tables-list" id="tables-list">
                <div class="loading">Chargement des tables...</div>
            </div>
        </div>
    </div>
    
    <div class="database-main flex-1">
        <div class="database-toolbar">
            <div class="toolbar-left">
                <button class="btn btn-primary" id="execute-btn">▶️ Exécuter</button>
                <button class="btn btn-secondary" id="clear-btn">🗑️ Effacer</button>
            </div>
            <div class="toolbar-center">
                <select id="query-templates" class="form-control">
                    <option value="">Modèles de requêtes...</option>
                    <option value="SELECT * FROM {table} LIMIT 10;">SELECT simple</option>
                    <option value="CREATE TABLE {table} (id INTEGER PRIMARY KEY, name TEXT);">CREATE TABLE</option>
                    <option value="INSERT INTO {table} (name) VALUES ('exemple');">INSERT</option>
                    <option value="UPDATE {table} SET name = 'nouveau' WHERE id = 1;">UPDATE</option>
                    <option value="DELETE FROM {table} WHERE id = 1;">DELETE</option>
                </select>
            </div>
            <div class="toolbar-right">
                <button class="btn btn-secondary" id="export-btn">📤 Exporter</button>
                <button class="btn btn-secondary" id="backup-btn">💾 Sauvegarde</button>
            </div>
        </div>
        
        <div class="sql-editor" id="sql-editor">
            <textarea id="sql-query" placeholder="Entrez votre requête SQL ici...
Exemples :
SELECT * FROM users;
CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT, content TEXT);
INSERT INTO users (name, email) VALUES ('John', 'john@example.com');">SELECT * FROM sqlite_master WHERE type='table';</textarea>
        </div>
        
        <div class="results-container" id="results-container">
            <div class="results-header">
                <h4>Résultats</h4>
                <span id="results-count">0 lignes</span>
            </div>
            <div class="results-table" id="results-table">
                <div class="no-results">Aucune requête exécutée</div>
            </div>
        </div>
    </div>
</div>

<style>
.database-view {
    height: 100%;
    display: flex;
}

.database-sidebar {
    width: 300px;
    border-right: 1px solid var(--border-color);
}

.db-info {
    padding: 12px;
    background: var(--bg-tertiary);
    border-radius: 6px;
    margin-bottom: 16px;
    font-size: 0.9rem;
}

.db-info div {
    margin-bottom: 4px;
}

.tables-list {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.table-item {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    margin: 2px 0;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.table-item:hover {
    background: var(--accent-secondary);
}

.table-item.selected {
    background: var(--accent-primary);
    color: var(--bg-primary);
}

.table-icon {
    margin-right: 8px;
    font-size: 1rem;
}

.table-name {
    flex: 1;
    font-weight: 500;
}

.table-count {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.database-main {
    display: flex;
    flex-direction: column;
}

.database-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    gap: 16px;
}

.toolbar-left,
.toolbar-right {
    display: flex;
    gap: 8px;
}

.toolbar-center {
    flex: 1;
    max-width: 300px;
}

.sql-editor {
    height: 200px;
    border-bottom: 1px solid var(--border-color);
}

#sql-query {
    width: 100%;
    height: 100%;
    padding: 16px;
    border: none;
    background: var(--bg-tertiary);
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    resize: none;
    outline: none;
}

.results-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.results-header h4 {
    margin: 0;
    font-size: 1rem;
    color: var(--accent-primary);
}

#results-count {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.results-table {
    flex: 1;
    overflow: auto;
    padding: 16px;
}

.no-results {
    text-align: center;
    color: var(--text-secondary);
    font-style: italic;
    padding: 40px;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.data-table th,
.data-table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.data-table th {
    background: var(--bg-secondary);
    font-weight: 600;
    color: var(--accent-primary);
    position: sticky;
    top: 0;
}

.data-table tr:hover {
    background: var(--accent-secondary);
}

.data-table td {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.error-message {
    background: var(--error-color);
    color: var(--bg-primary);
    padding: 12px;
    border-radius: 6px;
    margin: 16px;
}

@media (max-width: 768px) {
    .database-view {
        flex-direction: column;
    }
    
    .database-sidebar {
        width: 100%;
        height: 200px;
        border-right: none;
        border-bottom: 1px solid var(--border-color);
    }
    
    .database-toolbar {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .toolbar-center {
        order: 3;
        width: 100%;
        max-width: none;
    }
    
    .sql-editor {
        height: 150px;
    }
}
</style>

<script>
window.initView = function() {
    const tablesList = document.getElementById('tables-list');
    const sqlQuery = document.getElementById('sql-query');
    const resultsTable = document.getElementById('results-table');
    const resultsCount = document.getElementById('results-count');
    const executeBtn = document.getElementById('execute-btn');
    const clearBtn = document.getElementById('clear-btn');
    const queryTemplates = document.getElementById('query-templates');
    const dbFilename = document.getElementById('db-filename');
    const dbSize = document.getElementById('db-size');
    
    let selectedTable = '';
    
    // Charger les tables
    async function loadTables() {
        try {
            AgentOne.ui.showLoader(tablesList, 'Chargement des tables...');
            
            const result = await AgentOne.api.post('database.php', {
                action: 'getTables'
            });
            
            if (result.success) {
                renderTables(result.data.tables);
                updateDbInfo(result.data.info);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            tablesList.innerHTML = `<div class="error-message">Erreur : ${error.message}</div>`;
        }
    }
    
    // Rendu des tables
    function renderTables(tables) {
        if (tables.length === 0) {
            tablesList.innerHTML = '<div class="text-muted text-center p-4">Aucune table</div>';
            return;
        }
        
        tablesList.innerHTML = tables.map(table => `
            <div class="table-item" data-table="${table.name}">
                <span class="table-icon">📊</span>
                <span class="table-name">${table.name}</span>
                <span class="table-count">${table.count || 0}</span>
            </div>
        `).join('');
        
        // Événements
        tablesList.querySelectorAll('.table-item').forEach(item => {
            item.addEventListener('click', () => {
                const tableName = item.dataset.table;
                selectTable(tableName);
            });
        });
    }
    
    // Sélectionner une table
    function selectTable(tableName) {
        selectedTable = tableName;
        
        // Mise à jour visuelle
        document.querySelectorAll('.table-item').forEach(item => {
            item.classList.remove('selected');
        });
        document.querySelector(`[data-table="${tableName}"]`).classList.add('selected');
        
        // Charger les données de la table
        sqlQuery.value = `SELECT * FROM ${tableName} LIMIT 100;`;
        executeQuery();
    }
    
    // Mettre à jour les infos de la base
    function updateDbInfo(info) {
        dbFilename.textContent = info.file;
        dbSize.textContent = AgentOne.utils.formatFileSize(info.size);
    }
    
    // Exécuter une requête
    async function executeQuery() {
        const query = sqlQuery.value.trim();
        if (!query) return;
        
        try {
            executeBtn.disabled = true;
            executeBtn.textContent = '⏳ Exécution...';
            
            const result = await AgentOne.api.post('database.php', {
                action: 'executeQuery',
                query: query
            });
            
            if (result.success) {
                renderResults(result.data);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            resultsTable.innerHTML = `<div class="error-message">Erreur : ${error.message}</div>`;
            resultsCount.textContent = '0 lignes';
        } finally {
            executeBtn.disabled = false;
            executeBtn.textContent = '▶️ Exécuter';
        }
    }
    
    // Rendu des résultats
    function renderResults(data) {
        if (!data.rows || data.rows.length === 0) {
            if (data.message) {
                resultsTable.innerHTML = `<div class="success-message">${data.message}</div>`;
                resultsCount.textContent = `${data.affected_rows || 0} ligne(s) affectée(s)`;
            } else {
                resultsTable.innerHTML = '<div class="no-results">Aucun résultat</div>';
                resultsCount.textContent = '0 lignes';
            }
            return;
        }
        
        const columns = data.columns || Object.keys(data.rows[0]);
        
        const table = document.createElement('table');
        table.className = 'data-table';
        
        // En-têtes
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        columns.forEach(col => {
            const th = document.createElement('th');
            th.textContent = col;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);
        table.appendChild(thead);
        
        // Corps
        const tbody = document.createElement('tbody');
        data.rows.forEach(row => {
            const tr = document.createElement('tr');
            columns.forEach(col => {
                const td = document.createElement('td');
                td.textContent = row[col] || '';
                td.title = row[col] || '';
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        
        resultsTable.innerHTML = '';
        resultsTable.appendChild(table);
        resultsCount.textContent = `${data.rows.length} ligne(s)`;
    }
    
    // Événements
    executeBtn.addEventListener('click', executeQuery);
    clearBtn.addEventListener('click', () => {
        sqlQuery.value = '';
        resultsTable.innerHTML = '<div class="no-results">Aucune requête exécutée</div>';
        resultsCount.textContent = '0 lignes';
    });
    
    // Templates de requêtes
    queryTemplates.addEventListener('change', () => {
        const template = queryTemplates.value;
        if (template) {
            if (selectedTable && template.includes('{table}')) {
                sqlQuery.value = template.replace('{table}', selectedTable);
            } else {
                sqlQuery.value = template;
            }
            queryTemplates.value = '';
        }
    });
    
    // Raccourcis clavier
    sqlQuery.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            executeQuery();
        }
    });
    
    // Initialisation
    loadTables();
};
</script>