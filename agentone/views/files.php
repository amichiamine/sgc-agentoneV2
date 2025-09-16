<?php
/**
 * Vue Fichiers - Explorateur de fichiers avec fonctionnalités complètes
 */
?>

<div class="files-view">
    <div class="files-sidebar sidebar">
        <div class="sidebar-header">
            <h3>📁 Explorateur</h3>
            <div class="sidebar-actions">
                <button class="btn btn-primary btn-sm" id="new-file-btn" title="Nouveau fichier">📄</button>
                <button class="btn btn-primary btn-sm" id="new-folder-btn" title="Nouveau dossier">📁</button>
                <button class="btn btn-secondary btn-sm" id="refresh-btn" title="Actualiser">🔄</button>
            </div>
        </div>
        <div class="sidebar-content">
            <div class="path-breadcrumb" id="path-breadcrumb">
                <span class="path-item active" data-path="">🏠 Racine</span>
            </div>
            <div class="file-tree" id="file-tree">
                <div class="loading">Chargement...</div>
            </div>
        </div>
    </div>
    
    <div class="files-main flex-1">
        <div class="files-toolbar">
            <div class="toolbar-left">
                <button class="btn btn-secondary" id="back-btn" disabled>◀️ Retour</button>
                <button class="btn btn-secondary" id="up-btn" disabled>⬆️ Parent</button>
            </div>
            <div class="toolbar-center">
                <div class="search-box">
                    <input type="text" id="search-input" placeholder="Rechercher des fichiers..." class="form-control">
                </div>
            </div>
            <div class="toolbar-right">
                <button class="btn btn-secondary" id="view-list-btn" title="Vue liste">📋</button>
                <button class="btn btn-primary" id="view-grid-btn" title="Vue grille">⊞</button>
                <button class="btn btn-secondary" id="upload-btn" title="Uploader">⬆️</button>
            </div>
        </div>
        
        <div class="files-content" id="files-content">
            <div class="files-grid" id="files-grid">
                <!-- Contenu dynamique -->
            </div>
        </div>
        
        <div class="files-status">
            <span id="files-count">0 éléments</span>
            <span id="current-path">Racine</span>
        </div>
    </div>
</div>

<!-- Modal pour les actions -->
<div id="file-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Action</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="modal-body">
            <!-- Contenu dynamique -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="modal-cancel">Annuler</button>
            <button class="btn btn-primary" id="modal-confirm">Confirmer</button>
        </div>
    </div>
</div>

<!-- Input caché pour upload -->
<input type="file" id="file-upload-input" multiple style="display: none;">

<style>
.files-view {
    height: 100%;
    display: flex;
}

.files-sidebar {
    width: 300px;
    border-right: 1px solid var(--border-color);
}

.sidebar-actions {
    display: flex;
    gap: 4px;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 0.8rem;
}

.path-breadcrumb {
    margin-bottom: 16px;
    padding: 8px;
    background: var(--bg-tertiary);
    border-radius: 6px;
    font-size: 0.9rem;
}

