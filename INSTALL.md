# 🚀 Guide d'Installation SGC-AgentOne

## Installation Locale (XAMPP/WAMP/MAMP)

### 1. Téléchargement et Extraction
```bash
# Télécharger le projet
# Extraire dans le dossier web de votre serveur local
```

### 2. Placement des Fichiers

**XAMPP (Windows/Mac/Linux):**
```
C:\xampp\htdocs\sgc-agentone\
```

**WAMP (Windows):**
```
C:\wamp64\www\sgc-agentone\
```

**MAMP (Mac):**
```
/Applications/MAMP/htdocs/sgc-agentone/
```

### 3. Configuration des Permissions
```bash
# Linux/Mac
chmod -R 755 sgc-agentone/
chmod -R 644 sgc-agentone/*.php
chmod -R 755 sgc-agentone/core/logs/
chmod -R 755 sgc-agentone/core/db/

# Windows : Clic droit → Propriétés → Sécurité → Contrôle total
```

### 4. Test de Fonctionnement
```
http://localhost/sgc-agentone/
```

**Si erreur "Fichier index.html introuvable" :**
```
http://localhost/sgc-agentone/?debug=1
```

## Installation Serveur Mutualisé

### 1. Upload via FTP/cPanel
```
Uploader tous les fichiers dans :
public_html/sgc-agentone/
```

### 2. Vérification des Permissions
```bash
# Via SSH si disponible
find sgc-agentone/ -type d -exec chmod 755 {} \;
find sgc-agentone/ -type f -exec chmod 644 {} \;
chmod 755 sgc-agentone/core/logs/
chmod 755 sgc-agentone/core/db/
```

### 3. Configuration .htaccess
Le fichier `.htaccess` est automatiquement configuré pour les environnements mutualisés.

### 4. Test
```
https://mondomaine.com/sgc-agentone/
```

## Diagnostic et Dépannage

### Mode Diagnostic
Ajoutez `?debug=1` à l'URL pour activer le diagnostic :
```
http://localhost/sgc-agentone/?debug=1
https://mondomaine.com/sgc-agentone/?debug=1
```

### Problèmes Courants

#### 1. "Fichier index.html introuvable"
**Solutions :**
- Vérifiez que le dossier `extensions/webview/` existe
- Vérifiez les permissions (755 pour dossiers, 644 pour fichiers)
- Utilisez le mode diagnostic

#### 2. Erreur 500 (Internal Server Error)
**Solutions :**
- Vérifiez les logs d'erreur du serveur
- Vérifiez la version PHP (minimum 7.4)
- Vérifiez les permissions des fichiers

#### 3. API non accessible
**Solutions :**
- Vérifiez que le dossier `api/` est accessible
- Vérifiez la configuration `.htaccess`
- Testez directement : `http://localhost/sgc-agentone/api/auth.php`

#### 4. Interface ne se charge pas
**Solutions :**
- Vérifiez la console du navigateur (F12)
- Vérifiez que JavaScript est activé
- Testez avec un autre navigateur

### Structure des Fichiers Requise
```
sgc-agentone/
├── index.php                 ✅ Point d'entrée
├── .htaccess                 ✅ Configuration Apache
├── core/
│   ├── utils/
│   │   └── PathHelper.php    ✅ Gestion des chemins
│   ├── agents/
│   ├── config/
│   ├── logs/                 ✅ Permissions 755
│   └── db/                   ✅ Permissions 755
├── api/
│   ├── auth.php
│   ├── chat.php
│   ├── files.php
│   └── server.php
└── extensions/
    └── webview/
        ├── index.html        ✅ Interface principale
        ├── chat.html
        ├── files.html
        └── ...
```

## Configuration Avancée

### Variables d'Environnement
Créez un fichier `.env` (optionnel) :
```env
SGC_DEBUG=false
SGC_PORT=5000
SGC_HOST=0.0.0.0
```

### Configuration PHP
Ajoutez dans `php.ini` ou `.htaccess` :
```ini
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
```

### Configuration Serveur Web

#### Apache
Le fichier `.htaccess` fourni gère automatiquement la configuration.

#### Nginx
```nginx
location /sgc-agentone {
    try_files $uri $uri/ /sgc-agentone/index.php?$query_string;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
    
    location ~ ^/sgc-agentone/(core|prompts)/ {
        deny all;
    }
}
```

## Support et Aide

### Logs de Diagnostic
Les logs sont stockés dans :
- `core/logs/actions.log` - Actions utilisateur
- `core/logs/chat.log` - Conversations
- Logs serveur web (selon configuration)

### Contact Support
- Consultez la documentation dans l'interface : Guide → FAQ
- Utilisez le mode diagnostic pour identifier les problèmes
- Vérifiez les permissions et la structure des fichiers

### Mise à Jour
1. Sauvegardez vos données (`core/db/`, `core/logs/`, `prompts/`)
2. Remplacez tous les fichiers sauf les dossiers de données
3. Testez le fonctionnement
4. Restaurez vos données si nécessaire