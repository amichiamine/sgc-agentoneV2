<?php
/**
 * Vue Prompts - Gestionnaire de prompts et templates
 */
?>

<div class="prompts-view">
    <div class="prompts-header">
        <div class="header-left">
            <h2>📝 Gestionnaire de Prompts</h2>
            <p class="header-subtitle">Créez et gérez vos templates de commandes</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" id="import-prompt-btn">📥 Importer</button>
            <button class="btn btn-primary" id="new-prompt-btn">➕ Nouveau Prompt</button>
        </div>
    </div>
    
    <div class="prompts-content">
        <div class="prompts-sidebar">
            <div class="sidebar-header">
                <h3>📂 Catégories</h3>
                <button class="btn btn-secondary btn-sm" id="new-category-btn">➕</button>
            </div>
            <div class="categories-list" id="categories-list">
                <div class="category-item active" data-category="all">
                    <span class="category-icon">📋</span>
                    <span class="category-name">Tous les prompts</span>
                    <span class="category-count" id="all-count">0</span>
                </div>
            </div>
        </div>
        
        <div class="prompts-main">
            <div class="prompts-toolbar">
                <div class="search-container">
                    <input type="text" id="search-prompts" class="form-control" placeholder="🔍 Rechercher des prompts...">
                </div>
                <div class="toolbar-actions">
                    <select id="sort-prompts" class="form-control">
                        <option value="modified">Dernière modification</option>
                        <option value="name">Nom</option>
                        <option value="category">Catégorie</option>
                        <option value="usage">Utilisation</option>
                    </select>
                    <button class="btn btn-secondary" id="refresh-prompts-btn">🔄</button>
                </div>
            </div>
            
            <div class="prompts-grid" id="prompts-grid">
                <div class="loading">Chargement des prompts...</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal nouveau prompt -->
<div id="new-prompt-modal" class="modal" style="display: none;">
    <div class="modal-content large-modal">
        <div class="modal-header">
            <h3 id="prompt-modal-title">➕ Nouveau Prompt</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="prompt-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="prompt-name">Nom du prompt *</label>
                        <input type="text" id="prompt-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prompt-category">Catégorie</label>
                        <select id="prompt-category" class="form-control">
                            <option value="general">Général</option>
                            <option value="development">Développement</option>
                            <option value="files">Fichiers</option>
                            <option value="server">Serveur</option>
                            <option value="database">Base de données</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="prompt-description">Description</label>
                    <textarea id="prompt-description" class="form-control" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="prompt-content">Contenu du prompt *</label>
                    <div class="editor-container">
                        <div class="editor-toolbar">
                            <button type="button" class="editor-btn" data-action="bold">B</button>
                            <button type="button" class="editor-btn" data-action="italic">I</button>
                            <button type="button" class="editor-btn" data-action="code">Code</button>
                            <button type="button" class="editor-btn" data-action="variable">Var</button>
                        </div>
                        <textarea id="prompt-content" class="form-control code-editor" rows="10" required></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="prompt-tags">Tags (séparés par des virgules)</label>
                        <input type="text" id="prompt-tags" class="form-control" placeholder="web, php, création">
                    </div>
                    <div class="form-group">
                        <label for="prompt-shortcut">Raccourci clavier</label>
                        <input type="text" id="prompt-shortcut" class="form-control" placeholder="Ctrl+Alt+P">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="prompt-favorite">
                        <span class="checkmark"></span>
                        Marquer comme favori
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-prompt-btn">Annuler</button>
            <button class="btn btn-secondary" id="test-prompt-btn">🧪 Tester</button>
            <button class="btn btn-primary" id="save-prompt-btn">💾 Sauvegarder</button>
        </div>
    </div>
</div>

<!-- Modal aperçu prompt -->
<div id="prompt-preview-modal" class="modal" style="display: none;">
    <div class="modal-content large-modal">
        <div class="modal-header">
            <h3 id="preview-title">Aperçu du prompt</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="prompt-preview" id="prompt-preview-content">
                <!-- Contenu dynamique -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="edit-prompt-btn">✏️ Modifier</button>
            <button class="btn btn-secondary" id="duplicate-prompt-btn">📋 Dupliquer</button>
            <button class="btn btn-danger" id="delete-prompt-btn">🗑️ Supprimer</button>
            <button class="btn btn-primary" id="execute-prompt-btn">▶️ Exécuter</button>
        </div>
    </div>
</div>

<style>
.prompts-view {
    height: 100%;
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 16px;
    overflow: hidden;
}

