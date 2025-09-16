<?php
/**
 * Vue Aide - Documentation et guide d'utilisation
 */
?>

<div class="help-view">
    <div class="help-sidebar">
        <div class="sidebar-header">
            <h3>📚 Documentation</h3>
        </div>
        <div class="help-nav" id="help-nav">
            <div class="nav-section">
                <h4>Démarrage</h4>
                <a href="#getting-started" class="nav-link active">🚀 Premiers pas</a>
                <a href="#interface" class="nav-link">🖥️ Interface</a>
                <a href="#navigation" class="nav-link">🧭 Navigation</a>
            </div>
            <div class="nav-section">
                <h4>Fonctionnalités</h4>
                <a href="#chat" class="nav-link">💬 Chat Assistant</a>
                <a href="#files" class="nav-link">📁 Gestionnaire de fichiers</a>
                <a href="#editor" class="nav-link">📝 Éditeur de code</a>
                <a href="#terminal" class="nav-link">⚡ Terminal</a>
                <a href="#server" class="nav-link">🖥️ Serveur</a>
                <a href="#database" class="nav-link">🗄️ Base de données</a>
                <a href="#browser" class="nav-link">🌐 Navigateur</a>
                <a href="#projects" class="nav-link">📂 Projets</a>
                <a href="#prompts" class="nav-link">📝 Prompts</a>
            </div>
            <div class="nav-section">
                <h4>Avancé</h4>
                <a href="#commands" class="nav-link">⌨️ Commandes</a>
                <a href="#shortcuts" class="nav-link">⚡ Raccourcis</a>
                <a href="#troubleshooting" class="nav-link">🔧 Dépannage</a>
                <a href="#faq" class="nav-link">❓ FAQ</a>
            </div>
        </div>
    </div>
    
    <div class="help-content" id="help-content">
        <div class="help-header">
            <h1>📖 Guide d'utilisation SGC-AgentOne v3.0</h1>
            <p class="help-subtitle">Assistant universel de développement et gestion de projets</p>
        </div>
        
        <div class="help-search">
            <input type="text" id="search-help" class="form-control" placeholder="🔍 Rechercher dans la documentation...">
        </div>
        
        <div class="help-sections" id="help-sections">
            <!-- Contenu dynamique -->
        </div>
    </div>
</div>

<style>
.help-view {
    height: 100%;
    display: flex;
    background: var(--bg-primary);
}

.help-sidebar {
    width: 280px;
    background: var(--bg-secondary);
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
}

.sidebar-header h3 {
    margin: 0;
    color: var(--accent-primary);
    font-size: 1.1rem;
}

.help-nav {
    flex: 1;
    overflow-y: auto;
    padding: 16px 0;
}

.nav-section {
    margin-bottom: 24px;
}