.path-item {
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.path-item:hover {
    background: var(--accent-secondary);
}

.path-item.active {
    background: var(--accent-primary);
    color: var(--bg-primary);
}

.file-tree {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.tree-item {
    display: flex;
    align-items: center;
    padding: 6px 8px;
    margin: 2px 0;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.tree-item:hover {
    background: var(--accent-secondary);
}

.tree-item.selected {
    background: var(--accent-primary);
    color: var(--bg-primary);
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

.files-main {
    display: flex;
    flex-direction: column;
}

.files-toolbar {
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
    max-width: 400px;
}

.search-box {
    position: relative;
}

.search-box input {
    width: 100%;
    padding-left: 32px;
}

.search-box::before {
    content: '🔍';
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
}

.files-content {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 16px;
}

.files-grid.list-view {
    grid-template-columns: 1fr;
    gap: 8px;
}

.file-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.file-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.file-item.selected {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 2px rgba(26, 184, 184, 0.3);
}

.files-grid.list-view .file-item {
    flex-direction: row;
    text-align: left;
    padding: 8px 12px;
}

.file-icon {
    font-size: 2rem;
    margin-bottom: 8px;
}

.files-grid.list-view .file-icon {
    font-size: 1.2rem;
    margin-bottom: 0;
    margin-right: 12px;
}

.file-name {
    font-size: 0.9rem;
    font-weight: 500;
    text-align: center;
    word-break: break-word;
    line-height: 1.3;
}

.files-grid.list-view .file-name {
    flex: 1;
    text-align: left;
}

.file-meta {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-top: 4px;
    text-align: center;
}

.files-grid.list-view .file-meta {
    text-align: right;
    margin-top: 0;
    margin-left: auto;
}

.file-actions {
    position: absolute;
    top: 4px;
    right: 4px;
    display: none;
    gap: 4px;
}

.file-item:hover .file-actions {
    display: flex;
}

.file-action-btn {
    width: 24px;
    height: 24px;
    border: none;
    border-radius: 4px;
    background: var(--bg-primary);
    color: var(--text-primary);
    cursor: pointer;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.file-action-btn:hover {
    background: var(--accent-primary);
    color: var(--bg-primary);
}

.files-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 16px;
    background: var(--bg-secondary);
    border-top: 1px solid var(--border-color);
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: var(--bg-secondary);
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
    margin: 0;
    color: var(--accent-primary);
}

.modal-close {
    background: none;
    border: none;
    color: var(--text-primary);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s ease;
}

.modal-close:hover {
    background: var(--accent-secondary);
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 16px 20px;
    border-top: 1px solid var(--border-color);
}

@media (max-width: 768px) {
    .files-view {
        flex-direction: column;
    }
    
    .files-sidebar {
        width: 100%;
        height: 200px;
        border-right: none;
        border-bottom: 1px solid var(--border-color);
    }
    
    .files-toolbar {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .toolbar-center {
        order: 3;
        width: 100%;
        max-width: none;
    }
    
    .files-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
    }
}
</style>

<script>
window.initView = function() {
    const fileTree = document.getElementById('file-tree');
    const filesGrid = document.getElementById('files-grid');
    const pathBreadcrumb = document.getElementById('path-breadcrumb');
    const filesCount = document.getElementById('files-count');
    const currentPathEl = document.getElementById('current-path');
    const searchInput = document.getElementById('search-input');
    const fileUploadInput = document.getElementById('file-upload-input');
    
    let currentPath = '';
    let selectedFiles = new Set();
    let viewMode = 'grid'; // 'grid' ou 'list'
    let allFiles = [];
    
    // Initialisation
    loadDirectory('');
    
    // Charger un dossier
    async function loadDirectory(path) {
        currentPath = path;
        updateBreadcrumb();
        updateButtons();
        
        try {
            AgentOne.ui.showLoader(fileTree, 'Chargement de l\'arborescence...');
            AgentOne.ui.showLoader(filesGrid, 'Chargement des fichiers...');
            
            const result = await AgentOne.api.post('files.php', {
                action: 'listDir',
                path: path || '.'
            });
            
            if (result.success) {
                allFiles = result.data.items || [];
                renderFileTree(result.data.items);
                renderFilesGrid(result.data.items);
                updateStatus(result.data.items.length);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            fileTree.innerHTML = `<div class="alert alert-error">Erreur : ${error.message}</div>`;
            filesGrid.innerHTML = `<div class="alert alert-error">Erreur : ${error.message}</div>`;
        }
    }
    
    // Rendu de l'arborescence
    function renderFileTree(items) {
        const directories = items.filter(item => item.type === 'directory');
        
        if (directories.length === 0) {
            fileTree.innerHTML = '<div class="text-muted text-center p-4">Aucun dossier</div>';
            return;
        }
        
        fileTree.innerHTML = directories.map(dir => `
            <div class="tree-item" data-path="${dir.name}" data-type="directory">
                <span class="tree-icon">📁</span>
                <span class="tree-label">${dir.name}</span>
            </div>
        `).join('');
        
        // Événements
        fileTree.querySelectorAll('.tree-item').forEach(item => {
            item.addEventListener('click', () => {
                const path = item.dataset.path;
                const fullPath = currentPath ? `${currentPath}/${path}` : path;
                loadDirectory(fullPath);
            });
        });
    }
    
    // Rendu de la grille de fichiers
    function renderFilesGrid(items) {
        if (items.length === 0) {
            filesGrid.innerHTML = '<div class="text-muted text-center p-4">Dossier vide</div>';
            return;
        }
        
        filesGrid.className = `files-grid ${viewMode === 'list' ? 'list-view' : ''}`;
        
        filesGrid.innerHTML = items.map(item => {
            const icon = getFileIcon(item);
            const size = item.type === 'file' ? AgentOne.utils.formatFileSize(item.size) : '';
            const modified = AgentOne.utils.formatDate(item.modified);
            
            return `
                <div class="file-item" data-name="${item.name}" data-type="${item.type}">
                    <div class="file-icon">${icon}</div>
                    <div class="file-name">${item.name}</div>
                    <div class="file-meta">${size} ${modified}</div>
                    <div class="file-actions">
                        <button class="file-action-btn" data-action="rename" title="Renommer">✏️</button>
                        <button class="file-action-btn" data-action="delete" title="Supprimer">🗑️</button>
                        ${item.type === 'file' ? '<button class="file-action-btn" data-action="download" title="Télécharger">⬇️</button>' : ''}
                    </div>
                </div>
            `;
        }).join('');
        
        // Événements
        filesGrid.querySelectorAll('.file-item').forEach(item => {
            // Double-clic pour ouvrir
            item.addEventListener('dblclick', () => {
                const name = item.dataset.name;
                const type = item.dataset.type;
                
                if (type === 'directory') {
                    const fullPath = currentPath ? `${currentPath}/${name}` : name;
                    loadDirectory(fullPath);
                } else {
                    openFile(name);
                }
            });
            
            // Sélection
            item.addEventListener('click', (e) => {
                if (e.target.classList.contains('file-action-btn')) return;
                
                if (e.ctrlKey || e.metaKey) {
                    toggleSelection(item);
                } else {
                    clearSelection();
                    selectItem(item);
                }
            });
            
            // Actions
            item.querySelectorAll('.file-action-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const action = btn.dataset.action;
                    const fileName = item.dataset.name;
                    handleFileAction(action, fileName);
                });
            });
        });
    }
    
    // Icône selon le type de fichier
    function getFileIcon(item) {
        if (item.type === 'directory') return '📁';
        
        const ext = item.name.split('.').pop().toLowerCase();
        const icons = {
            'txt': '📄', 'md': '📝', 'pdf': '📕',
            'jpg': '🖼️', 'jpeg': '🖼️', 'png': '🖼️', 'gif': '🖼️',
            'mp3': '🎵', 'wav': '🎵', 'mp4': '🎬', 'avi': '🎬',
            'zip': '📦', 'rar': '📦', '7z': '📦',
            'js': '📜', 'html': '🌐', 'css': '🎨', 'php': '⚙️',
            'json': '📋', 'xml': '📋', 'csv': '📊'
        };
        
        return icons[ext] || '📄';
    }
    
    // Gestion de la sélection
    function selectItem(item) {
        item.classList.add('selected');
        selectedFiles.add(item.dataset.name);
    }
    
    function toggleSelection(item) {
        if (item.classList.contains('selected')) {
            item.classList.remove('selected');
            selectedFiles.delete(item.dataset.name);
        } else {
            selectItem(item);
        }
    }
    
    function clearSelection() {
        document.querySelectorAll('.file-item.selected').forEach(item => {
            item.classList.remove('selected');
        });
        selectedFiles.clear();
    }
    
    // Mise à jour du breadcrumb
    function updateBreadcrumb() {
        const parts = currentPath ? currentPath.split('/') : [];
        let breadcrumb = '<span class="path-item" data-path="">🏠 Racine</span>';
        
        let buildPath = '';
        parts.forEach(part => {
            buildPath += (buildPath ? '/' : '') + part;
            breadcrumb += ` / <span class="path-item" data-path="${buildPath}">${part}</span>`;
        });
        
        pathBreadcrumb.innerHTML = breadcrumb;
        currentPathEl.textContent = currentPath || 'Racine';
        
        // Événements breadcrumb
        pathBreadcrumb.querySelectorAll('.path-item').forEach(item => {
            item.addEventListener('click', () => {
                loadDirectory(item.dataset.path);
            });
        });
    }
    
    // Mise à jour des boutons
    function updateButtons() {
        const backBtn = document.getElementById('back-btn');
        const upBtn = document.getElementById('up-btn');
        
        backBtn.disabled = !currentPath;
        upBtn.disabled = !currentPath;
    }
    
    // Mise à jour du statut
    function updateStatus(count) {
        filesCount.textContent = `${count} élément${count > 1 ? 's' : ''}`;
    }
    
    // Actions sur les fichiers
    async function handleFileAction(action, fileName) {
        const filePath = currentPath ? `${currentPath}/${fileName}` : fileName;
        
        switch (action) {
            case 'rename':
                const newName = prompt('Nouveau nom :', fileName);
                if (newName && newName !== fileName) {
                    await renameFile(filePath, newName);
                }
                break;
                
            case 'delete':
                if (confirm(`Êtes-vous sûr de vouloir supprimer "${fileName}" ?`)) {
                    await deleteFile(filePath);
                }
                break;
                
            case 'download':
                downloadFile(filePath);
                break;
        }
    }
    
    // Renommer un fichier
    async function renameFile(oldPath, newName) {
        try {
            const result = await AgentOne.api.post('files.php', {
                action: 'renameFile',
                path: oldPath,
                newName: newName
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Fichier renommé avec succès', 'success');
                loadDirectory(currentPath);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Supprimer un fichier
    async function deleteFile(filePath) {
        try {
            const result = await AgentOne.api.post('files.php', {
                action: 'deleteFile',
                path: filePath
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Fichier supprimé avec succès', 'success');
                loadDirectory(currentPath);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Télécharger un fichier
    function downloadFile(filePath) {
        const link = document.createElement('a');
        link.href = `api/files.php?action=downloadFile&path=${encodeURIComponent(filePath)}`;
        link.download = filePath.split('/').pop();
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Ouvrir un fichier
    function openFile(fileName) {
        // Rediriger vers l'éditeur avec le fichier
        window.location.href = `?view=editor&file=${encodeURIComponent(currentPath ? `${currentPath}/${fileName}` : fileName)}`;
    }
    
    // Recherche
    searchInput.addEventListener('input', AgentOne.utils.debounce(() => {
        const query = searchInput.value.toLowerCase();
        if (!query) {
            renderFilesGrid(allFiles);
            return;
        }
        
        const filtered = allFiles.filter(item => 
            item.name.toLowerCase().includes(query)
        );
        renderFilesGrid(filtered);
        updateStatus(filtered.length);
    }, 300));
    
    // Événements des boutons
    document.getElementById('back-btn').addEventListener('click', () => {
        if (currentPath) {
            const parentPath = currentPath.split('/').slice(0, -1).join('/');
            loadDirectory(parentPath);
        }
    });
    
    document.getElementById('up-btn').addEventListener('click', () => {
        if (currentPath) {
            const parentPath = currentPath.split('/').slice(0, -1).join('/');
            loadDirectory(parentPath);
        }
    });
    
    document.getElementById('view-list-btn').addEventListener('click', () => {
        viewMode = 'list';
        renderFilesGrid(allFiles);
        document.getElementById('view-list-btn').classList.add('btn-primary');
        document.getElementById('view-list-btn').classList.remove('btn-secondary');
        document.getElementById('view-grid-btn').classList.add('btn-secondary');
        document.getElementById('view-grid-btn').classList.remove('btn-primary');
    });
    
    document.getElementById('view-grid-btn').addEventListener('click', () => {
        viewMode = 'grid';
        renderFilesGrid(allFiles);
        document.getElementById('view-grid-btn').classList.add('btn-primary');
        document.getElementById('view-grid-btn').classList.remove('btn-secondary');
        document.getElementById('view-list-btn').classList.add('btn-secondary');
        document.getElementById('view-list-btn').classList.remove('btn-primary');
    });
    
    document.getElementById('refresh-btn').addEventListener('click', () => {
        loadDirectory(currentPath);
    });
    
    document.getElementById('new-file-btn').addEventListener('click', () => {
        const fileName = prompt('Nom du nouveau fichier :');
        if (fileName) {
            createFile(fileName);
        }
    });
    
    document.getElementById('new-folder-btn').addEventListener('click', () => {
        const folderName = prompt('Nom du nouveau dossier :');
        if (folderName) {
            createFolder(folderName);
        }
    });
    
    document.getElementById('upload-btn').addEventListener('click', () => {
        fileUploadInput.click();
    });
    
    // Upload de fichiers
    fileUploadInput.addEventListener('change', async (e) => {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;
        
        for (const file of files) {
            await uploadFile(file);
        }
        
        loadDirectory(currentPath);
        fileUploadInput.value = '';
    });
    
    // Créer un fichier
    async function createFile(fileName) {
        try {
            const filePath = currentPath ? `${currentPath}/${fileName}` : fileName;
            const result = await AgentOne.api.post('chat.php', {
                message: `createFile ${filePath} : `
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Fichier créé avec succès', 'success');
                loadDirectory(currentPath);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Créer un dossier
    async function createFolder(folderName) {
        try {
            const folderPath = currentPath ? `${currentPath}/${folderName}` : folderName;
            const result = await AgentOne.api.post('chat.php', {
                message: `createDir ${folderPath}`
            });
            
            if (result.success) {
                AgentOne.ui.showNotification('Dossier créé avec succès', 'success');
                loadDirectory(currentPath);
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Upload d'un fichier
    async function uploadFile(file) {
        try {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('path', currentPath);
            
            const result = await AgentOne.api.upload('files.php', formData);
            
            if (result.success) {
                AgentOne.ui.showNotification(`Fichier "${file.name}" uploadé avec succès`, 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur upload "${file.name}" : ${error.message}`, 'error');
        }
    }
};
</script>