.prompts-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
    background: var(--bg-secondary);
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.header-left h2 {
    margin: 0 0 4px 0;
    color: var(--accent-primary);
    font-size: 1.5rem;
}

.header-subtitle {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.header-actions {
    display: flex;
    gap: 8px;
}

.prompts-content {
    flex: 1;
    display: flex;
    gap: 16px;
    overflow: hidden;
}

.prompts-sidebar {
    width: 250px;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
}

.sidebar-header h3 {
    margin: 0;
    font-size: 1rem;
    color: var(--accent-primary);
}

.categories-list {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
}

.category-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    margin-bottom: 2px;
}

.category-item:hover {
    background: var(--accent-secondary);
}

.category-item.active {
    background: var(--accent-primary);
    color: var(--bg-primary);
}

.category-icon {
    font-size: 1rem;
}

.category-name {
    flex: 1;
}

.category-count {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 0.75rem;
    min-width: 20px;
    text-align: center;
}

.category-item.active .category-count {
    background: rgba(255, 255, 255, 0.2);
    color: var(--bg-primary);
}

.prompts-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.prompts-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    margin-bottom: 16px;
}

.search-container {
    flex: 1;
    max-width: 400px;
}

.toolbar-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.prompts-grid {
    flex: 1;
    overflow-y: auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
    padding: 16px;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.prompt-card {
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.prompt-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    border-color: var(--accent-primary);
}

.prompt-card.favorite::before {
    content: '⭐';
    position: absolute;
    top: 8px;
    right: 8px;
    font-size: 1rem;
}

.prompt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.prompt-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.3;
}

.prompt-category-badge {
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 500;
    background: var(--accent-secondary);
    color: var(--text-primary);
}

.prompt-description {
    color: var(--text-secondary);
    font-size: 0.85rem;
    line-height: 1.4;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prompt-preview {
    background: var(--bg-primary);
    border-radius: 4px;
    padding: 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    line-height: 1.4;
    margin-bottom: 12px;
    max-height: 60px;
    overflow: hidden;
    position: relative;
}

.prompt-preview::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 20px;
    background: linear-gradient(transparent, var(--bg-primary));
}

.prompt-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.prompt-tags {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
}

.prompt-tag {
    background: var(--accent-secondary);
    color: var(--text-primary);
    padding: 2px 6px;
    border-radius: 8px;
    font-size: 0.7rem;
}

.prompt-stats {
    display: flex;
    gap: 8px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.editor-container {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    overflow: hidden;
}

.editor-toolbar {
    display: flex;
    gap: 4px;
    padding: 8px;
    background: var(--bg-tertiary);
    border-bottom: 1px solid var(--border-color);
}

.editor-btn {
    padding: 4px 8px;
    border: none;
    border-radius: 4px;
    background: var(--accent-secondary);
    color: var(--text-primary);
    cursor: pointer;
    font-size: 0.8rem;
    transition: background 0.2s ease;
}

.editor-btn:hover {
    background: var(--bg-primary);
}

.code-editor {
    border: none;
    border-radius: 0;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    resize: vertical;
}

.large-modal .modal-content {
    max-width: 900px;
    width: 90%;
}

.prompt-preview-content {
    background: var(--bg-tertiary);
    border-radius: 8px;
    padding: 20px;
}

.preview-section {
    margin-bottom: 20px;
}

.preview-section h4 {
    margin: 0 0 8px 0;
    color: var(--accent-primary);
    font-size: 1rem;
}

.preview-content {
    background: var(--bg-primary);
    border-radius: 6px;
    padding: 12px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.5;
    white-space: pre-wrap;
}

.preview-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color);
}

.meta-label {
    font-weight: 500;
    color: var(--text-secondary);
}

