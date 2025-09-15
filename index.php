<?php
/**
 * Point d'entrée universel de SGC-AgentOne v2.0
 * Compatible : XAMPP local, serveur mutualisé, sous-dossiers
 * Gestion robuste des chemins et diagnostic intégré
 * 
 * Corrections complètes pour résoudre "Fichier index.html introuvable"
 */

// Configuration d'erreurs pour le diagnostic
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';
$showErrors = $debug || (isset($_GET['show_errors']) && $_GET['show_errors'] === '1');

if ($showErrors) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Mode diagnostic complet
if ($debug) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>🔍 Diagnostic SGC-AgentOne</title>";
    echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#1a1a1a;color:#fff;}";
    echo ".section{background:#2d2d2d;padding:15px;margin:15px 0;border-radius:8px;border-left:4px solid #4CAF50;}";
    echo ".error{border-left-color:#f44336;}.warning{border-left-color:#ff9800;}";
    echo "pre{background:#000;padding:10px;border-radius:4px;overflow-x:auto;font-size:12px;}";
    echo ".btn{display:inline-block;padding:8px 16px;background:#2196F3;color:white;text-decoration:none;border-radius:4px;margin:5px;}";
    echo "</style></head><body>";
    echo "<h1>🔍 Diagnostic SGC-AgentOne v2.0</h1>";
    echo "<p><strong>Mode Debug Activé</strong> | " . date('Y-m-d H:i:s') . "</p>";
}

