<?php
/**
 * Point d'entrée universel de SGC-AgentOne.
 * Compatible : XAMPP local, serveur mutualisé, sous-dossiers
 * Gestion robuste des chemins et diagnostic intégré
 * 
 * Version 2.0 - Audit et corrections complètes
 */

// Mode diagnostic (à activer temporairement si problèmes)
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    echo "<h2>🔍 Diagnostic SGC-AgentOne</h2>";
    echo "<pre>";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Script: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
    echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "\n";
    echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . "\n";
    echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Non défini') . "\n";
    echo "Working Directory: " . getcwd() . "\n";
    echo "</pre>";
}

require_once 'core/utils/PathHelper.php';
use core\utils\PathHelper;

try {
    // Validation des chemins critiques
    $validation = PathHelper::validatePaths();
    if ($validation !== true) {
        if ($debug) {
            echo "<h3>❌ Erreurs de chemins détectées:</h3><pre>";
            foreach ($validation as $error) {
                echo $error . "\n";
            }
            echo "</pre>";
        }
        throw new Exception("Chemins critiques manquants");
    }
    
    // Construire le chemin vers index.html
    $webviewPath = PathHelper::getWebviewPath();
    $indexPath = $webviewPath . 'index.html';
    
    if ($debug) {
        echo "<h3>📁 Chemins détectés:</h3><pre>";
        echo "Base Path: " . PathHelper::getBasePath() . "\n";
        echo "Webview Path: " . $webviewPath . "\n";
        echo "Index Path: " . $indexPath . "\n";
        echo "Base URL: " . PathHelper::getBaseUrl() . "\n";
        echo "Index exists: " . (file_exists($indexPath) ? '✅ OUI' : '❌ NON') . "\n";
        echo "</pre>";
        
        if (!file_exists($indexPath)) {
            echo "<h3>🔍 Contenu du dossier webview:</h3><pre>";
            if (is_dir($webviewPath)) {
                $files = scandir($webviewPath);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        echo $file . "\n";
                    }
                }
            } else {
                echo "Dossier webview introuvable!\n";
            }
            echo "</pre>";
        }
        
        echo "<hr><p><a href='?'>Accéder à l'application (sans debug)</a></p>";
        exit;
    }
    
    // Vérifier l'existence du fichier
    if (!file_exists($indexPath)) {
        throw new Exception("Fichier index.html introuvable: " . $indexPath);
    }
    
    // Servir le fichier avec les bons headers
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Lire et servir le contenu
    $content = file_get_contents($indexPath);
    if ($content === false) {
        throw new Exception("Impossible de lire le fichier index.html");
    }
    
    // Injection de la base URL pour les chemins relatifs
    $baseUrl = PathHelper::getBaseUrl();
    $content = str_replace('<head>', '<head><base href="' . $baseUrl . '/extensions/webview/">', $content);
    
    echo $content;
    
} catch (Exception $e) {
    // Gestion d'erreur robuste
    http_response_code(500);
    
    echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Erreur SGC-AgentOne</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #1a1a1a; color: #fff; }
        .error { background: #2d1b1b; border: 1px solid #d32f2f; padding: 20px; border-radius: 8px; }
        .diagnostic { background: #1b2d1b; border: 1px solid #2f7d32; padding: 15px; border-radius: 8px; margin-top: 20px; }
        pre { background: #000; padding: 10px; border-radius: 4px; overflow-x: auto; }
        a { color: #64b5f6; }
    </style>
</head>
<body>
    <h1>🚨 Erreur SGC-AgentOne</h1>
    <div class="error">
        <h3>Erreur:</h3>
        <p>' . htmlspecialchars($e->getMessage()) . '</p>
    </div>
    
    <div class="diagnostic">
        <h3>🔧 Solutions:</h3>
        <ol>
            <li>Vérifiez que tous les fichiers sont bien copiés</li>
            <li>Vérifiez les permissions (755 pour dossiers, 644 pour fichiers)</li>
            <li>Testez avec le mode diagnostic: <a href="?debug=1">?debug=1</a></li>
            <li>Consultez le fichier INSTALL.md pour l\'installation</li>
        </ol>
    </div>
    
    <div class="diagnostic">
        <h3>📋 Informations système:</h3>
        <pre>PHP: ' . PHP_VERSION . '
Serveur: ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu') . '
Document Root: ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . '
Script: ' . $_SERVER['SCRIPT_FILENAME'] . '</pre>
    </div>
</body>
</html>';
}


if (file_exists($path)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($path);
    exit;
}

http_response_code(404);
echo "Fichier index.html introuvable.";