.nav-section h4 {
    margin: 0 0 8px 0;
    padding: 0 16px;
    color: var(--text-secondary);
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-link {
    display: block;
    padding: 8px 16px;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: var(--accent-secondary);
    color: var(--text-primary);
}

.nav-link.active {
    background: var(--accent-primary);
    color: var(--bg-primary);
    border-left-color: var(--bg-primary);
}

.help-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.help-header {
    padding: 24px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    text-align: center;
}

.help-header h1 {
    margin: 0 0 8px 0;
    color: var(--accent-primary);
    font-size: 2rem;
    font-weight: 700;
}

.help-subtitle {
    margin: 0;
    color: var(--text-secondary);
    font-size: 1.1rem;
}

.help-search {
    padding: 16px 24px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.help-sections {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
}

.help-section {
    margin-bottom: 48px;
    display: none;
}

.help-section.active {
    display: block;
}

.help-section h2 {
    margin: 0 0 16px 0;
    color: var(--accent-primary);
    font-size: 1.8rem;
    font-weight: 600;
    border-bottom: 2px solid var(--accent-primary);
    padding-bottom: 8px;
}

.help-section h3 {
    margin: 24px 0 12px 0;
    color: var(--text-primary);
    font-size: 1.3rem;
    font-weight: 600;
}

.help-section h4 {
    margin: 20px 0 8px 0;
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: 500;
}

.help-section p {
    margin: 0 0 16px 0;
    line-height: 1.6;
    color: var(--text-primary);
}

.help-section ul,
.help-section ol {
    margin: 0 0 16px 0;
    padding-left: 24px;
}

.help-section li {
    margin-bottom: 8px;
    line-height: 1.5;
    color: var(--text-primary);
}

.help-section code {
    background: var(--bg-tertiary);
    color: var(--accent-primary);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
}

.help-section pre {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    padding: 16px;
    border-radius: 8px;
    overflow-x: auto;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 16px 0;
    border-left: 4px solid var(--accent-primary);
}

.help-section blockquote {
    background: var(--bg-secondary);
    border-left: 4px solid var(--accent-primary);
    padding: 16px;
    margin: 16px 0;
    border-radius: 0 8px 8px 0;
    font-style: italic;
}

.help-section .tip {
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: 8px;
    padding: 16px;
    margin: 16px 0;
}

.help-section .warning {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 8px;
    padding: 16px;
    margin: 16px 0;
}

.help-section .danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 8px;
    padding: 16px;
    margin: 16px 0;
}

.command-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
    margin: 16px 0;
}

.command-item {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 16px;
}

.command-item h5 {
    margin: 0 0 8px 0;
    color: var(--accent-primary);
    font-family: 'JetBrains Mono', monospace;
}

.command-item p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.shortcut-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
    margin: 16px 0;
}

.shortcut-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: var(--bg-secondary);
    border-radius: 6px;
    border: 1px solid var(--border-color);
}

