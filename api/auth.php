<?php
/**
 * Endpoint d'authentification unique.
 * Génère un token session et le stocke en mémoire (pas de base).
 * Aucun utilisateur, aucun mot de passe — juste une clé aléatoire pour éviter les appels externes.
 * Utilisé uniquement par l'interface pour valider les requêtes POST.
 */
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$token = bin2hex(random_bytes(32));
$_SESSION['auth_token'] = $token;

echo json_encode(['token' => $token]);
