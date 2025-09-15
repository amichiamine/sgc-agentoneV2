<?php
namespace core\utils;

class PathHelper {
    /**
     * Détecte automatiquement le chemin de base du projet
     * Compatible : XAMPP local, serveur mutualisé, sous-dossiers
     */
    public static function getBasePath() {
        // Méthode 1 : Chercher depuis le script actuel
        $current = dirname($_SERVER['SCRIPT_FILENAME']);
        
        // Remonter jusqu'à trouver index.php (fichier marqueur du projet)
        $maxDepth = 10; // Sécurité contre boucle infinie
        $depth = 0;
        
        while ($depth < $maxDepth) {
            if (is_file($current . '/index.php') && is_dir($current . '/core')) {
                return $current;
            }
            
            $parent = dirname($current);
            if ($parent === $current || $parent === '/' || $parent === '\\') {
                break; // Racine atteinte
            }
            $current = dirname($current);
            $depth++;
        }
        
        // Méthode 2 : Utiliser __DIR__ comme fallback
        $fallback = dirname(dirname(__DIR__)); // Remonte de core/utils vers racine
        if (is_file($fallback . '/index.php') && is_dir($fallback . '/core')) {
            return $fallback;
        }
        
        // Méthode 3 : Document root + détection sous-dossier
        if (isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['REQUEST_URI'])) {
            $docRoot = $_SERVER['DOCUMENT_ROOT'];
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $pathParts = explode('/', trim($requestUri, '/'));
            
            // Tester différents niveaux de sous-dossiers
            for ($i = 0; $i < count($pathParts); $i++) {
                $testPath = $docRoot . '/' . implode('/', array_slice($pathParts, 0, $i + 1));
                if (is_file($testPath . '/index.php') && is_dir($testPath . '/core')) {
                    return $testPath;
                }
            }
            
            // Test direct document root
            if (is_file($docRoot . '/index.php') && is_dir($docRoot . '/core')) {
                return $docRoot;
            }
        }
        
        // Dernière tentative : chemin actuel
        $currentDir = getcwd();
        if (is_file($currentDir . '/index.php') && is_dir($currentDir . '/core')) {
            return $currentDir;
        }
        
        // Si tout échoue, utiliser le fallback
        return $fallback;
    }

    public static function getCorePath() { 
        return self::getBasePath() . '/core'; 
    }
    
    public static function getApiPath() { 
        return self::getBasePath() . '/api'; 
    }
    
    public static function getWebviewPath() { 
        return self::getBasePath() . '/extensions/webview/'; 
    }
    
    public static function getLogsPath() { 
        return self::getCorePath() . '/logs'; 
    }
    
    public static function getDBPath() { 
        return self::getCorePath() . '/db/app.db'; 
    }
    
    public static function getPromptsPath() { 
        return self::getBasePath() . '/prompts'; 
    }
    
    /**
     * Obtient l'URL de base du projet pour les liens web
     */
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Détecter le sous-dossier depuis l'URI
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Extraire le chemin du dossier
        $basePath = dirname($scriptName);
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        return $protocol . '://' . $host . $basePath;
    }
    
    /**
     * Vérifie si tous les chemins critiques existent
     */
    public static function validatePaths() {
        $basePath = self::getBasePath();
        $criticalPaths = [
            'index.php' => $basePath . '/index.php',
            'core' => $basePath . '/core',
            'api' => $basePath . '/api',
            'extensions/webview' => $basePath . '/extensions/webview',
            'extensions/webview/index.html' => $basePath . '/extensions/webview/index.html'
        ];
        
        $errors = [];
        foreach ($criticalPaths as $name => $path) {
            if (!file_exists($path)) {
                $errors[] = "Manquant: $name ($path)";
            }
        }
        
        return empty($errors) ? true : $errors;
    }
}
