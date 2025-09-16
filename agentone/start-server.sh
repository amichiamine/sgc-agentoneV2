#!/bin/bash

echo "========================================"
echo "  SGC-AgentOne v3.0 - Serveur Unix/Linux"
echo "========================================"
echo

cd "$(dirname "$0")"

echo "Vérification de PHP..."
if ! command -v php &> /dev/null; then
    echo "ERREUR: PHP n'est pas installé ou pas dans le PATH"
    echo "Installez PHP avec votre gestionnaire de paquets :"
    echo "  Ubuntu/Debian: sudo apt install php"
    echo "  CentOS/RHEL: sudo yum install php"
    echo "  macOS: brew install php"
    exit 1
fi

echo "PHP détecté. Démarrage du serveur..."
echo
echo "Interface disponible sur: http://localhost:5000"
echo "Appuyez sur Ctrl+C pour arrêter le serveur"
echo

php -S 0.0.0.0:5000 -t . index.php

echo
echo "Serveur arrêté."