.shortcut-key {
    background: var(--bg-tertiary);
    color: var(--accent-primary);
    padding: 4px 8px;
    border-radius: 4px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .help-view {
        flex-direction: column;
    }
    
    .help-sidebar {
        width: 100%;
        height: 200px;
        border-right: none;
        border-bottom: 1px solid var(--border-color);
    }
    
    .help-nav {
        display: flex;
        overflow-x: auto;
        padding: 8px 16px;
    }
    
    .nav-section {
        margin-right: 24px;
        margin-bottom: 0;
        min-width: 150px;
    }
    
    .help-header h1 {
        font-size: 1.5rem;
    }
    
    .help-sections {
        padding: 16px;
    }
    
    .command-list,
    .shortcut-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
window.initView = function() {
    const helpNav = document.getElementById('help-nav');
    const helpSections = document.getElementById('help-sections');
    const searchInput = document.getElementById('search-help');
    
    let currentSection = 'getting-started';
    
    // Contenu de l'aide
    const helpContent = {
        'getting-started': {
            title: '🚀 Premiers pas',
            content: `
                <h3>Bienvenue dans SGC-AgentOne v3.0</h3>
                <p>SGC-AgentOne est un assistant universel de développement qui transforme votre navigateur en un environnement de développement complet. Cette version 3.0 apporte une architecture modulaire et une interface repensée pour une meilleure productivité.</p>
                
                <h4>Installation et démarrage</h4>
                <ol>
                    <li>Téléchargez et décompressez le projet dans votre dossier web</li>
                    <li>Lancez le serveur avec le script approprié :
                        <pre>Windows : start-server.bat
Linux/Mac : ./start-server.sh</pre>
                    </li>
                    <li>Ouvrez votre navigateur sur <code>http://localhost:5000</code></li>
                </ol>
                
                <div class="tip">
                    <strong>💡 Conseil :</strong> SGC-AgentOne fonctionne sur tous les systèmes : XAMPP, serveurs mutualisés, Windows, Linux, macOS. Aucune configuration supplémentaire n'est requise.
                </div>
                
                <h4>Première utilisation</h4>
                <p>Une fois l'interface chargée, vous pouvez immédiatement :</p>
                <ul>
                    <li>Utiliser le chat assistant pour créer des fichiers</li>
                    <li>Explorer vos fichiers avec le gestionnaire intégré</li>
                    <li>Éditer du code avec la coloration syntaxique</li>
                    <li>Contrôler le serveur PHP intégré</li>
                </ul>
            `
        },
        
        'interface': {
            title: '🖥️ Interface',
            content: `
                <h3>Vue d'ensemble de l'interface</h3>
                <p>L'interface de SGC-AgentOne est conçue pour être intuitive et productive. Elle s'adapte automatiquement à votre écran et à votre façon de travailler.</p>
                
                <h4>Header (Barre supérieure)</h4>
                <ul>
                    <li><strong>Logo et titre :</strong> Informations sur la version</li>
                    <li><strong>Menu de navigation :</strong> Accès rapide à toutes les vues</li>
                    <li><strong>Indicateurs :</strong> Statut du serveur et informations système</li>
                </ul>
                
                <h4>Zone principale</h4>
                <p>La zone principale affiche la vue sélectionnée. Chaque vue est optimisée pour sa fonction spécifique :</p>
                <ul>
                    <li><strong>Chat :</strong> Interface conversationnelle avec l'assistant</li>
                    <li><strong>Fichiers :</strong> Explorateur avec arborescence et actions</li>
                    <li><strong>Éditeur :</strong> Éditeur de code avec coloration syntaxique</li>
                    <li><strong>Terminal :</strong> Émulateur de terminal web</li>
                    <li><strong>Et plus...</strong></li>
                </ul>
                
                <h4>Footer (Barre inférieure)</h4>
                <ul>
                    <li><strong>Statut serveur :</strong> État de connexion et port</li>
                    <li><strong>Informations projet :</strong> Projet actuel et fichier ouvert</li>
                    <li><strong>Horodatage :</strong> Date et heure actuelles</li>
                </ul>
                
                <div class="tip">
                    <strong>💡 Responsive :</strong> L'interface s'adapte automatiquement aux écrans mobiles et tablettes pour une utilisation optimale sur tous les appareils.
                </div>
            `
        },
        
        'chat': {
            title: '💬 Chat Assistant',
            content: `
                <h3>Assistant IA intégré</h3>
                <p>Le chat assistant est le cœur de SGC-AgentOne. Il comprend le langage naturel et peut exécuter des actions complexes sur vos fichiers et projets.</p>
                
                <h4>Commandes de base</h4>
                <div class="command-list">
                    <div class="command-item">
                        <h5>createFile nom.txt : contenu</h5>
                        <p>Crée un nouveau fichier avec le contenu spécifié</p>
                    </div>
                    <div class="command-item">
                        <h5>readFile nom.txt</h5>
                        <p>Lit et affiche le contenu d'un fichier</p>
                    </div>
                    <div class="command-item">
                        <h5>listDir dossier</h5>
                        <p>Liste le contenu d'un dossier</p>
                    </div>
                    <div class="command-item">
                        <h5>createDir nouveau-dossier</h5>
                        <p>Crée un nouveau dossier</p>
                    </div>
                </div>
                
                <h4>Fonctionnalités avancées</h4>
                <ul>
                    <li><strong>Historique :</strong> Naviguez dans vos commandes précédentes avec ↑/↓</li>
                    <li><strong>Auto-complétion :</strong> Suggestions intelligentes pendant la frappe</li>
                    <li><strong>Réponses contextuelles :</strong> L'assistant comprend le contexte de votre projet</li>
                    <li><strong>Gestion d'erreurs :</strong> Messages d'erreur clairs et suggestions de correction</li>
                </ul>
                
                <div class="warning">
                    <strong>⚠️ Sécurité :</strong> Toutes les actions sont limitées au dossier de votre projet. L'assistant ne peut pas accéder aux fichiers système.
                </div>
            `
        },
        
        'commands': {
            title: '⌨️ Commandes',
            content: `
                <h3>Liste complète des commandes</h3>
                <p>Voici toutes les commandes disponibles dans SGC-AgentOne, organisées par catégorie.</p>
                
                <h4>Gestion des fichiers</h4>
                <div class="command-list">
                    <div class="command-item">
                        <h5>createFile chemin/nom.ext : contenu</h5>
                        <p>Crée un fichier avec le contenu spécifié</p>
                    </div>
                    <div class="command-item">
                        <h5>readFile chemin/nom.ext</h5>
                        <p>Lit le contenu d'un fichier</p>
                    </div>
                    <div class="command-item">
                        <h5>deleteFile chemin/nom.ext</h5>
                        <p>Supprime un fichier</p>
                    </div>
                    <div class="command-item">
                        <h5>renameFile ancien.ext nouveau.ext</h5>
                        <p>Renomme un fichier</p>
                    </div>
                </div>
                
                <h4>Gestion des dossiers</h4>
                <div class="command-list">
                    <div class="command-item">
                        <h5>createDir chemin/dossier</h5>
                        <p>Crée un nouveau dossier</p>
                    </div>
                    <div class="command-item">
                        <h5>listDir chemin/dossier</h5>
                        <p>Liste le contenu d'un dossier</p>
                    </div>
                    <div class="command-item">
                        <h5>deleteDir chemin/dossier</h5>
                        <p>Supprime un dossier vide</p>
                    </div>
                </div>
                
                <h4>Contrôle du serveur</h4>
                <div class="command-list">
                    <div class="command-item">
                        <h5>startServer</h5>
                        <p>Démarre le serveur PHP intégré</p>
                    </div>
                    <div class="command-item">
                        <h5>stopServer</h5>
                        <p>Arrête le serveur PHP</p>
                    </div>
                    <div class="command-item">
                        <h5>serverStatus</h5>
                        <p>Affiche l'état du serveur</p>
                    </div>
                </div>
            `
        },
        
        'shortcuts': {
            title: '⚡ Raccourcis clavier',
            content: `
                <h3>Raccourcis clavier</h3>
                <p>Gagnez en productivité avec ces raccourcis clavier disponibles dans toute l'application.</p>
                
                <h4>Navigation générale</h4>
                <div class="shortcut-grid">
                    <div class="shortcut-item">
                        <span>Basculer entre les vues</span>
                        <span class="shortcut-key">Ctrl + 1-9</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Recherche globale</span>
                        <span class="shortcut-key">Ctrl + K</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Fermer les modals</span>
                        <span class="shortcut-key">Échap</span>
                    </div>
                </div>
                
                <h4>Chat Assistant</h4>
                <div class="shortcut-grid">
                    <div class="shortcut-item">
                        <span>Envoyer message</span>
                        <span class="shortcut-key">Entrée</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Historique précédent</span>
                        <span class="shortcut-key">↑</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Historique suivant</span>
                        <span class="shortcut-key">↓</span>
                    </div>
                </div>
                
                <h4>Éditeur de code</h4>
                <div class="shortcut-grid">
                    <div class="shortcut-item">
                        <span>Sauvegarder</span>
                        <span class="shortcut-key">Ctrl + S</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Rechercher</span>
                        <span class="shortcut-key">Ctrl + F</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Remplacer</span>
                        <span class="shortcut-key">Ctrl + H</span>
                    </div>
                </div>
                
                <h4>Terminal</h4>
                <div class="shortcut-grid">
                    <div class="shortcut-item">
                        <span>Exécuter commande</span>
                        <span class="shortcut-key">Entrée</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Auto-complétion</span>
                        <span class="shortcut-key">Tab</span>
                    </div>
                    <div class="shortcut-item">
                        <span>Historique commandes</span>
                        <span class="shortcut-key">↑ / ↓</span>
                    </div>
                </div>
            `
        },
        
        'troubleshooting': {
            title: '🔧 Dépannage',
            content: `
                <h3>Résolution des problèmes courants</h3>
                <p>Solutions aux problèmes les plus fréquents rencontrés avec SGC-AgentOne.</p>
                
                <h4>Le serveur ne démarre pas</h4>
                <div class="warning">
                    <strong>Symptômes :</strong> Erreur "Port déjà utilisé" ou "Impossible de démarrer le serveur"
                </div>
                <p><strong>Solutions :</strong></p>
                <ol>
                    <li>Vérifiez qu'aucun autre serveur n'utilise le port 5000</li>
                    <li>Changez le port dans Serveur → Configuration</li>
                    <li>Redémarrez votre ordinateur si nécessaire</li>
                </ol>
                
                <h4>L'interface ne se charge pas</h4>
                <div class="warning">
                    <strong>Symptômes :</strong> Page blanche ou erreurs JavaScript
                </div>
                <p><strong>Solutions :</strong></p>
                <ol>
                    <li>Videz le cache de votre navigateur (Ctrl+F5)</li>
                    <li>Vérifiez que JavaScript est activé</li>
                    <li>Testez avec un autre navigateur</li>
                    <li>Vérifiez les permissions des fichiers</li>
                </ol>
                
                <h4>Les commandes ne fonctionnent pas</h4>
                <div class="warning">
                    <strong>Symptômes :</strong> "Erreur de connexion" dans le chat
                </div>
                <p><strong>Solutions :</strong></p>
                <ol>
                    <li>Vérifiez que le serveur PHP est démarré</li>
                    <li>Contrôlez les permissions des dossiers core/ et api/</li>
                    <li>Vérifiez les logs d'erreur PHP</li>
                </ol>
                
                <h4>Problèmes de permissions</h4>
                <div class="danger">
                    <strong>Symptômes :</strong> "Permission denied" ou "Impossible d'écrire"
                </div>
                <p><strong>Solutions :</strong></p>
                <pre>chmod -R 755 sgc-agentone/
chmod -R 644 sgc-agentone/*.php
chmod -R 755 sgc-agentone/core/logs/
chmod -R 755 sgc-agentone/core/db/</pre>
                
                <h4>Obtenir de l'aide</h4>
                <p>Si les solutions ci-dessus ne résolvent pas votre problème :</p>
                <ul>
                    <li>Consultez les logs dans core/logs/</li>
                    <li>Activez le mode debug dans les paramètres</li>
                    <li>Vérifiez la configuration PHP (version, extensions)</li>
                </ul>
            `
        },
        
        'faq': {
            title: '❓ Questions fréquentes',
            content: `
                <h3>Questions fréquemment posées</h3>
                
                <h4>SGC-AgentOne est-il gratuit ?</h4>
                <p>Oui, SGC-AgentOne est entièrement gratuit et open source. Vous pouvez l'utiliser, le modifier et le distribuer librement.</p>
                
                <h4>Quels sont les prérequis système ?</h4>
                <p>SGC-AgentOne nécessite uniquement :</p>
                <ul>
                    <li>PHP 7.4 ou supérieur</li>
                    <li>Un serveur web (Apache, Nginx, ou serveur intégré PHP)</li>
                    <li>Un navigateur moderne avec JavaScript activé</li>
                </ul>
                
                <h4>Puis-je utiliser SGC-AgentOne sur un serveur mutualisé ?</h4>
                <p>Oui, SGC-AgentOne est conçu pour fonctionner sur tous types d'hébergement, y compris les serveurs mutualisés. Aucune configuration spéciale n'est requise.</p>
                
                <h4>Les données sont-elles sécurisées ?</h4>
                <p>Oui, SGC-AgentOne implémente plusieurs mesures de sécurité :</p>
                <ul>
                    <li>Protection contre les path traversal</li>
                    <li>Validation de toutes les entrées utilisateur</li>
                    <li>Limitation des actions au dossier du projet</li>
                    <li>Logs détaillés de toutes les actions</li>
                </ul>
                
                <h4>Comment sauvegarder mes projets ?</h4>
                <p>Plusieurs options sont disponibles :</p>
                <ul>
                    <li>Export automatique via le gestionnaire de projets</li>
                    <li>Sauvegarde manuelle des dossiers core/db/ et core/logs/</li>
                    <li>Utilisation d'outils de versioning comme Git</li>
                </ul>
                
                <h4>Puis-je personnaliser l'interface ?</h4>
                <p>Oui, vous pouvez :</p>
                <ul>
                    <li>Modifier les couleurs dans Paramètres → Apparence</li>
                    <li>Changer le titre et le logo de l'application</li>
                    <li>Ajuster la configuration du serveur</li>
                    <li>Créer vos propres prompts et templates</li>
                </ul>
                
                <h4>SGC-AgentOne fonctionne-t-il hors ligne ?</h4>
                <p>Une fois chargé, SGC-AgentOne peut fonctionner en mode local sans connexion Internet. Seules certaines fonctionnalités comme la recherche web nécessitent une connexion.</p>
                
                <h4>Comment mettre à jour SGC-AgentOne ?</h4>
                <p>Pour mettre à jour :</p>
                <ol>
                    <li>Sauvegardez vos données (core/db/, core/logs/, projets)</li>
                    <li>Téléchargez la nouvelle version</li>
                    <li>Remplacez tous les fichiers sauf vos données</li>
                    <li>Testez le fonctionnement</li>
                </ol>
            `
        }
    };
    
    // Générer le contenu des sections
    function generateSections() {
        helpSections.innerHTML = Object.keys(helpContent).map(sectionId => `
            <div class="help-section ${sectionId === currentSection ? 'active' : ''}" id="section-${sectionId}">
                <h2>${helpContent[sectionId].title}</h2>
                ${helpContent[sectionId].content}
            </div>
        `).join('');
    }
    
    // Naviguer vers une section
    function navigateToSection(sectionId) {
        if (!helpContent[sectionId]) return;
        
        currentSection = sectionId;
        
        // Mettre à jour la navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`[href="#${sectionId}"]`).classList.add('active');
        
        // Afficher la section
        document.querySelectorAll('.help-section').forEach(section => {
            section.classList.remove('active');
        });
        document.getElementById(`section-${sectionId}`).classList.add('active');
        
        // Scroll vers le haut
        helpSections.scrollTop = 0;
    }
    
    // Recherche dans la documentation
    function searchHelp(query) {
        if (!query) {
            generateSections();
            return;
        }
        
        const results = [];
        Object.keys(helpContent).forEach(sectionId => {
            const section = helpContent[sectionId];
            const content = section.content.toLowerCase();
            const title = section.title.toLowerCase();
            
            if (title.includes(query.toLowerCase()) || content.includes(query.toLowerCase())) {
                results.push(sectionId);
            }
        });
        
        // Afficher les résultats
        helpSections.innerHTML = results.map(sectionId => `
            <div class="help-section active" id="section-${sectionId}">
                <h2>${helpContent[sectionId].title}</h2>
                ${helpContent[sectionId].content}
            </div>
        `).join('');
        
        if (results.length === 0) {
            helpSections.innerHTML = `
                <div class="help-section active">
                    <h2>🔍 Aucun résultat</h2>
                    <p>Aucun résultat trouvé pour "${query}". Essayez avec d'autres mots-clés.</p>
                </div>
            `;
        }
    }
    
    // Événements de navigation
    helpNav.addEventListener('click', (e) => {
        if (e.target.classList.contains('nav-link')) {
            e.preventDefault();
            const sectionId = e.target.getAttribute('href').substring(1);
            navigateToSection(sectionId);
        }
    });
    
    // Recherche
    searchInput.addEventListener('input', AgentOne.utils.debounce(() => {
        const query = searchInput.value.trim();
        searchHelp(query);
    }, 300));
    
    // Initialisation
    generateSections();
};
</script>