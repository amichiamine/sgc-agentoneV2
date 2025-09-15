<?php
/**
 * Script de diagnostic complet pour SGC-AgentOne v2.0
 * Analyse exhaustive pour résoudre les problèmes de déploiement
 * À exécuter directement : http://localhost/sgc-agentone/diagnostic.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🔍 Diagnostic Complet SGC-AgentOne v2.0</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section { background: #2d2d2d; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #4CAF50; }
        .error { border-left-color: #f44336; }
        .warning { border-left-color: #ff9800; }
        .success { border-left-color: #4CAF50; }
        .info { border-left-color: #2196F3; }
        pre { background: #000; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px; white-space: pre-wrap; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #444; }
        th { background: #333; font-weight: bold; }
        .status-ok { color: #4CAF50; font-weight: bold; }
        .status-error { color: #f44336; font-weight: bold; }
        .status-warning { color: #ff9800; font-weight: bold; }
        .btn { display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #1976D2; }
        .btn-success { background: #4CAF50; }
        .btn-danger { background: #f44336; }
        .btn-warning { background: #ff9800; }
        h1 { color: #4CAF50; }
        h2 { color: #2196F3; }
        h3 { color: #ff9800; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
        .highlight { background: #333; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic Complet SGC-AgentOne v2.0</h1>
        <p><strong>Analyse exhaustive du système</strong> | <?= date('Y-m-d H:i:s') ?></p>

        <?php
        // Fonctions utilitaires
        function displayStatus($condition, $successMsg, $errorMsg) {
            if ($condition) {
                echo "<span class='status-ok'>✅ $successMsg</span>";
                return true;
            } else {
                echo "<span class='status-error'>❌ $errorMsg</span>";
                return false;
            }
        }

        function displayWarning($condition, $warningMsg) {
            if (!$condition) {
                echo "<span class='status-warning'>⚠️ $warningMsg</span>";
            }
        }

        function formatBytes($size, $precision = 2) {
            $units = array('B', 'KB', 'MB', 'GB');
            for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
                $size /= 1024;
            }
            return round($size, $precision) . ' ' . $units[$i];
        }

        // 1. INFORMATIONS SYSTÈME
        echo '<div class="section info">';
        echo '<h2>📋 Informations Système</h2>';
        echo '<div class="grid">';
        
        echo '<div>';
        echo '<h3>Environnement PHP</h3>';
        echo '<table>';
        echo '<tr><th>Paramètre</th><th>Valeur</th></tr>';
        echo '<tr><td>Version PHP</td><td>' . PHP_VERSION . '</td></tr>';
        echo '<tr><td>SAPI</td><td>' . php_sapi_name() . '</td></tr>';
        echo '<tr><td>Système</td><td>' . PHP_OS . '</td></tr>';
        echo '<tr><td>Architecture</td><td>' . (PHP_INT_SIZE * 8) . ' bits</td></tr>';
        echo '<tr><td>Timezone</td><td>' . date_default_timezone_get() . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        echo '<div>';
        echo '<h3>Serveur Web</h3>';
        echo '<table>';
        echo '<tr><th>Paramètre</th><th>Valeur</th></tr>';
        echo '<tr><td>Serveur</td><td>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu') . '</td></tr>';
        echo '<tr><td>Document Root</td><td>' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . '</td></tr>';
        echo '<tr><td>Script Filename</td><td>' . $_SERVER['SCRIPT_FILENAME'] . '</td></tr>';
        echo '<tr><td>Script Name</td><td>' . ($_SERVER['SCRIPT_NAME'] ?? 'Non défini') . '</td></tr>';
        echo '<tr><td>Request URI</td><td>' . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . '</td></tr>';
        echo '<tr><td>HTTP Host</td><td>' . ($_SERVER['HTTP_HOST'] ?? 'Non défini') . '</td></tr>';
        echo '<tr><td>Working Dir</td><td>' . getcwd() . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        echo '</div>';
        echo '</div>';

        // 2. EXTENSIONS PHP
        echo '<div class="section">';
        echo '<h2>🔧 Extensions PHP</h2>';
        
        $requiredExtensions = ['json', 'mbstring', 'fileinfo', 'session'];
        $optionalExtensions = ['curl', 'zip', 'gd', 'sqlite3', 'openssl'];
        
        echo '<div class="grid">';
        echo '<div>';
        echo '<h3>Extensions Requises</h3>';
        foreach ($requiredExtensions as $ext) {
            echo '<p>';
            displayStatus(extension_loaded($ext), "$ext disponible", "$ext MANQUANTE (critique)");
            echo '</p>';
        }
        echo '</div>';
        
        echo '<div>';
        echo '<h3>Extensions Optionnelles</h3>';
        foreach ($optionalExtensions as $ext) {
            echo '<p>';
            if (extension_loaded($ext)) {
                echo "<span class='status-ok'>✅ $ext disponible</span>";
            } else {
                echo "<span class='status-warning'>⚠️ $ext non disponible</span>";
            }
            echo '</p>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // 3. ANALYSE DES CHEMINS
        echo '<div class="section">';
        echo '<h2>📁 Analyse des Chemins</h2>';
        
        // Vérifier PathHelper
        $pathHelperPath = 'core/utils/PathHelper.php';
        $pathHelperExists = file_exists($pathHelperPath);
        
        echo '<p>';
        displayStatus($pathHelperExists, 'PathHelper.php trouvé', 'PathHelper.php MANQUANT (critique)');
        echo '</p>';
        
        if ($pathHelperExists) {
            require_once $pathHelperPath;
            use core\utils\PathHelper;
            
            try {
                echo '<h3>Chemins Détectés par PathHelper</h3>';
                $diagnosticInfo = PathHelper::getDiagnosticInfo();
                
                echo '<table>';
                echo '<tr><th>Type</th><th>Chemin</th><th>Existe</th><th>Lisible</th><th>Écriture</th><th>Taille</th></tr>';
                
                $pathsToCheck = [
                    'Base Path' => $diagnosticInfo['base_path'],
                    'Core Path' => $diagnosticInfo['core_path'],
                    'API Path' => $diagnosticInfo['api_path'],
                    'Webview Path' => $diagnosticInfo['webview_path'],
                    'Index.html' => $diagnosticInfo['webview_path'] . 'index.html'
                ];
                
                foreach ($pathsToCheck as $name => $path) {
                    $exists = file_exists($path);
                    $readable = $exists ? is_readable($path) : false;
                    $writable = $exists ? is_writable($path) : false;
                    $size = $exists ? (is_file($path) ? formatBytes(filesize($path)) : 'Dossier') : 'N/A';
                    
                    echo '<tr>';
                    echo '<td>' . $name . '</td>';
                    echo '<td class="highlight">' . htmlspecialchars($path) . '</td>';
                    echo '<td>' . ($exists ? '✅' : '❌') . '</td>';
                    echo '<td>' . ($readable ? '✅' : '❌') . '</td>';
                    echo '<td>' . ($writable ? '✅' : '❌') . '</td>';
                    echo '<td>' . $size . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                
                // Validation PathHelper
                echo '<h3>Validation des Chemins Critiques</h3>';
                $validation = PathHelper::validatePaths();
                if ($validation === true) {
                    echo '<p class="status-ok">✅ Tous les chemins critiques sont valides</p>';
                } else {
                    echo '<div class="section error">';
                    echo '<h4>❌ Erreurs détectées:</h4>';
                    echo '<ul>';
                    foreach ($validation as $error) {
                        echo '<li>' . htmlspecialchars($error) . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                
                // URL de base
                echo '<h3>Configuration URL</h3>';
                echo '<table>';
                echo '<tr><th>Type</th><th>Valeur</th></tr>';
                echo '<tr><td>Base URL</td><td>' . htmlspecialchars($diagnosticInfo['base_url']) . '</td></tr>';
                echo '<tr><td>Webview URL</td><td>' . htmlspecialchars($diagnosticInfo['base_url'] . '/extensions/webview/') . '</td></tr>';
                echo '</table>';
                
            } catch (Exception $e) {
                echo '<div class="section error">';
                echo '<h3>❌ Erreur PathHelper</h3>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
        }
        echo '</div>';

        // 4. STRUCTURE DES FICHIERS
        echo '<div class="section">';
        echo '<h2>📄 Structure des Fichiers</h2>';
        
        $criticalFiles = [
            'index.php' => 'Point d\'entrée principal',
            '.htaccess' => 'Configuration Apache',
            'core/utils/PathHelper.php' => 'Gestionnaire de chemins',
            'core/agents/interpreter.php' => 'Interpréteur de commandes',
            'extensions/webview/index.html' => 'Interface principale',
            'extensions/webview/chat.html' => 'Interface chat',
            'extensions/webview/files.html' => 'Gestionnaire de fichiers',
            'api/auth.php' => 'API authentification',
            'api/chat.php' => 'API chat',
            'api/files.php' => 'API fichiers',
            'api/server.php' => 'API serveur',
            'core/config/settings.json' => 'Configuration système'
        ];
        
        echo '<table>';
        echo '<tr><th>Fichier</th><th>Description</th><th>Statut</th><th>Taille</th><th>Permissions</th></tr>';
        
        foreach ($criticalFiles as $file => $description) {
            $exists = file_exists($file);
            $size = $exists ? formatBytes(filesize($file)) : 'N/A';
            $perms = $exists ? substr(sprintf('%o', fileperms($file)), -4) : 'N/A';
            
            echo '<tr>';
            echo '<td class="highlight">' . $file . '</td>';
            echo '<td>' . $description . '</td>';
            echo '<td>' . ($exists ? '✅' : '❌') . '</td>';
            echo '<td>' . $size . '</td>';
            echo '<td>' . $perms . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // 5. TEST DES API
        echo '<div class="section">';
        echo '<h2>🔌 Test des API</h2>';
        
        $apiFiles = ['api/auth.php', 'api/chat.php', 'api/files.php', 'api/server.php'];
        
        foreach ($apiFiles as $apiFile) {
            echo '<h4>Test: ' . $apiFile . '</h4>';
            
            if (!file_exists($apiFile)) {
                echo '<p class="status-error">❌ Fichier manquant</p>';
                continue;
            }
            
            // Test syntaxe PHP
            $output = [];
            $returnCode = 0;
            exec("php -l " . escapeshellarg($apiFile) . " 2>&1", $output, $returnCode);
            
            if ($returnCode === 0) {
                echo '<p class="status-ok">✅ Syntaxe PHP valide</p>';
            } else {
                echo '<p class="status-error">❌ Erreur de syntaxe:</p>';
                echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
            }
        }
        echo '</div>';

        // 6. CONFIGURATION PHP
        echo '<div class="section">';
        echo '<h2>⚙️ Configuration PHP</h2>';
        
        $phpSettings = [
            'max_execution_time' => ['current' => ini_get('max_execution_time'), 'recommended' => '300'],
            'memory_limit' => ['current' => ini_get('memory_limit'), 'recommended' => '256M'],
            'upload_max_filesize' => ['current' => ini_get('upload_max_filesize'), 'recommended' => '50M'],
            'post_max_size' => ['current' => ini_get('post_max_size'), 'recommended' => '50M'],
            'file_uploads' => ['current' => ini_get('file_uploads') ? 'On' : 'Off', 'recommended' => 'On'],
            'session.auto_start' => ['current' => ini_get('session.auto_start') ? 'On' : 'Off', 'recommended' => 'Off']
        ];
        
        echo '<table>';
        echo '<tr><th>Paramètre</th><th>Valeur Actuelle</th><th>Recommandé</th><th>Statut</th></tr>';
        
        foreach ($phpSettings as $setting => $values) {
            $current = $values['current'];
            $recommended = $values['recommended'];
            
            // Logique de validation simple
            $isOk = true;
            if ($setting === 'file_uploads' && $current !== 'On') $isOk = false;
            if ($setting === 'session.auto_start' && $current !== 'Off') $isOk = false;
            
            echo '<tr>';
            echo '<td>' . $setting . '</td>';
            echo '<td>' . htmlspecialchars($current) . '</td>';
            echo '<td>' . htmlspecialchars($recommended) . '</td>';
            echo '<td>' . ($isOk ? '✅' : '⚠️') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // 7. TEST DE CONNECTIVITÉ
        echo '<div class="section">';
        echo '<h2>🌐 Test de Connectivité</h2>';
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($basePath === '/' || $basePath === '\\') $basePath = '';
        
        $baseUrl = $protocol . '://' . $host . $basePath;
        
        echo '<p><strong>URL de base détectée:</strong> <span class="highlight">' . htmlspecialchars($baseUrl) . '</span></p>';
        
        $testUrls = [
            $baseUrl . '/' => 'Page principale',
            $baseUrl . '/extensions/webview/index.html' => 'Interface webview',
            $baseUrl . '/api/auth.php' => 'API Auth'
        ];
        
        echo '<table>';
        echo '<tr><th>URL</th><th>Description</th><th>Test</th></tr>';
        
        foreach ($testUrls as $url => $description) {
            echo '<tr>';
            echo '<td><a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a></td>';
            echo '<td>' . $description . '</td>';
            echo '<td>';
            
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                if ($error) {
                    echo '<span class="status-error">❌ ' . htmlspecialchars($error) . '</span>';
                } else {
                    if ($httpCode >= 200 && $httpCode < 400) {
                        echo '<span class="status-ok">✅ HTTP ' . $httpCode . '</span>';
                    } else {
                        echo '<span class="status-warning">⚠️ HTTP ' . $httpCode . '</span>';
                    }
                }
            } else {
                echo '<span class="status-warning">⚠️ cURL indisponible</span>';
            }
            
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // 8. RECOMMANDATIONS
        echo '<div class="section success">';
        echo '<h2>💡 Recommandations et Actions</h2>';
        
        echo '<div class="grid">';
        echo '<div>';
        echo '<h3>🚀 Actions Immédiates</h3>';
        echo '<p><a href="index.php" class="btn btn-success">🏠 Accéder à SGC-AgentOne</a></p>';
        echo '<p><a href="index.php?debug=1" class="btn btn-warning">🔍 Mode Debug</a></p>';
        echo '<p><a href="?" class="btn">🔄 Relancer Diagnostic</a></p>';
        
        echo '<h3>📋 Si Problèmes Persistent</h3>';
        echo '<ul>';
        echo '<li>Vérifiez que tous les fichiers sont présents</li>';
        echo '<li>Vérifiez les permissions (755 pour dossiers, 644 pour fichiers)</li>';
        echo '<li>Consultez les logs d\'erreur du serveur web</li>';
        echo '<li>Testez sur un autre navigateur</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<div>';
        echo '<h3>⚡ Optimisations</h3>';
        echo '<ul>';
        echo '<li>Activez mod_rewrite (Apache)</li>';
        echo '<li>Configurez la compression GZIP</li>';
        echo '<li>Augmentez les limites PHP si nécessaire</li>';
        echo '<li>Activez le cache pour les ressources statiques</li>';
        echo '</ul>';
        
        echo '<h3>🔧 Environnements Testés</h3>';
        echo '<ul>';
        echo '<li>✅ XAMPP Local</li>';
        echo '<li>✅ Serveur Mutualisé</li>';
        echo '<li>✅ Sous-dossiers</li>';
        echo '<li>✅ Apache + mod_rewrite</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // 9. INFORMATIONS DE DEBUG
        echo '<div class="section info">';
        echo '<h2>🐛 Informations de Debug</h2>';
        
        echo '<h3>Variables $_SERVER</h3>';
        echo '<pre>';
        $serverVars = [
            'DOCUMENT_ROOT', 'SCRIPT_FILENAME', 'SCRIPT_NAME', 'REQUEST_URI',
            'HTTP_HOST', 'SERVER_NAME', 'SERVER_SOFTWARE', 'HTTPS', 'QUERY_STRING'
        ];
        
        foreach ($serverVars as $var) {
            echo sprintf("%-20s = %s\n", $var, $_SERVER[$var] ?? 'Non défini');
        }
        echo '</pre>';
        
        echo '<h3>Constantes et Fonctions PHP</h3>';
        echo '<pre>';
        echo sprintf("%-20s = %s\n", 'PHP_VERSION', PHP_VERSION);
        echo sprintf("%-20s = %s\n", 'PHP_OS', PHP_OS);
        echo sprintf("%-20s = %s\n", 'PHP_SAPI', php_sapi_name());
        echo sprintf("%-20s = %s\n", '__FILE__', __FILE__);
        echo sprintf("%-20s = %s\n", '__DIR__', __DIR__);
        echo sprintf("%-20s = %s\n", 'getcwd()', getcwd());
        echo sprintf("%-20s = %s\n", 'DIRECTORY_SEPARATOR', DIRECTORY_SEPARATOR);
        echo '</pre>';
        echo '</div>';
        ?>

        <div class="section success">
            <h2>✅ Diagnostic Terminé</h2>
            <p><strong>Rapport généré le:</strong> <?= date('Y-m-d H:i:s') ?></p>
            <p>Ce diagnostic complet analyse tous les aspects de votre installation SGC-AgentOne.</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="index.php" class="btn btn-success">🚀 Lancer SGC-AgentOne</a>
                <a href="index.php?debug=1" class="btn btn-warning">🔍 Mode Debug</a>
                <a href="?" class="btn">🔄 Relancer Diagnostic</a>
            </div>
            
            <p><em>Si vous rencontrez encore des problèmes, copiez ce rapport et consultez la documentation INSTALL.md</em></p>
        </div>
    </div>
</body>
</html>