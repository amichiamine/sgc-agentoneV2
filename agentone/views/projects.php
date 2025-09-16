<?php
/**
 * Vue Projets - Gestionnaire de projets avec métadonnées
 */
?>

<div class="projects-view">
    <div class="projects-header">
        <div class="header-left">
            <h2>📂 Gestionnaire de Projets</h2>
            <p class="header-subtitle">Organisez et gérez vos projets de développement</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" id="import-project-btn">📥 Importer</button>
            <button class="btn btn-primary" id="new-project-btn">➕ Nouveau Projet</button>
        </div>
    </div>
    
    <div class="projects-filters">
        <div class="filter-group">
            <input type="text" id="search-projects" class="form-control" placeholder="🔍 Rechercher des projets...">
        </div>
        <div class="filter-group">
            <select id="filter-status" class="form-control">
                <option value="">Tous les statuts</option>
                <option value="active">En cours</option>
                <option value="completed">Terminé</option>
                <option value="paused">En pause</option>
                <option value="archived">Archivé</option>
            </select>
        </div>
        <div class="filter-group">
            <select id="filter-language" class="form-control">
                <option value="">Tous les langages</option>
                <option value="php">PHP</option>
                <option value="javascript">JavaScript</option>
                <option value="python">Python</option>
                <option value="html">HTML/CSS</option>
                <option value="other">Autre</option>
            </select>
        </div>
        <div class="filter-group">
            <select id="sort-projects" class="form-control">
                <option value="modified">Dernière modification</option>
                <option value="name">Nom</option>
                <option value="created">Date de création</option>
                <option value="status">Statut</option>
            </select>
        </div>
    </div>
    
    <div class="projects-grid" id="projects-grid">
        <div class="loading">Chargement des projets...</div>
    </div>
</div>

<!-- Modal nouveau projet -->
<div id="new-project-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>➕ Nouveau Projet</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="new-project-form">
                <div class="form-group">
                    <label for="project-name">Nom du projet *</label>
                    <input type="text" id="project-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="project-description">Description</label>
                    <textarea id="project-description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="project-path">Chemin du projet *</label>
                    <div class="path-input-group">
                        <input type="text" id="project-path" class="form-control" required>
                        <button type="button" class="btn btn-secondary" id="browse-path-btn">📁 Parcourir</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="project-languages">Langages utilisés</label>
                    <div class="languages-checkboxes">
                        <label class="checkbox-label">
                            <input type="checkbox" name="languages" value="php"> PHP
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="languages" value="javascript"> JavaScript
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="languages" value="html"> HTML/CSS
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="languages" value="python"> Python
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="languages" value="other"> Autre
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="project-status">Statut</label>
                    <select id="project-status" class="form-control">
                        <option value="active">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="paused">En pause</option>
                        <option value="archived">Archivé</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="project-notes">Notes</label>
                    <textarea id="project-notes" class="form-control" rows="4"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-project-btn">Annuler</button>
            <button class="btn btn-primary" id="save-project-btn">💾 Créer le projet</button>
        </div>
    </div>
</div>

<!-- Modal détails projet -->
<div id="project-details-modal" class="modal" style="display: none;">
    <div class="modal-content large-modal">
        <div class="modal-header">
            <h3 id="project-details-title">Détails du projet</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="project-details-content" id="project-details-content">
                <!-- Contenu dynamique -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="edit-project-btn">✏️ Modifier</button>
            <button class="btn btn-secondary" id="export-project-btn">📤 Exporter</button>
            <button class="btn btn-danger" id="delete-project-btn">🗑️ Supprimer</button>
            <button class="btn btn-primary" id="open-project-btn">📂 Ouvrir</button>
        </div>
    </div>
</div>

<style>
.projects-view {
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 16px;
    gap: 16px;
    overflow: hidden;
}

.projects-header {
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

.projects-filters {
    display: flex;
    gap: 12px;
    padding: 16px;
    background: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.projects-grid {
    flex: 1;
    overflow-y: auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 16px;
    padding: 16px;
}

.project-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    border-color: var(--accent-primary);
}

.project-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent-primary);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.project-card:hover::before {
    opacity: 1;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.project-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.3;
}

.project-status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: rgba(34, 197, 94, 0.2);
    color: #22c55e;
}

.status-completed {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
}

