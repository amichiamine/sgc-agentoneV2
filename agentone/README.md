# 🚀 SGC-AgentOne v3.0 - Architecture Modulaire

**Assistant universel de développement - Interface web complète**

## ✨ Nouveautés Version 3.0

- 🏗️ **Architecture modulaire** : Vues séparées et indépendantes
- 🎨 **Interface repensée** : Design moderne et responsive
- ⚡ **Performance optimisée** : Chargement rapide et fluidité
- 🔧 **Fonctionnalités étendues** : Tous les outils en un seul endroit
- 🛡️ **Sécurité renforcée** : Protection avancée et logs détaillés
- 📱 **Mobile-first** : Optimisé pour tous les appareils

## 🚀 Installation Ultra-Simple

### 1. Téléchargement
```bash
# Télécharger et décompresser dans votre dossier web
# Aucune configuration requise !
```

### 2. Démarrage
```bash
# Windows
start-server.bat

# Linux/Mac
chmod +x start-server.sh
./start-server.sh

# Ou directement avec PHP
php -S 0.0.0.0:5000 -t . index.php
```

### 3. Accès
```
http://localhost:5000
```

## 🎯 Fonctionnalités Complètes

### 💬 Chat Assistant IA
- Commandes en langage naturel
- Gestion complète des fichiers et dossiers
- Historique et auto-complétion
- Réponses contextuelles intelligentes

### 📁 Gestionnaire de Fichiers
- Explorateur visuel avec arborescence
- Upload/download par glisser-déposer
- Actions contextuelles (copier, déplacer, renommer)
- Aperçu et édition rapide

### 📝 Éditeur de Code
- Coloration syntaxique multi-langages
- Numérotation des lignes
- Onglets multiples
- Sauvegarde automatique
- Recherche et remplacement

### ⚡ Terminal Web
- Émulateur de terminal complet
- Commandes système de base
- Historique et auto-complétion
- Onglets multiples

### 🖥️ Contrôle Serveur
- Démarrage/arrêt du serveur PHP
- Configuration des ports et paramètres
- Monitoring en temps réel
- Logs détaillés

### 🗄️ Base de Données SQLite
- Interface graphique pour SQLite
- Éditeur de requêtes SQL
- Visualisation des données
- Import/export de schémas

### 🌐 Navigateur Intégré
- Navigation web avec onglets
- Prévisualisation des projets
- Outils développeur basiques
- Gestion des favoris

### 📂 Gestionnaire de Projets
- Organisation par projets
- Métadonnées et statuts
- Export/import de projets
- Recherche et filtrage

### 📝 Gestionnaire de Prompts
- Templates de commandes réutilisables
- Catégorisation et tags
- Raccourcis clavier personnalisés
- Statistiques d'utilisation

### ⚙️ Paramètres Avancés
- Personnalisation complète de l'interface
- Configuration serveur et sécurité
- Thèmes et couleurs
- Maintenance et sauvegarde

## 📋 Prérequis

- **PHP** : Version 7.4 ou supérieure
- **Extensions PHP** : json, mbstring, fileinfo, session, sqlite3
- **Serveur Web** : Apache (avec mod_rewrite) ou Nginx
- **Navigateur** : Chrome, Firefox, Safari, Edge (version récente)

## 🔧 Configuration

### Serveurs Web

#### Apache (.htaccess inclus)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Permissions
```bash
# Linux/Mac
chmod -R 755 agentone/
chmod -R 644 agentone/*.php
chmod -R 755 agentone/core/logs/
chmod -R 755 agentone/core/db/

# Windows : Propriétés → Sécurité → Contrôle total
```

## 🎨 Personnalisation

### Thèmes et Couleurs
- Interface entièrement personnalisable
- Mode sombre/clair
- Palette de couleurs configurable
- Polices et tailles ajustables

### Extensions
- Architecture modulaire permettant l'ajout de nouvelles vues
- API extensible pour de nouvelles fonctionnalités
- Système de plugins (à venir)

