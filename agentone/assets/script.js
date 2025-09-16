/**
 * SGC-AgentOne v3.0 - Scripts globaux
 * Fonctionnalités communes à toutes les vues
 */

// Configuration globale
const AgentOne = {
    baseUrl: window.location.pathname.replace(/\/[^\/]*$/, ''),
    currentView: new URLSearchParams(window.location.search).get('view') || 'chat',
    
    // Utilitaires
    utils: {
        // Formatage des dates
        formatDate: (date) => {
            return new Date(date).toLocaleString('fr-FR');
        },
        
        // Formatage des tailles de fichiers
        formatFileSize: (bytes) => {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        
        // Debounce pour les recherches
        debounce: (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        // Copier dans le presse-papiers
        copyToClipboard: async (text) => {
            try {
                await navigator.clipboard.writeText(text);
                AgentOne.ui.showNotification('Copié dans le presse-papiers', 'success');
            } catch (err) {
                console.error('Erreur copie:', err);
                AgentOne.ui.showNotification('Erreur lors de la copie', 'error');
            }
        }
    },
    
    // Interface utilisateur
    ui: {
        // Afficher une notification
        showNotification: (message, type = 'info', duration = 3000) => {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            
            // Styles inline pour éviter les conflits
            Object.assign(notification.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '12px 16px',
                borderRadius: '8px',
                color: 'white',
                fontWeight: '500',
                zIndex: '10000',
                transform: 'translateX(100%)',
                transition: 'transform 0.3s ease',
                maxWidth: '300px',
                wordWrap: 'break-word'
            });
            
            // Couleurs selon le type
            const colors = {
                success: '#22c55e',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            notification.style.backgroundColor = colors[type] || colors.info;
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Suppression automatique
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, duration);
        },
        
        // Modal simple
        showModal: (title, content, actions = []) => {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>${title}</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                    <div class="modal-footer">
                        ${actions.map(action => 
                            `<button class="btn ${action.class || 'btn-secondary'}" data-action="${action.action}">${action.text}</button>`
                        ).join('')}
                    </div>
                </div>
            `;
            
            // Styles inline
            Object.assign(modal.style, {
                position: 'fixed',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                zIndex: '10000'
            });
            
            const modalContent = modal.querySelector('.modal-content');
            Object.assign(modalContent.style, {
                backgroundColor: 'var(--bg-secondary)',
                borderRadius: '12px',
                padding: '0',
                maxWidth: '500px',
                width: '90%',
                maxHeight: '80vh',
                overflow: 'auto'
            });
            
            document.body.appendChild(modal);
            
            // Gestion des événements
            modal.addEventListener('click', (e) => {
                if (e.target === modal || e.target.classList.contains('modal-close')) {
                    document.body.removeChild(modal);
                }
                
                if (e.target.hasAttribute('data-action')) {
                    const action = e.target.getAttribute('data-action');
                    const actionHandler = actions.find(a => a.action === action);
                    if (actionHandler && actionHandler.handler) {
                        actionHandler.handler();
                    }
                    document.body.removeChild(modal);
                }
            });
            
            return modal;
        },
        
        // Loader
        showLoader: (container, message = 'Chargement...') => {
            container.innerHTML = `
                <div class="loading">
                    ${message}
                </div>
            `;
        }
    },
    
    // API
    api: {
        // Requête GET
        get: async (endpoint) => {
            try {
                const response = await fetch(`${AgentOne.baseUrl}/api/${endpoint}`);
                return await response.json();
            } catch (error) {
                console.error('Erreur API GET:', error);
                throw error;
            }
        },
        
        // Requête POST
        post: async (endpoint, data) => {
            try {
                const response = await fetch(`${AgentOne.baseUrl}/api/${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                return await response.json();
            } catch (error) {
                console.error('Erreur API POST:', error);
                throw error;
            }
        },
        
        // Upload de fichier
        upload: async (endpoint, formData) => {
            try {
                const response = await fetch(`${AgentOne.baseUrl}/api/${endpoint}`, {
                    method: 'POST',
                    body: formData
                });
                return await response.json();
            } catch (error) {
                console.error('Erreur API UPLOAD:', error);
                throw error;
            }
        }
    },
    
    // Stockage local
    storage: {
        set: (key, value) => {
            localStorage.setItem(`agentone_${key}`, JSON.stringify(value));
        },
        
        get: (key, defaultValue = null) => {
            const item = localStorage.getItem(`agentone_${key}`);
            return item ? JSON.parse(item) : defaultValue;
        },
        
        remove: (key) => {
            localStorage.removeItem(`agentone_${key}`);
        }
    }
};

// Initialisation globale
document.addEventListener('DOMContentLoaded', () => {
    // Mise à jour du timestamp dans le footer
    const updateTimestamp = () => {
        const timestampEl = document.getElementById('timestamp');
        if (timestampEl) {
            timestampEl.textContent = new Date().toLocaleString('fr-FR');
        }
    };
    
    updateTimestamp();
    setInterval(updateTimestamp, 1000);
    
    // Gestion des raccourcis clavier globaux
    document.addEventListener('keydown', (e) => {
        // Ctrl+K pour la recherche globale
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            // TODO: Implémenter la recherche globale
        }
        
        // Échap pour fermer les modals
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal-overlay');
            modals.forEach(modal => {
                if (modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            });
        }
    });
    
    // Gestion responsive du menu
    const handleResize = () => {
        const isMobile = window.innerWidth <= 768;
        document.body.classList.toggle('mobile', isMobile);
    };
    
    handleResize();
    window.addEventListener('resize', handleResize);
    
    // Auto-sauvegarde des formulaires
    const autoSaveForms = () => {
        const forms = document.querySelectorAll('form[data-autosave]');
        forms.forEach(form => {
            const formId = form.getAttribute('data-autosave');
            
            // Restaurer les données sauvegardées
            const savedData = AgentOne.storage.get(`form_${formId}`);
            if (savedData) {
                Object.keys(savedData).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = savedData[key];
                    }
                });
            }
            
            // Sauvegarder lors des changements
            form.addEventListener('input', AgentOne.utils.debounce(() => {
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                AgentOne.storage.set(`form_${formId}`, data);
            }, 500));
        });
    };
    
    autoSaveForms();
    
    // Initialisation spécifique à la vue
    if (typeof window.initView === 'function') {
        window.initView();
    }
});

// Export global
window.AgentOne = AgentOne;