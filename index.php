<?php
/**
 * Point d'entrée universel de SGC-AgentOne.
 * Ne fait qu'un seul choix : servir index.html depuis le conteneur webview.
 * Aucune détection d'environnement, aucun __DIR__, aucun $_SERVER['DOCUMENT_ROOT'] utilisé.
 * Tout chemin passe par PathHelper.php.
 * 
 * Philosophie : Universalité absolue — marche sur Replit, XAMPP, Apache, Android PWA, mutualisé.
 */
require_once 'core/utils/PathHelper.php';
use core\utils\PathHelper;

$path = PathHelper::getWebviewPath() . 'index.html';

if (file_exists($path)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($path);
    exit;
}

http_response_code(404);
echo "Fichier index.html introuvable.";
