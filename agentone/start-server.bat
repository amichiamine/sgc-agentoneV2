@echo off
echo ========================================
echo   SGC-AgentOne v3.0 - Serveur Windows
echo ========================================
echo.

cd /d "%~dp0"

echo Verification de PHP...
php --version >nul 2>&1
if errorlevel 1 (
    echo ERREUR: PHP n'est pas installe ou pas dans le PATH
    echo Installez XAMPP ou ajoutez PHP au PATH systeme
    pause
    exit /b 1
)

echo PHP detecte. Demarrage du serveur...
echo.
echo Interface disponible sur: http://localhost:5000
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.

php -S 0.0.0.0:5000 -t . index.php

echo.
echo Serveur arrete.
pause