## 🛡️ Sécurité

### Mesures Implémentées
- ✅ Protection contre les path traversal
- ✅ Validation de toutes les entrées
- ✅ Limitation aux dossiers autorisés
- ✅ Logs détaillés de toutes les actions
- ✅ Sessions sécurisées
- ✅ Headers de sécurité

### Mode Blind-Exec
- Désactivé par défaut
- Permet l'exécution automatique (avec précautions)
- Liste blanche des actions autorisées
- Logs renforcés en mode automatique

## 📱 Compatibilité Mobile

### Responsive Design
- Interface adaptative automatique
- Navigation optimisée pour le tactile
- Gestes et interactions mobiles
- Performance optimisée

### PWA (Progressive Web App)
- Installation sur l'écran d'accueil
- Fonctionnement hors ligne partiel
- Notifications push (à venir)
- Synchronisation multi-appareils (à venir)

## 🔄 Migration depuis v2.x

### Automatique
- Les paramètres sont migrés automatiquement
- Les projets existants sont préservés
- Les logs sont conservés

### Manuelle (si nécessaire)
1. Sauvegardez `core/db/` et `core/logs/`
2. Installez la v3.0
3. Restaurez vos données
4. Vérifiez la configuration

## 🆘 Dépannage

### Problèmes Courants

#### Serveur ne démarre pas
```bash
# Vérifier le port
netstat -an | grep :5000

# Changer le port dans les paramètres
# Ou utiliser un port différent
php -S 0.0.0.0:8080 -t . index.php
```

#### Interface ne se charge pas
```bash
# Vider le cache navigateur
Ctrl+F5

# Vérifier les permissions
ls -la agentone/

# Vérifier les logs PHP
tail -f /var/log/apache2/error.log
```

#### Commandes ne fonctionnent pas
```bash
# Vérifier l'API
curl http://localhost:5000/api/chat.php

# Vérifier les permissions
chmod 755 agentone/api/
```

### Mode Debug
Activez le mode debug dans Paramètres → Serveur pour des logs détaillés.

### Support
- 📖 Documentation intégrée : Vue Aide
- 🔍 Recherche dans l'aide : Ctrl+K
- 📝 Logs détaillés : `core/logs/`
- 🐛 Issues : Consultez les logs d'erreur

## 📊 Statistiques et Monitoring

### Logs Disponibles
- `actions.log` : Toutes les actions utilisateur
- `chat.log` : Conversations avec l'assistant
- `server.log` : Événements du serveur
- `errors.log` : Erreurs système

### Métriques
- Utilisation des prompts
- Performance des requêtes
- Statistiques des projets
- Temps de réponse API

## 🔮 Roadmap v3.1+

### Fonctionnalités Prévues
- 🔌 Système de plugins
- 🌐 Synchronisation cloud
- 🤖 IA améliorée avec GPT
- 📊 Tableaux de bord analytics
- 🔄 Git intégré
- 📱 App mobile native
- 🎯 Déploiement automatique
- 🔐 Authentification multi-utilisateurs

### Améliorations Continues
- Performance et optimisation
- Nouvelles intégrations
- Interface utilisateur
- Sécurité renforcée

## 📄 Licence

SGC-AgentOne est distribué sous licence MIT. Libre d'utilisation, modification et distribution.

## 👨‍💻 Développement

### Structure du Code
```
agentone/
├── index.php              # Point d'entrée principal
├── assets/                # CSS, JS, images
├── views/                 # Vues PHP modulaires
├── api/                   # Endpoints API
├── core/                  # Logique métier
│   ├── config/           # Configuration
│   ├── db/               # Bases de données
│   └── logs/             # Journaux
└── README.md             # Documentation
```

### Contribution
Les contributions sont les bienvenues ! Consultez le guide de développement dans la documentation intégrée.

---

**SGC-AgentOne v3.0** - *Développé avec ❤️ pour la communauté des développeurs*