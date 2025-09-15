<?php
/**
 * Script de diagnostic complet pour SGC-AgentOne
 * À exécuter directement : http://localhost/sgc-agentone/diagnostic.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🔍 Diagnostic SGC-AgentOne</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; }
        .section { background: #2d2d2d; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #4CAF50; }
        .error { border-left-color: #f44336; }
        .warning { border-left-color: #ff9800; }
        .success { border-left-color: #4CAF50; }
        .info { border-left-color: #2196F3; }
        pre { background: #000; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #444; }
        th { background: #333; }
        .status-ok { color: #4CAF50; font-weight: bold; }
        .status-error { color: #f44336; font-weight: bold; }
        .status-warning { color: #ff9800; font-weight: bold; }
        .btn { display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #1976D2; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic Complet SGC-AgentOne</h1>
    <p>Version du diagnostic : 2.0 | Date : <?= date('Y-m-d H:i:s') ?></p>

    <?php
    // Fonction utilitaire pour afficher le statut
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

    // 1. INFORMATIONS SYSTÈME
    echo '<div class="section info">';
    echo '<h2>📋 Informations Système</h2>';
    echo '<table>';
    echo '<tr><th>Paramètre</th><th>Valeur</th></tr>';
    echo '<tr><td>Version PHP</td><td>' . PHP_VERSION . '</td></tr>';
    echo '<tr><td>Serveur Web</td><td>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu') . '</td></tr>';
    echo '<tr><td>Système d\'exploitation</td><td>' . PHP_OS . '</td></tr>';
    echo '<tr><td>Document Root</td><td>' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . '</td></tr>';
    echo '<tr><td>Script actuel</td><td>' . $_SERVER['SCRIPT_FILENAME'] . '</td></tr>';
    echo '<tr><td>URI de requête</td><td>' . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . '</td></tr>';
    echo '<tr><td>Nom du script</td><td>' . ($_SERVER['SCRIPT_NAME'] ?? 'Non défini') . '</td></tr>';
    echo '<tr><td>Répertoire de travail</td><td>' . getcwd() . '</td></tr>';
    echo '<tr><td>Utilisateur PHP</td><td>' . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'Inconnu') . '</td></tr>';
    echo '</table>';
    echo '</div>';

    // 2. VÉRIFICATION DES EXTENSIONS PHP
    echo '<div class="section">';
    echo '<h2>🔧 Extensions PHP Requises</h2>';
    $requiredExtensions = ['json', 'mbstring', 'fileinfo', 'session'];
    $optionalExtensions = ['curl', 'zip', 'gd', 'sqlite3'];
    
    echo '<h3>Extensions Requises:</h3>';
    foreach ($requiredExtensions as $ext) {
        echo '<p>';
        displayStatus(extension_loaded($ext), "$ext chargée", "$ext MANQUANTE (critique)");
        echo '</p>';
    }
    
    echo '<h3>Extensions Optionnelles:</h3>';
    foreach ($optionalExtensions as $ext) {
        echo '<p>';
        if (extension_loaded($ext)) {
            echo "<span class='status-ok'>✅ $ext disponible</span>";
        } else {
            echo "<span class='status-warning'>⚠️ $ext non disponible (fonctionnalités limitées)</span>";
        }
        echo '</p>';
    }
    echo '</div>';

    // 3. VÉRIFICATION DES CHEMINS
    echo '<div class="section">';
    echo '<h2>📁 Vérification des Chemins</h2>';
    
    // Charger PathHelper
    $pathHelperExists = file_exists('core/utils/PathHelper.php');
    echo '<p>';
    displayStatus($pathHelperExists, 'PathHelper.php trouvé', 'PathHelper.php MANQUANT');
    echo '</p>';
    
    if ($pathHelperExists) {
        require_once 'core/utils/PathHelper.php';
        use core\utils\PathHelper;
        
        try {
            $basePath = PathHelper::getBasePath();
            $webviewPath = PathHelper::getWebviewPath();
            $corePath = PathHelper::getCorePath();
            $apiPath = PathHelper::getApiPath();
            
            echo '<h3>Chemins Détectés:</h3>';
            echo '<table>';
            echo '<tr><th>Type</th><th>Chemin</th><th>Existe</th><th>Permissions</th></tr>';
            
            $paths = [
                'Base' => $basePath,
                'Core' => $corePath,
                'API' => $apiPath,
                'Webview' => $webviewPath,
                'Index.html' => $webviewPath . 'index.html',
                'Logs' => PathHelper::getLogsPath(),
                'DB' => dirname(PathHelper::getDBPath())
            ];
            
            foreach ($paths as $name => $path) {
                $exists = file_exists($path);
                $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
                $readable = $exists ? (is_readable($path) ? '✅' : '❌') : 'N/A';
                $writable = $exists ? (is_writable($path) ? '✅' : '❌') : 'N/A';
                
                echo '<tr>';
                echo '<td>' . $name . '</td>';
                echo '<td>' . $path . '</td>';
                echo '<td>' . ($exists ? '✅' : '❌') . '</td>';
                echo '<td>' . $perms . ' (R:' . $readable . ' W:' . $writable . ')</td>';
                echo '</tr>';
            }
            echo '</table>';
            
            // Validation complète
            $validation = PathHelper::validatePaths();
            if ($validation === true) {
                echo '<p class="status-ok">✅ Tous les chemins critiques sont valides</p>';
            } else {
                echo '<div class="section error">';
                echo '<h3>❌ Erreurs de chemins:</h3>';
                foreach ($validation as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="section error">';
            echo '<h3>❌ Erreur PathHelper:</h3>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
    }
    echo '</div>';

    // 4. VÉRIFICATION DES FICHIERS CRITIQUES
    echo '<div class="section">';
    echo '<h2>📄 Fichiers Critiques</h2>';
    
    $criticalFiles = [
        'index.php' => 'Point d\'entrée principal',
        '.htaccess' => 'Configuration Apache',
        'core/utils/PathHelper.php' => 'Gestionnaire de chemins',
        'extensions/webview/index.html' => 'Interface principale',
        'api/auth.php' => 'API d\'authentification',
        'api/chat.php' => 'API de chat',
        'api/files.php' => 'API de fichiers',
        'core/config/settings.json' => 'Configuration'
    ];
    
    echo '<table>';
    echo '<tr><th>Fichier</th><th>Description</th><th>Statut</th><th>Taille</th></tr>';
    
    foreach ($criticalFiles as $file => $description) {
        $exists = file_exists($file);
        $size = $exists ? filesize($file) : 0;
        $sizeFormatted = $exists ? number_format($size) . ' octets' : 'N/A';
        
        echo '<tr>';
        echo '<td>' . $file . '</td>';
        echo '<td>' . $description . '</td>';
        echo '<td>' . ($exists ? '✅' : '❌') . '</td>';
        echo '<td>' . $sizeFormatted . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // 5. TEST DES API
    echo '<div class="section">';
    echo '<h2>🔌 Test des API</h2>';
    
    $apiEndpoints = [
        'api/auth.php' => 'POST',
        'api/files.php' => 'POST', 
        'api/chat.php' => 'POST',
        'api/server.php' => 'POST'
    ];
    
    foreach ($apiEndpoints as $endpoint => $method) {
        echo '<h4>Test: ' . $endpoint . '</h4>';
        
        if (!file_exists($endpoint)) {
            echo '<p class="status-error">❌ Fichier manquant</p>';
            continue;
        }
        
        // Test basique de syntaxe PHP
        $output = [];
        $returnCode = 0;
        exec("php -l $endpoint 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo '<p class="status-ok">✅ Syntaxe PHP valide</p>';
        } else {
            echo '<p class="status-error">❌ Erreur de syntaxe:</p>';
            echo '<pre>' . implode("\n", $output) . '</pre>';
        }
    }
    echo '</div>';

    // 6. CONFIGURATION PHP
    echo '<div class="section">';
    echo '<h2>⚙️ Configuration PHP</h2>';
    
    $phpConfig = [
        'max_execution_time' => ['recommandé' => '300', 'critique' => false],
        'memory_limit' => ['recommandé' => '256M', 'critique' => false],
        'upload_max_filesize' => ['recommandé' => '50M', 'critique' => false],
        'post_max_size' => ['recommandé' => '50M', 'critique' => false],
        'file_uploads' => ['recommandé' => '1', 'critique' => true],
        'session.auto_start' => ['recommandé' => '0', 'critique' => false]
    ];
    
    echo '<table>';
    echo '<tr><th>Paramètre</th><th>Valeur Actuelle</th><th>Recommandé</th><th>Statut</th></tr>';
    
    foreach ($phpConfig as $param => $config) {
        $currentValue = ini_get($param);
        $recommended = $config['recommandé'];
        $critical = $config['critique'];
        
        // Logique de comparaison simple
        $isOk = true;
        if ($param === 'file_uploads' && $currentValue != '1') $isOk = false;
        if ($param === 'session.auto_start' && $currentValue != '0') $isOk = false;
        
        echo '<tr>';
        echo '<td>' . $param . '</td>';
        echo '<td>' . ($currentValue ?: 'Non défini') . '</td>';
        echo '<td>' . $recommended . '</td>';
        echo '<td>';
        if ($isOk) {
            echo '<span class="status-ok">✅</span>';
        } else {
            echo $critical ? '<span class="status-error">❌</span>' : '<span class="status-warning">⚠️</span>';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // 7. TEST DE CONNECTIVITÉ
    echo '<div class="section">';
    echo '<h2>🌐 Test de Connectivité</h2>';
    
    // Test d'accès aux ressources
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
               '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    
    echo '<p><strong>URL de base détectée:</strong> ' . $baseUrl . '</p>';
    
    $testUrls = [
        $baseUrl . '/extensions/webview/index.html' => 'Interface principale',
        $baseUrl . '/extensions/webview/chat.html' => 'Interface chat',
        $baseUrl . '/api/auth.php' => 'API Auth (devrait retourner erreur méthode)'
    ];
    
    foreach ($testUrls as $url => $description) {
        echo '<h4>' . $description . '</h4>';
        echo '<p>URL: <a href="' . $url . '" target="_blank">' . $url . '</a></p>';
        
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                echo '<p class="status-error">❌ Erreur cURL: ' . $error . '</p>';
            } else {
                if ($httpCode >= 200 && $httpCode < 400) {
                    echo '<p class="status-ok">✅ Accessible (HTTP ' . $httpCode . ')</p>';
                } else {
                    echo '<p class="status-warning">⚠️ HTTP ' . $httpCode . '</p>';
                }
            }
        } else {
            echo '<p class="status-warning">⚠️ cURL non disponible - test manuel requis</p>';
        }
    }
    echo '</div>';

    // 8. RECOMMANDATIONS
    echo '<div class="section info">';
    echo '<h2>💡 Recommandations</h2>';
    echo '<h3>Actions Immédiates:</h3>';
    echo '<ul>';
    echo '<li>Testez l\'accès principal: <a href="index.php" class="btn">Accéder à SGC-AgentOne</a></li>';
    echo '<li>Si erreur, testez avec debug: <a href="index.php?debug=1" class="btn">Mode Debug</a></li>';
    echo '<li>Vérifiez les permissions des dossiers core/logs/ et core/db/</li>';
    echo '<li>Consultez les logs d\'erreur de votre serveur web</li>';
    echo '</ul>';
    
    echo '<h3>Optimisations:</h3>';
    echo '<ul>';
    echo '<li>Activez la compression GZIP dans votre serveur web</li>';
    echo '<li>Configurez le cache pour les ressources statiques</li>';
    echo '<li>Vérifiez que mod_rewrite est activé (Apache)</li>';
    echo '<li>Augmentez les limites PHP si nécessaire</li>';
    echo '</ul>';
    echo '</div>';

    // 9. INFORMATIONS DE DÉBOGAGE
    echo '<div class="section">';
    echo '<h2>🐛 Informations de Débogage</h2>';
    echo '<h3>Variables $_SERVER importantes:</h3>';
    echo '<pre>';
    $serverVars = [
        'DOCUMENT_ROOT', 'SCRIPT_FILENAME', 'SCRIPT_NAME', 'REQUEST_URI',
        'HTTP_HOST', 'SERVER_NAME', 'SERVER_SOFTWARE', 'HTTPS'
    ];
    
    foreach ($serverVars as $var) {
        echo $var . ' = ' . ($_SERVER[$var] ?? 'Non défini') . "\n";
    }
    echo '</pre>';
    
    echo '<h3>Constantes PHP importantes:</h3>';
    echo '<pre>';
    echo 'PHP_VERSION = ' . PHP_VERSION . "\n";
    echo 'PHP_OS = ' . PHP_OS . "\n";
    echo 'DIRECTORY_SEPARATOR = ' . DIRECTORY_SEPARATOR . "\n";
    echo '__FILE__ = ' . __FILE__ . "\n";
    echo '__DIR__ = ' . __DIR__ . "\n";
    echo 'getcwd() = ' . getcwd() . "\n";
    echo '</pre>';
    echo '</div>';
    ?>

    <div class="section success">
        <h2>✅ Diagnostic Terminé</h2>
        <p>Ce diagnostic a été généré le <?= date('Y-m-d H:i:s') ?>.</p>
        <p>Si vous rencontrez encore des problèmes, copiez ces informations et consultez la documentation.</p>
        
        <h3>Actions Suivantes:</h3>
        <a href="index.php" class="btn">🚀 Lancer SGC-AgentOne</a>
        <a href="index.php?debug=1" class="btn">🔍 Mode Debug</a>
        <a href="diagnostic.php" class="btn">🔄 Relancer Diagnostic</a>
    </div>

</body>
</html>