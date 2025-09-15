# 🚀 Guide d'Installation Complet SGC-AgentOne v2.0

## 🎯 Corrections Apportées

Cette version 2.0 résout complètement le problème **"Fichier index.html introuvable"** rencontré sur :
- ✅ XAMPP Local (`localhost/sgc-agentone`)
- ✅ Serveur Mutualisé (`mondomaine.com/sgc-agentone`)
- ✅ Tous environnements de sous-dossiers

## 📋 Prérequis

### Système Requis
- **PHP** : Version 7.4 ou supérieure
- **Serveur Web** : Apache avec mod_rewrite OU Nginx
- **Extensions PHP** : json, mbstring, fileinfo, session
- **Permissions** : Lecture/écriture sur dossiers logs/ et db/

### Extensions PHP Optionnelles (recommandées)
- curl (pour tests de connectivité)
- zip (pour export de projets)
- gd (pour manipulation d'images)
- sqlite3 (pour base de données locale)

## 🚀 Installation Locale (XAMPP/WAMP/MAMP)

### 1. Placement des Fichiers

**XAMPP (Windows/Mac/Linux):**
```bash
# Extraire dans :
C:\xampp\htdocs\sgc-agentone\
# ou
/opt/lampp/htdocs/sgc-agentone/
```

**WAMP (Windows):**
```bash
# Extraire dans :
C:\wamp64\www\sgc-agentone\
```

**MAMP (Mac):**
```bash
# Extraire dans :
/Applications/MAMP/htdocs/sgc-agentone/
```

### 2. Vérification de la Structure

Assurez-vous que la structure suivante est présente :
```
sgc-agentone/
├── index.php                    ✅ Point d'entrée
├── diagnostic.php               ✅ Outil de diagnostic
├── .htaccess                    ✅ Configuration Apache
├── core/
│   ├── utils/
│   │   └── PathHelper.php       ✅ Gestion des chemins
│   ├── agents/
│   ├── config/
│   ├── logs/                    ✅ Permissions 755
│   └── db/                      ✅ Permissions 755
├── api/
│   ├── auth.php
│   ├── chat.php
│   ├── files.php
│   └── server.php
└── extensions/
    └── webview/
        ├── index.html           ✅ Interface principale
        ├── chat.html
        ├── files.html
        └── ...
```

### 3. Configuration des Permissions

**Linux/Mac :**
```bash
# Permissions générales
chmod -R 755 sgc-agentone/
find sgc-agentone/ -type f -exec chmod 644 {} \;

# Permissions spéciales
chmod 755 sgc-agentone/core/logs/
chmod 755 sgc-agentone/core/db/
chmod 644 sgc-agentone/.htaccess
```

**Windows :**
- Clic droit sur le dossier `sgc-agentone`
- Propriétés → Sécurité → Modifier
- Donner "Contrôle total" à l'utilisateur du serveur web

### 4. Test de Fonctionnement

```bash
# 1. Accès principal
http://localhost/sgc-agentone/

# 2. Si erreur, diagnostic rapide
http://localhost/sgc-agentone/?debug=1

# 3. Diagnostic complet
http://localhost/sgc-agentone/diagnostic.php
```

## 🌐 Installation Serveur Mutualisé

### 1. Upload des Fichiers

**Via FTP/SFTP :**
```bash
# Uploader tous les fichiers dans :
public_html/sgc-agentone/
# ou
www/sgc-agentone/
# ou selon votre hébergeur
```

**Via cPanel File Manager :**
1. Connectez-vous à cPanel
2. Ouvrez "Gestionnaire de fichiers"
3. Naviguez vers `public_html`
4. Créez le dossier `sgc-agentone`
5. Uploadez tous les fichiers

### 2. Vérification des Permissions

**Via SSH (si disponible) :**
```bash
# Permissions générales
find public_html/sgc-agentone/ -type d -exec chmod 755 {} \;
find public_html/sgc-agentone/ -type f -exec chmod 644 {} \;

# Permissions spéciales
chmod 755 public_html/sgc-agentone/core/logs/
chmod 755 public_html/sgc-agentone/core/db/
```

**Via cPanel File Manager :**
1. Sélectionnez tous les dossiers
2. Clic droit → Permissions → 755
3. Sélectionnez tous les fichiers
4. Clic droit → Permissions → 644

### 3. Configuration .htaccess

Le fichier `.htaccess` fourni est automatiquement configuré pour les serveurs mutualisés. Vérifiez qu'il est bien présent à la racine.

### 4. Test de Fonctionnement

```bash
# 1. Accès principal
https://mondomaine.com/sgc-agentone/

# 2. Si erreur, diagnostic
https://mondomaine.com/sgc-agentone/?debug=1

# 3. Diagnostic complet
https://mondomaine.com/sgc-agentone/diagnostic.php
```

## 🔧 Diagnostic et Dépannage

### Outils de Diagnostic Intégrés

#### 1. Mode Debug Rapide
```bash
# Ajouter ?debug=1 à l'URL
http://localhost/sgc-agentone/?debug=1
```
**Affiche :**
- Chemins détectés par PathHelper
- Validation des fichiers critiques
- Informations système de base
- Actions de correction

#### 2. Diagnostic Complet
```bash
# Script de diagnostic exhaustif
http://localhost/sgc-agentone/diagnostic.php
```
**Analyse :**
- Configuration PHP complète
- Extensions disponibles
- Structure des fichiers
- Permissions et accès
- Tests de connectivité
- Recommandations personnalisées

### Problèmes Courants et Solutions

#### ❌ "Fichier index.html introuvable"

**Causes possibles :**
1. Structure de fichiers incomplète
2. Permissions incorrectes
3. PathHelper ne trouve pas la racine du projet
4. Configuration serveur web

**Solutions :**
```bash
# 1. Vérifier la structure
ls -la sgc-agentone/extensions/webview/index.html

# 2. Mode diagnostic
http://localhost/sgc-agentone/?debug=1

# 3. Diagnostic complet
http://localhost/sgc-agentone/diagnostic.php

# 4. Vérifier les permissions
chmod 644 sgc-agentone/extensions/webview/index.html
```

#### ❌ Erreur 500 (Internal Server Error)

**Solutions :**
```bash
# 1. Vérifier les logs d'erreur
tail -f /var/log/apache2/error.log

# 2. Tester la syntaxe PHP
php -l sgc-agentone/index.php

# 3. Vérifier mod_rewrite
apache2ctl -M | grep rewrite

# 4. Permissions .htaccess
chmod 644 sgc-agentone/.htaccess
```

#### ❌ API non accessible

**Solutions :**
```bash
# 1. Test direct API
http://localhost/sgc-agentone/api/auth.php

# 2. Vérifier .htaccess
# Ligne : RewriteRule ^api/ - [L]

# 3. Permissions API
chmod 644 sgc-agentone/api/*.php
```

#### ❌ Interface ne se charge pas

**Solutions :**
1. Vérifier la console navigateur (F12)
2. Tester avec un autre navigateur
3. Désactiver les extensions de navigateur
4. Vérifier que JavaScript est activé

### Tests de Validation

#### Test 1 : Accès Principal
```bash
curl -I http://localhost/sgc-agentone/
# Attendu : HTTP/1.1 200 OK
```

#### Test 2 : API Auth
```bash
curl -X POST http://localhost/sgc-agentone/api/auth.php
# Attendu : JSON avec token
```

#### Test 3 : Ressources Webview
```bash
curl -I http://localhost/sgc-agentone/extensions/webview/index.html
# Attendu : HTTP/1.1 200 OK
```

## ⚙️ Configuration Avancée

### Variables d'Environnement

Créez un fichier `.env` (optionnel) :
```env
SGC_DEBUG=false
SGC_PORT=5000
SGC_HOST=0.0.0.0
SGC_THEME=dark
```

### Configuration PHP

**Via php.ini ou .htaccess :**
```ini
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M
file_uploads = On
session.auto_start = Off
```

### Configuration Apache

**VirtualHost personnalisé :**
```apache
<VirtualHost *:80>
    ServerName sgc-agentone.local
    DocumentRoot /path/to/sgc-agentone
    
    <Directory /path/to/sgc-agentone>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sgc-agentone_error.log
    CustomLog ${APACHE_LOG_DIR}/sgc-agentone_access.log combined
</VirtualHost>
```

### Configuration Nginx

```nginx
server {
    listen 80;
    server_name sgc-agentone.local;
    root /path/to/sgc-agentone;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    location ~ ^/(core|prompts)/ {
        deny all;
    }
    
    location /api/ {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## 🔄 Mise à Jour

### Sauvegarde Avant Mise à Jour
```bash
# Sauvegarder les données importantes
cp -r sgc-agentone/core/db/ backup/
cp -r sgc-agentone/core/logs/ backup/
cp -r sgc-agentone/prompts/ backup/
cp sgc-agentone/core/config/settings.json backup/
```

### Procédure de Mise à Jour
1. Télécharger la nouvelle version
2. Remplacer tous les fichiers sauf les dossiers de données
3. Exécuter le diagnostic : `diagnostic.php`
4. Tester le fonctionnement
5. Restaurer les données sauvegardées si nécessaire

## 📞 Support et Aide

### En Cas de Problème

1. **Diagnostic automatique :**
   ```bash
   http://localhost/sgc-agentone/diagnostic.php
   ```

2. **Mode debug :**
   ```bash
   http://localhost/sgc-agentone/?debug=1
   ```

3. **Vérification manuelle :**
   - Structure des fichiers
   - Permissions
   - Logs d'erreur serveur
   - Configuration PHP

### Informations à Fournir pour le Support

- Version PHP : `php -v`
- Serveur web et version
- Système d'exploitation
- Résultat du diagnostic complet
- Messages d'erreur exacts
- Configuration d'hébergement

### Ressources Utiles

- **Documentation intégrée :** Interface → Guide
- **Diagnostic système :** `diagnostic.php`
- **Logs d'activité :** `core/logs/`
- **Configuration :** `core/config/settings.json`

## ✅ Validation de l'Installation

### Checklist Finale

- [ ] Tous les fichiers sont présents
- [ ] Permissions correctes (755/644)
- [ ] `.htaccess` configuré
- [ ] PHP 7.4+ avec extensions requises
- [ ] mod_rewrite activé (Apache)
- [ ] Diagnostic complet sans erreur
- [ ] Interface accessible
- [ ] API fonctionnelle
- [ ] Chat opérationnel

### Test de Fonctionnement Complet

1. **Accès principal :** ✅ Interface se charge
2. **Chat :** ✅ Envoi de message fonctionne
3. **Fichiers :** ✅ Navigation dans l'arborescence
4. **Éditeur :** ✅ Ouverture et édition de fichiers
5. **Serveur :** ✅ Contrôles serveur opérationnels
6. **Paramètres :** ✅ Sauvegarde de configuration

---

**🎉 Installation Terminée avec Succès !**

SGC-AgentOne v2.0 est maintenant opérationnel sur votre environnement. Toutes les corrections ont été apportées pour garantir un fonctionnement optimal sur serveurs locaux et mutualisés.