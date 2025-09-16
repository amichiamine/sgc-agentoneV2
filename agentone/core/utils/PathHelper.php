<?php
/**
 * PathHelper - Gestionnaire de chemins universels
 * Compatible XAMPP, serveur mutualisé, Windows, Linux, macOS
 */

class PathHelper {
    private static $basePath = null;
    
    /**
     * Obtenir le chemin de base du projet
     */
    public static function getBasePath() {
        if (self::$basePath === null) {
            // Utiliser __DIR__ pour une compatibilité universelle
            self::$basePath = dirname(dirname(__DIR__));
        }
        return self::$basePath;
    }
    
    /**
     * Obtenir le chemin du dossier core
     */
    public static function getCorePath() {
        return self::getBasePath() . '/core';
    }
    
    /**
     * Obtenir le chemin du dossier logs
     */
    public static function getLogsPath() {
        $logsPath = self::getCorePath() . '/logs';
        if (!is_dir($logsPath)) {
            mkdir($logsPath, 0755, true);
        }
        return $logsPath;
    }
    
    /**
     * Obtenir le chemin du dossier db
     */
    public static function getDbPath() {
        $dbPath = self::getCorePath() . '/db';
        if (!is_dir($dbPath)) {
            mkdir($dbPath, 0755, true);
        }
        return $dbPath;
    }
    
    /**
     * Obtenir le chemin du dossier config
     */
    public static function getConfigPath() {
        $configPath = self::getCorePath() . '/config';
        if (!is_dir($configPath)) {
            mkdir($configPath, 0755, true);
        }
        return $configPath;
    }
    
    /**
     * Sécuriser un chemin contre les path traversal
     */
    public static function securePath($path) {
        // Supprimer les tentatives de path traversal
        $path = str_replace(['../', '..\\', '../', '..\\'], '', $path);
        $path = ltrim($path, '/\\');
        return $path;
    }
    
    /**
     * Vérifier si un chemin est dans la zone autorisée
     */
    public static function isPathAllowed($path) {
        $realPath = realpath($path);
        $basePath = realpath(self::getBasePath());
        
        if (!$realPath || !$basePath) {
            return false;
        }
        
        return strpos($realPath, $basePath) === 0;
    }
    
    /**
     * Créer un chemin complet sécurisé
     */
    public static function buildPath($relativePath) {
        $securePath = self::securePath($relativePath);
        return self::getBasePath() . '/' . $securePath;
    }
}
?>