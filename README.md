@@ .. @@
+# 🚀 SGC-AgentOne v2.0
+
+**Assistant universel d'écriture, de gestion et de contrôle de projets locaux**
+
+## ✨ Nouveautés Version 2.0
+
+- 🔧 **Audit complet et corrections** pour serveurs mutualisés et XAMPP
+- 🛠️ **PathHelper.php renforcé** avec détection automatique des environnements
+- 🔍 **Mode diagnostic intégré** (`?debug=1`)
+- 📋 **Script de diagnostic complet** (`diagnostic.php`)
+- ⚙️ **Configuration .htaccess optimisée** pour tous environnements
+- 📖 **Documentation d'installation complète** (`INSTALL.md`)
+
+## 🚀 Installation Rapide
+
+### Local (XAMPP/WAMP/MAMP)
+```bash
+# 1. Extraire dans le dossier web
+C:\xampp\htdocs\sgc-agentone\
+
+# 2. Accéder via navigateur
+http://localhost/sgc-agentone/
+
+# 3. Si erreur, utiliser le diagnostic
+http://localhost/sgc-agentone/?debug=1
+http://localhost/sgc-agentone/diagnostic.php
+```
+
+### Serveur Mutualisé
+```bash
+# 1. Upload via FTP dans public_html
+public_html/sgc-agentone/
+
+# 2. Vérifier les permissions (755/644)
+
+# 3. Accéder via navigateur
+https://mondomaine.com/sgc-agentone/
+```
+
+## 🔧 Diagnostic et Dépannage
+
+### Erreur "Fichier index.html introuvable"
+```bash
+# 1. Mode diagnostic
+http://localhost/sgc-agentone/?debug=1
+
+# 2. Script de diagnostic complet
+http://localhost/sgc-agentone/diagnostic.php
+
+# 3. Vérifier la structure des fichiers
+# 4. Vérifier les permissions
+# 5. Consulter INSTALL.md
+```
+
+### Outils de Diagnostic
+- **Mode Debug** : `?debug=1` - Diagnostic rapide intégré
+- **Script Diagnostic** : `diagnostic.php` - Analyse complète du système
+- **Logs** : `core/logs/` - Journaux d'activité
+- **Validation** : PathHelper::validatePaths() - Vérification automatique
+
 ## 🎯 Fonctionnalités
 
 - **Chat Agent** : Interface conversationnelle pour contrôler le système
@@ -18,6 +68,12 @@
 - **Sécurité** : Path traversal protection, whitelist, logs complets
 - **Universalité** : Fonctionne partout (XAMPP, mutualisé, Replit, Android PWA)
 
+## 📋 Prérequis
+
+- **PHP** : Version 7.4 ou supérieure
+- **Extensions** : json, mbstring, fileinfo, session
+- **Serveur Web** : Apache (avec mod_rewrite) ou Nginx
+- **Permissions** : Lecture/écriture sur dossiers logs/ et db/
+
 ## 🚀 Démarrage Rapide
 
@@ -30,6 +86,15 @@
 ./start-server.sh
 ```
 
+## 📖 Documentation Complète
+
+- **[INSTALL.md](INSTALL.md)** - Guide d'installation détaillé
+- **[AUDIT_REPORT.md](AUDIT_REPORT.md)** - Rapport d'audit et corrections
+- **Interface → Guide** - Documentation utilisateur intégrée
+- **diagnostic.php** - Outil de diagnostic système
+
+## 🆘 Support
+
 ## 🎨 Interface
 
 - **Mode Portrait** : Une vue à la fois, navigation par menu
@@ -37,6 +102,13 @@
 - **Responsive** : S'adapte automatiquement à tous les écrans
 - **PWA Ready** : Convertible en app Android native
 
+### En cas de problème
+1. Utilisez le mode diagnostic : `?debug=1`
+2. Exécutez le script de diagnostic : `diagnostic.php`
+3. Consultez `INSTALL.md` pour l'installation
+4. Vérifiez les permissions et la structure des fichiers
+5. Consultez les logs dans `core/logs/`
+
 ## 🔧 Architecture
 
 - **PHP Pur** : Aucune dépendance externe
@@ -46,6 +118,12 @@
 - **Modulaire** : Actions dans `core/agents/actions/`
 - **Sécurisé** : Validation, logs, protection path traversal
 
+## 🔄 Changelog v2.0
+
+- ✅ Correction complète des problèmes de chemins
+- ✅ Support serveurs mutualisés et sous-dossiers
+- ✅ Outils de diagnostic intégrés
+- ✅ Documentation d'installation complète
+- ✅ Configuration .htaccess optimisée
+- ✅ Gestion d'erreurs robuste
+
 ## 📱 PWA Android