.status-paused {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

.status-archived {
    background: rgba(107, 114, 128, 0.2);
    color: #6b7280;
}

.project-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.language-badge {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    background: var(--accent-secondary);
    color: var(--text-primary);
}

.project-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.project-path {
    font-family: 'JetBrains Mono', monospace;
    background: var(--bg-tertiary);
    padding: 2px 6px;
    border-radius: 4px;
}

.project-date {
    font-style: italic;
}

.path-input-group {
    display: flex;
    gap: 8px;
}

.path-input-group input {
    flex: 1;
}

.languages-checkboxes {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-size: 0.9rem;
}

.large-modal .modal-content {
    max-width: 800px;
    width: 90%;
}

.project-details-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.detail-section {
    background: var(--bg-tertiary);
    padding: 16px;
    border-radius: 8px;
}

.detail-section h4 {
    margin: 0 0 12px 0;
    color: var(--accent-primary);
    font-size: 1rem;
}

.detail-item {
    margin-bottom: 8px;
}

.detail-label {
    font-weight: 500;
    color: var(--text-secondary);
    display: block;
    margin-bottom: 2px;
}

.detail-value {
    color: var(--text-primary);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: var(--text-secondary);
}

.empty-state h3 {
    margin: 0 0 8px 0;
    font-size: 1.2rem;
}

.empty-state p {
    margin: 0;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .projects-header {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
    
    .header-actions {
        width: 100%;
        justify-content: center;
    }
    
    .projects-filters {
        flex-direction: column;
    }
    
    .projects-grid {
        grid-template-columns: 1fr;
        padding: 8px;
    }
    
    .project-details-content {
        grid-template-columns: 1fr;
    }
    
    .languages-checkboxes {
        flex-direction: column;
    }
}
</style>

<script>
window.initView = function() {
    const projectsGrid = document.getElementById('projects-grid');
    const newProjectBtn = document.getElementById('new-project-btn');
    const importProjectBtn = document.getElementById('import-project-btn');
    const searchInput = document.getElementById('search-projects');
    const filterStatus = document.getElementById('filter-status');
    const filterLanguage = document.getElementById('filter-language');
    const sortProjects = document.getElementById('sort-projects');
    
    // Modals
    const newProjectModal = document.getElementById('new-project-modal');
    const projectDetailsModal = document.getElementById('project-details-modal');
    
    let projects = [];
    let filteredProjects = [];
    
    // Charger les projets
    async function loadProjects() {
        try {
            AgentOne.ui.showLoader(projectsGrid, 'Chargement des projets...');
            
            const result = await AgentOne.api.post('projects.php', {
                action: 'getProjects'
            });
            
            if (result.success) {
                projects = result.data.projects || [];
                applyFilters();
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            projectsGrid.innerHTML = `<div class="empty-state">
                <h3>Erreur de chargement</h3>
                <p>${error.message}</p>
            </div>`;
        }
    }
    
    // Appliquer les filtres
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter = filterStatus.value;
        const languageFilter = filterLanguage.value;
        const sortBy = sortProjects.value;
        
        filteredProjects = projects.filter(project => {
            const matchesSearch = !searchTerm || 
                project.name.toLowerCase().includes(searchTerm) ||
                project.description.toLowerCase().includes(searchTerm);
            
            const matchesStatus = !statusFilter || project.status === statusFilter;
            
            const matchesLanguage = !languageFilter || 
                (project.languages && project.languages.includes(languageFilter));
            
            return matchesSearch && matchesStatus && matchesLanguage;
        });
        
        // Tri
        filteredProjects.sort((a, b) => {
            switch (sortBy) {
                case 'name':
                    return a.name.localeCompare(b.name);
                case 'created':
                    return new Date(b.created) - new Date(a.created);
                case 'status':
                    return a.status.localeCompare(b.status);
                case 'modified':
                default:
                    return new Date(b.modified) - new Date(a.modified);
            }
        });
        
        renderProjects();
    }
    
    // Rendu des projets
    function renderProjects() {
        if (filteredProjects.length === 0) {
            projectsGrid.innerHTML = `<div class="empty-state">
                <h3>Aucun projet trouvé</h3>
                <p>Créez votre premier projet ou ajustez vos filtres</p>
            </div>`;
            return;
        }
        
        projectsGrid.innerHTML = filteredProjects.map(project => `
            <div class="project-card" data-id="${project.id}">
                <div class="project-header">
                    <h3 class="project-title">${project.name}</h3>
                    <span class="project-status status-${project.status}">${getStatusLabel(project.status)}</span>
                </div>
                
                <p class="project-description">${project.description || 'Aucune description'}</p>
                
                <div class="project-meta">
                    ${(project.languages || []).map(lang => 
                        `<span class="language-badge">${lang.toUpperCase()}</span>`
                    ).join('')}
                </div>
                
                <div class="project-footer">
                    <span class="project-path">${project.path}</span>
                    <span class="project-date">${AgentOne.utils.formatDate(project.modified)}</span>
                </div>
            </div>
        `).join('');
        
        // Événements des cartes
        projectsGrid.querySelectorAll('.project-card').forEach(card => {
            card.addEventListener('click', () => {
                const projectId = card.dataset.id;
                showProjectDetails(projectId);
            });
        });
    }
    
    // Libellés des statuts
    function getStatusLabel(status) {
        const labels = {
            active: 'En cours',
            completed: 'Terminé',
            paused: 'En pause',
            archived: 'Archivé'
        };
        return labels[status] || status;
    }
    
    // Afficher les détails d'un projet
    function showProjectDetails(projectId) {
        const project = projects.find(p => p.id === projectId);
        if (!project) return;
        
        document.getElementById('project-details-title').textContent = project.name;
        
        const detailsContent = document.getElementById('project-details-content');
        detailsContent.innerHTML = `
            <div class="detail-section">
                <h4>Informations générales</h4>
                <div class="detail-item">
                    <span class="detail-label">Nom :</span>
                    <span class="detail-value">${project.name}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Statut :</span>
                    <span class="detail-value">${getStatusLabel(project.status)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Chemin :</span>
                    <span class="detail-value">${project.path}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Langages :</span>
                    <span class="detail-value">${(project.languages || []).join(', ') || 'Non spécifié'}</span>
                </div>
            </div>
            
            <div class="detail-section">
                <h4>Dates</h4>
                <div class="detail-item">
                    <span class="detail-label">Créé le :</span>
                    <span class="detail-value">${AgentOne.utils.formatDate(project.created)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Modifié le :</span>
                    <span class="detail-value">${AgentOne.utils.formatDate(project.modified)}</span>
                </div>
            </div>
            
            <div class="detail-section" style="grid-column: 1 / -1;">
                <h4>Description</h4>
                <p>${project.description || 'Aucune description'}</p>
            </div>
            
            <div class="detail-section" style="grid-column: 1 / -1;">
                <h4>Notes</h4>
                <p>${project.notes || 'Aucune note'}</p>
            </div>
        `;
        
        projectDetailsModal.style.display = 'flex';
        
        // Configurer les boutons d'action
        document.getElementById('open-project-btn').onclick = () => openProject(project);
        document.getElementById('export-project-btn').onclick = () => exportProject(project);
        document.getElementById('delete-project-btn').onclick = () => deleteProject(project);
    }
    
    // Ouvrir un projet
    function openProject(project) {
        // Rediriger vers l'explorateur de fichiers avec le chemin du projet
        window.parent.postMessage({
            type: 'OPEN_PROJECT',
            path: project.path
        }, '*');
        
        projectDetailsModal.style.display = 'none';
        AgentOne.ui.showNotification(`Projet "${project.name}" ouvert`, 'success');
    }
    
    // Exporter un projet
    async function exportProject(project) {
        try {
            const result = await AgentOne.api.post('projects.php', {
                action: 'exportProject',
                projectId: project.id
            });
            
            if (result.success) {
                // Télécharger le fichier
                const blob = new Blob([result.data.content], { type: 'application/zip' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = result.data.filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                AgentOne.ui.showNotification('Projet exporté avec succès', 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Supprimer un projet
    async function deleteProject(project) {
        if (!confirm(`Êtes-vous sûr de vouloir supprimer le projet "${project.name}" ?\n\nCette action supprimera uniquement les métadonnées, pas les fichiers du projet.`)) {
            return;
        }
        
        try {
            const result = await AgentOne.api.post('projects.php', {
                action: 'deleteProject',
                projectId: project.id
            });
            
            if (result.success) {
                projectDetailsModal.style.display = 'none';
                loadProjects();
                AgentOne.ui.showNotification('Projet supprimé', 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Créer un nouveau projet
    async function createProject(projectData) {
        try {
            const result = await AgentOne.api.post('projects.php', {
                action: 'createProject',
                project: projectData
            });
            
            if (result.success) {
                newProjectModal.style.display = 'none';
                loadProjects();
                AgentOne.ui.showNotification('Projet créé avec succès', 'success');
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            AgentOne.ui.showNotification(`Erreur : ${error.message}`, 'error');
        }
    }
    
    // Événements des filtres
    searchInput.addEventListener('input', AgentOne.utils.debounce(applyFilters, 300));
    filterStatus.addEventListener('change', applyFilters);
    filterLanguage.addEventListener('change', applyFilters);
    sortProjects.addEventListener('change', applyFilters);
    
    // Événements des boutons
    newProjectBtn.addEventListener('click', () => {
        newProjectModal.style.display = 'flex';
    });
    
    // Gestion du modal nouveau projet
    document.getElementById('save-project-btn').addEventListener('click', () => {
        const form = document.getElementById('new-project-form');
        const formData = new FormData(form);
        
        const languages = Array.from(form.querySelectorAll('input[name="languages"]:checked'))
            .map(cb => cb.value);
        
        const projectData = {
            name: formData.get('project-name'),
            description: formData.get('project-description'),
            path: formData.get('project-path'),
            languages: languages,
            status: formData.get('project-status'),
            notes: formData.get('project-notes')
        };
        
        if (!projectData.name || !projectData.path) {
            AgentOne.ui.showNotification('Nom et chemin du projet requis', 'error');
            return;
        }
        
        createProject(projectData);
    });
    
    document.getElementById('cancel-project-btn').addEventListener('click', () => {
        newProjectModal.style.display = 'none';
    });
    
    // Fermeture des modals
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
    
    // Parcourir le chemin
    document.getElementById('browse-path-btn').addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.webkitdirectory = true;
        input.onchange = (e) => {
            if (e.target.files.length > 0) {
                const path = e.target.files[0].webkitRelativePath.split('/')[0];
                document.getElementById('project-path').value = path;
            }
        };
        input.click();
    });
    
    // Initialisation
    loadProjects();
};
</script>