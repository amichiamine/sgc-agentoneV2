<?php
namespace core\agents;

class Interpreter {
    public function interpret($prompt) {
        $prompt = trim(strtolower($prompt));
        
        // Extraction du format "action cible : contenu"
        if (strpos($prompt, ':') === false) {
            return ['error' => 'Format invalide. Utilisez : "action chemin : contenu"'];
        }
        
        list($actionAndTarget, $content) = explode(':', $prompt, 2);
        $actionAndTarget = trim($actionAndTarget);
        $content = trim($content);
        
        if (empty($actionAndTarget)) {
            return ['error' => 'Action manquante'];
        }
        
        // Séparer action et cible (premier espace)
        $parts = explode(' ', $actionAndTarget, 2);
        $action = $parts[0];
        $target = isset($parts[1]) ? trim($parts[1]) : '';
        
        if (empty($action)) {
            return ['error' => 'Action manquante'];
        }
        
        // Vérifier si l'action existe
        $actionClass = '\\core\\agents\\actions\\' . ucfirst($action);
        if (!class_exists($actionClass)) {
            return ['error' => "Action inconnue : {$action}"];
        }
        
        try {
            $instance = new $actionClass();
            
            if ($action === 'createFile' || $action === 'updateFile') {
                $result = $instance->execute($target, $content);
            } elseif ($action === 'createDir' || $action === 'deleteDir' || $action === 'listDir') {
                $result = $instance->execute($target);
            } elseif ($action === 'renameFile' || $action === 'renameDir' || $action === 'moveFile' || $action === 'moveDir' || $action === 'copyFile' || $action === 'copyDir') {
                $result = $instance->execute($target, $content); // Dans ce cas, $content est la destination
            } else {
                $result = $instance->execute($target);
            }
            
            return $result;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
