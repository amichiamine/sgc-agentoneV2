<?php
/**
 * SGC-AgentOne v3.0 - Point d'entrée principal
 * Architecture modulaire avec vues séparées
 * Compatible XAMPP, serveur mutualisé, toutes plateformes
 */

// Configuration de base
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Chemins relatifs pour compatibilité universelle
define('BASE_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/views');
define('API_PATH', BASE_PATH . '/api');
define('ASSETS_PATH', BASE_PATH . '/assets');

// Fonction pour charger une vue
function loadView($view = 'chat') {
    $viewFile = VIEWS_PATH . '/' . $view . '.php';
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        include VIEWS_PATH . '/chat.php'; // Vue par défaut
    }
}

// Routage simple
$view = $_GET['view'] ?? 'chat';
$validViews = ['chat', 'files', 'editor', 'terminal', 'server', 'database', 'browser', 'projects', 'prompts', 'settings', 'help'];

if (!in_array($view, $validViews)) {
    $view = 'chat';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGC-AgentOne v3.0</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div id="app">
        <!-- Header avec navigation -->
        <header id="header">
            <div class="header-left">
                <h1>SGC-AgentOne</h1>
                <small>v3.0 - Architecture Modulaire</small>
            </div>
            <nav id="nav-menu">
                <a href="?view=chat" class="nav-btn <?= $view === 'chat' ? 'active' : '' ?>">💬 Chat</a>
                <a href="?view=files" class="nav-btn <?= $view === 'files' ? 'active' : '' ?>">📁 Fichiers</a>
                <a href="?view=editor" class="nav-btn <?= $view === 'editor' ? 'active' : '' ?>">📝 Éditeur</a>
                <a href="?view=terminal" class="nav-btn <?= $view === 'terminal' ? 'active' : '' ?>">⚡ Terminal</a>
                <a href="?view=server" class="nav-btn <?= $view === 'server' ? 'active' : '' ?>">🖥️ Serveur</a>
                <a href="?view=database" class="nav-btn <?= $view === 'database' ? 'active' : '' ?>">🗄️ Base</a>
                <a href="?view=browser" class="nav-btn <?= $view === 'browser' ? 'active' : '' ?>">🌐 Navigateur</a>
                <a href="?view=projects" class="nav-btn <?= $view === 'projects' ? 'active' : '' ?>">📂 Projets</a>
                <a href="?view=prompts" class="nav-btn <?= $view === 'prompts' ? 'active' : '' ?>">📝 Prompts</a>
                <a href="?view=settings" class="nav-btn <?= $view === 'settings' ? 'active' : '' ?>">⚙️ Config</a>
                <a href="?view=help" class="nav-btn <?= $view === 'help' ? 'active' : '' ?>">❓ Aide</a>
            </nav>
        </header>

        <!-- Zone principale -->
        <main id="main-content">
            <?php loadView($view); ?>
        </main>

        <!-- Footer -->
        <footer id="footer">
            <div class="footer-left">
                <span id="status-info">Serveur : Connecté • Vue : <?= ucfirst($view) ?> • UTF-8</span>
            </div>
            <div class="footer-right">
                <span id="timestamp"><?= date('Y-m-d H:i:s') ?></span>
            </div>
        </footer>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>