try {
    // Chargement de PathHelper avec gestion d'erreur
    $pathHelperPath = 'core/utils/PathHelper.php';
    if (!file_exists($pathHelperPath)) {
        throw new Exception("PathHelper.php introuvable à: " . $pathHelperPath);
    }
    
    require_once $pathHelperPath;
    use core\utils\PathHelper;
    
    if ($debug) {
        echo "<div class='section'>";
        echo "<h2>📁 Informations de Diagnostic</h2>";
        $diagnosticInfo = PathHelper::getDiagnosticInfo();
        echo "<pre>" . print_r($diagnosticInfo, true) . "</pre>";
        echo "</div>";
    }
    
    // Validation des chemins critiques
    $validation = PathHelper::validatePaths();
    if ($validation !== true) {
        if ($debug) {
            echo "<div class='section error'>";
            echo "<h2>❌ Erreurs de Chemins Détectées</h2>";
            echo "<ul>";
            foreach ($validation as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        throw new Exception("Chemins critiques manquants. Utilisez ?debug=1 pour plus d'informations.");
    }
    
    // Construction du chemin vers index.html
    $webviewPath = PathHelper::getWebviewPath();
    $indexPath = $webviewPath . 'index.html';
    
    if ($debug) {
        echo "<div class='section'>";
        echo "<h2>🎯 Chemins Calculés</h2>";
        echo "<table style='width:100%;border-collapse:collapse;'>";
        echo "<tr style='background:#333;'><th style='padding:8px;text-align:left;'>Type</th><th style='padding:8px;text-align:left;'>Chemin</th><th style='padding:8px;text-align:left;'>Existe</th></tr>";
        
        $paths = [
            'Base Path' => PathHelper::getBasePath(),
            'Webview Path' => $webviewPath,
            'Index Path' => $indexPath,
            'Core Path' => PathHelper::getCorePath(),
            'API Path' => PathHelper::getApiPath()
        ];
        
        foreach ($paths as $name => $path) {
            $exists = file_exists($path);
            echo "<tr><td style='padding:8px;border-bottom:1px solid #444;'>" . $name . "</td>";
            echo "<td style='padding:8px;border-bottom:1px solid #444;'>" . htmlspecialchars($path) . "</td>";
            echo "<td style='padding:8px;border-bottom:1px solid #444;'>" . ($exists ? '✅' : '❌') . "</td></tr>";
        }
        echo "</table>";
        echo "</div>";
        
        if (is_dir($webviewPath)) {
            echo "<div class='section'>";
            echo "<h2>📂 Contenu du Dossier Webview</h2>";
            $files = scandir($webviewPath);
            echo "<ul>";
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $isFile = is_file($webviewPath . $file);
                    echo "<li>" . ($isFile ? '📄' : '📁') . " " . htmlspecialchars($file) . "</li>";
                }
            }
            echo "</ul>";
            echo "</div>";
        }
        
        echo "<div class='section'>";
        echo "<h2>🚀 Actions</h2>";
        echo "<a href='?' class='btn'>🏠 Accéder à l'Application</a>";
        echo "<a href='diagnostic.php' class='btn'>🔍 Diagnostic Complet</a>";
        echo "<a href='?debug=1&show_errors=1' class='btn'>⚠️ Afficher Erreurs PHP</a>";
        echo "</div>";
        
        echo "</body></html>";
        exit;
    }
    
    // Vérification finale de l'existence du fichier
    if (!file_exists($indexPath)) {
        throw new Exception("Fichier index.html introuvable: " . $indexPath . 
                          " (Webview path: " . $webviewPath . ")");
    }
    
    if (!is_readable($indexPath)) {
        throw new Exception("Fichier index.html non lisible: " . $indexPath . 
                          " (Vérifiez les permissions)");
    }
    
    // Lecture du contenu
    $content = file_get_contents($indexPath);
    if ($content === false) {
        throw new Exception("Impossible de lire le fichier index.html: " . $indexPath);
    }
    
    // Headers appropriés
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Injection de la base URL pour corriger les chemins relatifs
    $baseUrl = PathHelper::getBaseUrl();
    $webviewUrl = $baseUrl . '/extensions/webview/';
    
    // Injection de la balise base pour les ressources
    $content = str_replace(
        '<head>',
        '<head><base href="' . htmlspecialchars($webviewUrl) . '">',
        $content
    );
    
    // Injection d'informations de debug si nécessaire
    if ($showErrors) {
        $debugInfo = "<!-- SGC-AgentOne Debug Info\n";
        $debugInfo .= "Base Path: " . PathHelper::getBasePath() . "\n";
        $debugInfo .= "Webview Path: " . $webviewPath . "\n";
        $debugInfo .= "Base URL: " . $baseUrl . "\n";
        $debugInfo .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $debugInfo .= "-->";
        
        $content = str_replace('</head>', $debugInfo . '</head>', $content);
    }
    
    // Servir le contenu
    echo $content;
    
} catch (Exception $e) {
    // Gestion d'erreur robuste avec page d'erreur complète
    http_response_code(500);
    
    $errorMessage = htmlspecialchars($e->getMessage());
    $currentTime = date('Y-m-d H:i:s');
    
    echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>🚨 Erreur SGC-AgentOne</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        .error { background: #2d1b1b; border: 2px solid #d32f2f; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .diagnostic { background: #1b2d1b; border: 2px solid #2f7d32; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .warning { background: #2d2d1b; border: 2px solid #f57c00; padding: 20px; border-radius: 8px; margin: 20px 0; }
        pre { background: #000; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #1976D2; }
        .btn-danger { background: #d32f2f; }
        .btn-success { background: #2f7d32; }
        h1 { color: #f44336; }
        h2 { color: #4CAF50; }
        h3 { color: #ff9800; }
        ul { padding-left: 20px; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚨 Erreur SGC-AgentOne v2.0</h1>
        <p><strong>Heure:</strong> ' . $currentTime . '</p>
        
        <div class="error">
            <h2>❌ Erreur Détectée</h2>
            <p><strong>Message:</strong> ' . $errorMessage . '</p>
        </div>
        
        <div class="diagnostic">
            <h2>🔧 Solutions Recommandées</h2>
            <ol>
                <li><strong>Vérifiez la structure des fichiers:</strong>
                    <ul>
                        <li>Le dossier <code>extensions/webview/</code> doit exister</li>
                        <li>Le fichier <code>extensions/webview/index.html</code> doit être présent</li>
                        <li>Le fichier <code>core/utils/PathHelper.php</code> doit être accessible</li>
                    </ul>
                </li>
                <li><strong>Vérifiez les permissions:</strong>
                    <ul>
                        <li>Dossiers: 755 (lecture/exécution)</li>
                        <li>Fichiers: 644 (lecture/écriture)</li>
                    </ul>
                </li>
                <li><strong>Utilisez les outils de diagnostic:</strong>
                    <ul>
                        <li><a href="?debug=1" class="btn">🔍 Mode Debug</a></li>
                        <li><a href="diagnostic.php" class="btn btn-success">📋 Diagnostic Complet</a></li>
                    </ul>
                </li>
            </ol>
        </div>
        
        <div class="warning">
            <h2>📋 Informations Système</h2>
            <pre>PHP Version: ' . PHP_VERSION . '
Serveur: ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu') . '
Document Root: ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . '
Script: ' . $_SERVER['SCRIPT_FILENAME'] . '
Request URI: ' . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . '
Working Directory: ' . getcwd() . '</pre>
        </div>
        
        <div class="diagnostic">
            <h2>🚀 Actions Rapides</h2>
            <a href="?" class="btn">🔄 Réessayer</a>
            <a href="?debug=1" class="btn">🔍 Mode Debug</a>
            <a href="diagnostic.php" class="btn btn-success">📋 Diagnostic Complet</a>
            <a href="INSTALL.md" class="btn">📖 Guide Installation</a>
        </div>
        
        <div class="warning">
            <h3>💡 Aide Rapide</h3>
            <p>Si le problème persiste:</p>
            <ul>
                <li>Consultez le fichier <strong>INSTALL.md</strong> pour l\'installation</li>
                <li>Exécutez <strong>diagnostic.php</strong> pour une analyse complète</li>
                <li>Vérifiez que tous les fichiers ont été correctement copiés</li>
                <li>Contactez le support avec les informations de diagnostic</li>
            </ul>
        </div>
    </div>
</body>
</html>';
}