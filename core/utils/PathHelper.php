<?php
namespace core\utils;

class PathHelper {
    public static function getBasePath() {
        $current = dirname($_SERVER['SCRIPT_FILENAME']);
        while (!is_file($current . '/index.php') && $current !== '/') {
            $current = dirname($current);
        }
        return $current;
    }

    public static function getCorePath() { return self::getBasePath() . '/core'; }
    public static function getApiPath() { return self::getBasePath() . '/api'; }
    public static function getWebviewPath() { return self::getBasePath() . '/extensions/webview'; }
    public static function getLogsPath() { return self::getCorePath() . '/logs'; }
    public static function getDBPath() { return self::getCorePath() . '/db/app.db'; }
    public static function getPromptsPath() { return self::getBasePath() . '/prompts'; }
}
