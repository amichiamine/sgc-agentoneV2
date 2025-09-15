<?php
namespace core\utils;

class PathHelper {
    /**
     * Détecte automatiquement le chemin de base du projet
     * Compatible : XAMPP local, serveur mutualisé, sous-dossiers
     * 
     * Stratégie multi-méthodes avec fallbacks robustes
     */
    public static function getBasePath() {
        // Méthode 1 : Recherche ascendante depuis le script actuel
        $current = dirname($_SERVER['SCRIPT_FILENAME']);
        $maxDepth = 10; // Sécurité contre boucle infinie
        $depth = 0;
        
        while ($depth < $maxDepth) {
            // Chercher les fichiers marqueurs du projet
            if (self::isProjectRoot($current)) {
                return $current;
            }
            
            $parent = dirname($current);
            if ($parent === $current || $parent === '/' || $parent === '\\') {
                break; // Racine système atteinte
            }
            $current = $parent;
            $depth++;
        }
        
        // Méthode 2 : Utiliser __DIR__ comme référence (depuis core/utils)
        $fallback = dirname(dirname(__DIR__)); // Remonte vers racine projet
        if (self::isProjectRoot($fallback)) {
            return $fallback;
        }
        
        // Méthode 3 : Analyse de l'URL pour serveurs mutualisés
        if (isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['REQUEST_URI'])) {
            $docRoot = $_SERVER['DOCUMENT_ROOT'];
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $pathParts = explode('/', trim($requestUri, '/'));
            
            // Tester différents niveaux de sous-dossiers
            for ($i = 0; $i < count($pathParts); $i++) {
                $testPath = $docRoot . '/' . implode('/', array_slice($pathParts, 0, $i + 1));
                if (self::isProjectRoot($testPath)) {
                    return $testPath;
                }
            }
            
            // Test direct document root
            if (self::isProjectRoot($docRoot)) {
                return $docRoot;
            }
        }
        
        // Méthode 4 : Répertoire de travail actuel
        $currentDir = getcwd();
        if (self::isProjectRoot($currentDir)) {
            return $currentDir;
        }
        
        // Méthode 5 : Analyse du SCRIPT_NAME pour serveurs mutualisés
        if (isset($_SERVER['SCRIPT_NAME'])) {
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            if (isset($_SERVER['DOCUMENT_ROOT'])) {
                $testPath = $_SERVER['DOCUMENT_ROOT'] . $scriptDir;
                if (self::isProjectRoot($testPath)) {
                    return $testPath;
                }
            }
        }
        
        // Si tout échoue, utiliser le fallback avec avertissement
        error_log("SGC-AgentOne: Impossible de détecter automatiquement le chemin de base. Utilisation du fallback: " . $fallback);
        return $fallback;
    }
    
    /**
     * Vérifie si un dossier est la racine du projet SGC-AgentOne
     */
    private static function isProjectRoot($path) {
        return is_file($path . '/index.php') && 
               is_dir($path . '/core') && 
               is_dir($path . '/extensions/webview') &&
               is_file($path . '/extensions/webview/index.html');
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
     * Compatible serveurs mutualisés et sous-dossiers
     */
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Détecter le sous-dossier depuis l'URI et SCRIPT_NAME
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Extraire le chemin du dossier depuis SCRIPT_NAME
        $basePath = dirname($scriptName);
        if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
            $basePath = '';
        }
        
        return $protocol . '://' . $host . $basePath;
    }
    
    /**
     * Vérifie si tous les chemins critiques existent
     * Retourne true si OK, ou array d'erreurs
     */
    public static function validatePaths() {
        $basePath = self::getBasePath();
        $criticalPaths = [
            'index.php' => $basePath . '/index.php',
            'core' => $basePath . '/core',
            'core/utils' => $basePath . '/core/utils',
            'core/utils/PathHelper.php' => $basePath . '/core/utils/PathHelper.php',
            'api' => $basePath . '/api',
            'extensions' => $basePath . '/extensions',
            'extensions/webview' => $basePath . '/extensions/webview',
            'extensions/webview/index.html' => $basePath . '/extensions/webview/index.html',
            'core/logs' => $basePath . '/core/logs',
            'core/db' => $basePath . '/core/db'
        ];
        
        $errors = [];
        foreach ($criticalPaths as $name => $path) {
            if (!file_exists($path)) {
                $errors[] = "Manquant: $name → $path";
            }
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Informations de diagnostic pour le débogage
     */
    public static function getDiagnosticInfo() {
        return [
            'base_path' => self::getBasePath(),
            'webview_path' => self::getWebviewPath(),
            'core_path' => self::getCorePath(),
            'api_path' => self::getApiPath(),
            'base_url' => self::getBaseUrl(),
            'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Non défini',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Non défini',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Non défini',
            'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'Non défini',
            'working_directory' => getcwd(),
            'validation' => self::validatePaths()
        ];
    }
}