.meta-value {
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .prompts-content {
        flex-direction: column;
    }
    
    .prompts-sidebar {
        width: 100%;
        height: 200px;
    }
    
    .prompts-toolbar {
        flex-direction: column;
        gap: 12px;
    }
    
    .search-container {
        width: 100%;
        max-width: none;
    }
    
    .prompts-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .preview-meta {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
window.initView = function() {
    const promptsGrid = document.getElementById('prompts-grid');
    const categoriesList = document.getElementById('categories-list');
    const searchInput = document.getElementById('search-prompts');
    const sortSelect = document.getElementById('sort-prompts');
    const newPromptBtn = document.getElementById('new-prompt-btn');
    const newCategoryBtn = document.getElementById('new-category-btn');
    const refreshBtn = document.getElementById('refresh-prompts-btn');
    
    // Modals
    const newPromptModal = document.getElementById('new-prompt-modal');
    const promptPreviewModal = document.getElementById('prompt-preview-modal');
    
    let prompts = [];
    let categories = ['general', 'development', 'files', 'server', 'database'];
    let currentCategory = 'all';
    let filteredPrompts = [];
    
    // Charger les prompts
    async function loadPrompts() {
        try {
            AgentOne.ui.showLoader(promptsGrid, 'Chargement des prompts...');
            
            const result = await AgentOne.api.post('prompts.php', {
                action: 'getPrompts'
            });
            
            if (result.success) {
                prompts = result.data.prompts || [];
                updateCategories();
                applyFilters();
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            promptsGrid.innerHTML = `<div class="empty-state">
                <h3>Erreur de chargement</h3>
                <p>${error.message}</p>
            </div>`;
        }
    }
    
    // Mettre à jour les catégories
    function updateCategories() {
        const categoryCounts = {};
        
        // Compter les prompts par catégorie
        prompts.forEach(prompt => {
            const category = prompt.category || 'general';
            categoryCounts[category] = (categoryCounts[category] || 0) + 1;
        });
        
        // Mettre à jour l'affichage
        document.getElementById('all-count').textContent = prompts.length;
        
        // Ajouter les catégories dynamiques
        const existingCategories = Array.from(categoriesList.querySelectorAll('.category-item:not([data-category="all"])'));
        existingCategories.forEach(cat => cat.remove());
        
        Object.keys(categoryCounts).forEach(category => {
            if (!categories.includes(category)) {
                categories.push(category);
            }
            
            const categoryItem = document.createElement('div');
            categoryItem.className = 'category-item';
            categoryItem.dataset.category = category;
            categoryItem.innerHTML = `
                <span class="category-icon">${getCategoryIcon(category)}</span>
                <span class="category-name">${getCategoryName(category)}</span>
                <span class="category-count">${categoryCounts[category] || 0}</span>
            `;
            
            categoryItem.addEventListener('click', () => {
                selectCategory(category);
            });
            
            categoriesList.appendChild(categoryItem);
        });
    }
    
    // Icônes des catégories
    function getCategoryIcon(category) {
        const icons = {
            general: '📋',
            development: '💻',
            files: '📁',
            server: '🖥️',
            database: '🗄️'
        };
        return icons[category] || '📝';
    }
    
    // Noms des catégories
    function getCategoryName(category) {
        const names = {
            general: 'Général',
            development: 'Développement',
            files: 'Fichiers',
            server: 'Serveur',
            database: 'Base de données'
        };
        return names[category] || category.charAt(0).toUpperCase() + category.slice(1);
    }
    
    // Sélectionner une catégorie
    function selectCategory(category) {
        currentCategory = category;
        
        // Mise à jour visuelle
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-category="${category}"]`).classList.add('active');
        
        applyFilters();
    }
    
    // Appliquer les filtres
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const sortBy = sortSelect.value;
        
        filteredPrompts = prompts.filter(prompt => {
            const matchesCategory = currentCategory === 'all' || prompt.category === currentCategory;
            const matchesSearch = !searchTerm || 
                prompt.name.toLowerCase().includes(searchTerm) ||
                prompt.description.toLowerCase().includes(searchTerm) ||
                (prompt.tags && prompt.tags.some(tag => tag.toLowerCase().includes(searchTerm)));
            
            return matchesCategory && matchesSearch;
        });
        
        // Tri
        filteredPrompts.sort((a, b) => {
            switch (sortBy) {
                case 'name':
                    return a.name.localeCompare(b.name);
                case 'category':
                    return (a.category || 'general').localeCompare(b.category || 'general');
                case 'usage':
                    return (b.usage_count || 0) - (a.usage_count || 0);
                case 'modified':
                default:
                    return new Date(b.modified) - new Date(a.modified);
            }
        });
        
        renderPrompts();
    }
    
    // Rendu des prompts
    function renderPrompts() {
        if (filteredPrompts.length === 0) {
            promptsGrid.innerHTML = `<div class="empty-state">
                <h3>Aucun prompt trouvé</h3>
                <p>Créez votre premier prompt ou ajustez vos filtres</p>
            </div>`;
            return;
        }
        
        promptsGrid.innerHTML = filteredPrompts.map(prompt => `
            <div class="prompt-card ${prompt.favorite ? 'favorite' : ''}" data-id="${prompt.id}">
                <div class="prompt-header">
                    <h3 class="prompt-title">${prompt.name}</h3>
                    <span class="prompt-category-badge">${getCategoryName(prompt.category || 'general')}</span>
                </div>
                
                <p class="prompt-description">${prompt.description || 'Aucune description'}</p>
                
                <div class="prompt-preview">${prompt.content.substring(0, 100)}${prompt.content.length > 100 ? '...' : ''}</div>
                
                <div class="prompt-footer">
                    <div class="prompt-tags">
                        ${(prompt.tags || []).slice(0, 3).map(tag => 
                            `<span class="prompt-tag">${tag}</span>`
                        ).join('')}
                    </div>
                    <div class="prompt-stats">
                        <span>📊 ${prompt.usage_count || 0}</span>
                        <span>📅 ${AgentOne.utils.formatDate(prompt.modified)}</span>
                    </div>
                </div>
            </div>
        `).join('');
        
        // Événements des cartes
        promptsGrid.querySelectorAll('.prompt-card').forEach(card => {
            card.addEventListener('click', () => {
                const promptId = card.dataset.id;
                showPromptPreview(promptId);
            });
        });
    }
    
    // Afficher l'aperçu d'un prompt
    function showPromptPreview(promptId) {
        const prompt = prompts.find(p => p.id === promptId);
        if (!prompt) return;
        
        document.getElementById('preview-title').textContent = prompt.name;
        
        const previewContent = document.getElementById('prompt-preview-content');
        previewContent.innerHTML = `
            <div class="preview-meta">
                <div class="meta-item">
                    <span class="meta-label">Catégorie :</span>
                    <span class="meta-value">${getCategoryName(prompt.category || 'general')}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Utilisations :</span>
                    <span class="meta-value">${prompt.usage_count || 0}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Créé le :</span>
                    <span class="meta-value">${AgentOne.utils.formatDate(prompt.created)}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Modifié le :</span>
                    <span class="meta-value">${AgentOne.utils.formatDate(prompt.modified)}</span>
                </div>
            </div>
            
            <div class="preview-section">
                <h4>Description</h4>
                <p>${prompt.description || 'Aucune description'}</p>
            </div>
            
            <div class="preview-section">
                <h4>Contenu</h4>
                <div class="preview-content">${prompt.content}</div>
            </div>
            
            ${prompt.tags && prompt.tags.length > 0 ? `
                <div class="preview-section">
                    <h4>Tags</h4>
                    <div class="prompt-tags">
                        ${prompt.tags.map(tag => `<span class="prompt-tag">${tag}</span>`).join('')}
                    </div>
                </div>
            ` : ''}
        `;
        
        promptPreviewModal.style.display = 'flex';
        
        // Configurer les boutons d'action
        document.getElementById('execute-prompt-btn').onclick = () => executePrompt(prompt);
        document.getElementById('edit-prompt-btn').onclick = () => editPrompt(prompt);
        document.getElementById('duplicate-prompt-btn').onclick = () => duplicatePrompt(prompt);
        document.getElementById('delete-prompt-btn').onclick = () => deletePrompt(prompt);
    }
    
    // Exécuter un prompt
    async function executePrompt(prompt) {
        try {
            // Envoyer le contenu du prompt au chat
            window.parent.postMessage({
                type: 'EXECUTE_PROMPT',
                content: prompt.content
            }, '*');
            
            // Incrémenter le compteur d'utilisation
            await AgentOne.api.post('prompts.php', {
                action: 'incrementUsage',
                promptId: prompt.id
            });
            
            promptPreviewModal.style.display = 'none';
            AgentOne.ui.showNotification(`Prompt "${prompt.name}" exécuté`, 'success');
            
            // Recharger pour mettre à jour les statistiques
            loadPrompts();
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Modifier un prompt
    function editPrompt(prompt) {
        // Remplir le formulaire avec les données du prompt
        document.getElementById('prompt-modal-title').textContent = '✏️ Modifier le prompt';
        document.getElementById('prompt-name').value = prompt.name;
        document.getElementById('prompt-description').value = prompt.description || '';
        document.getElementById('prompt-category').value = prompt.category || 'general';
        document.getElementById('prompt-content').value = prompt.content;
        document.getElementById('prompt-tags').value = (prompt.tags || []).join(', ');
        document.getElementById('prompt-shortcut').value = prompt.shortcut || '';
        document.getElementById('prompt-favorite').checked = prompt.favorite || false;
        
        // Changer l'action du bouton de sauvegarde
        const saveBtn = document.getElementById('save-prompt-btn');
        saveBtn.textContent = '💾 Mettre à jour';
        saveBtn.onclick = () => updatePrompt(prompt.id);
        
        promptPreviewModal.style.display = 'none';
        newPromptModal.style.display = 'flex';
    }
    
    // Dupliquer un prompt
    function duplicatePrompt(prompt) {
        const duplicatedPrompt = {
            ...prompt,
            name: prompt.name + ' (Copie)',
            id: undefined,
            created: undefined,
            modified: undefined,
            usage_count: 0
        };
        
        createPrompt(duplicatedPrompt);
        promptPreviewModal.style.display = 'none';
    }
    
    // Supprimer un prompt
    async function deletePrompt(prompt) {
        if (!confirm(`Êtes-vous sûr de vouloir supprimer le prompt "${prompt.name}" ?`)) {
            return;
        }
        
        try {
            const result = await AgentOne.api.post('prompts.php', {
                action: 'deletePrompt',
                promptId: prompt.id
            });
            
            if (result.success) {
                promptPreviewModal.style.display = 'none';
                loadPrompts();
                AgentOne.ui.showNotification('Prompt supprimé', 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Créer un nouveau prompt
    async function createPrompt(promptData = null) {
        const formData = promptData || getFormData();
        
        try {
            const result = await AgentOne.api.post('prompts.php', {
                action: 'createPrompt',
                prompt: formData
            });
            
            if (result.success) {
                newPromptModal.style.display = 'none';
                resetForm();
                loadPrompts();
                AgentOne.ui.showNotification('Prompt créé avec succès', 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Mettre à jour un prompt
    async function updatePrompt(promptId) {
        const formData = getFormData();
        
        try {
            const result = await AgentOne.api.post('prompts.php', {
                action: 'updatePrompt',
                promptId: promptId,
                prompt: formData
            });
            
            if (result.success) {
                newPromptModal.style.display = 'none';
                resetForm();
                loadPrompts();
                AgentOne.ui.showNotification('Prompt mis à jour', 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Récupérer les données du formulaire
    function getFormData() {
        const tags = document.getElementById('prompt-tags').value
            .split(',')
            .map(tag => tag.trim())
            .filter(tag => tag.length > 0);
        
        return {
            name: document.getElementById('prompt-name').value.trim(),
            description: document.getElementById('prompt-description').value.trim(),
            category: document.getElementById('prompt-category').value,
            content: document.getElementById('prompt-content').value.trim(),
            tags: tags,
            shortcut: document.getElementById('prompt-shortcut').value.trim(),
            favorite: document.getElementById('prompt-favorite').checked
        };
    }
    
    // Réinitialiser le formulaire
    function resetForm() {
        document.getElementById('prompt-form').reset();
        document.getElementById('prompt-modal-title').textContent = '➕ Nouveau Prompt';
        const saveBtn = document.getElementById('save-prompt-btn');
        saveBtn.textContent = '💾 Sauvegarder';
        saveBtn.onclick = () => createPrompt();
    }
    
    // Événements des filtres
    searchInput.addEventListener('input', AgentOne.utils.debounce(applyFilters, 300));
    sortSelect.addEventListener('change', applyFilters);
    
    // Événements des boutons
    newPromptBtn.addEventListener('click', () => {
        resetForm();
        newPromptModal.style.display = 'flex';
    });
    
    refreshBtn.addEventListener('click', loadPrompts);
    
    // Gestion des modals
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.target.closest('.modal').style.display = 'none';
        });
    });
    
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Boutons du formulaire
    document.getElementById('save-prompt-btn').addEventListener('click', () => createPrompt());
    document.getElementById('cancel-prompt-btn').addEventListener('click', () => {
        newPromptModal.style.display = 'none';
    });
    
    // Boutons de l'éditeur
    document.querySelectorAll('.editor-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action;
            const textarea = document.getElementById('prompt-content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            
            let replacement = '';
            switch (action) {
                case 'bold':
                    replacement = `**${selectedText}**`;
                    break;
                case 'italic':
                    replacement = `*${selectedText}*`;
                    break;
                case 'code':
                    replacement = `\`${selectedText}\``;
                    break;
                case 'variable':
                    replacement = `{${selectedText || 'variable'}}`;
                    break;
            }
            
            textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
            textarea.focus();
            textarea.setSelectionRange(start + replacement.length, start + replacement.length);
        });
    });
    
    // Initialisation
    loadPrompts();
};
</script>