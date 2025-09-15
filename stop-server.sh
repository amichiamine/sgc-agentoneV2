#!/bin/bash
pkill -f "php -S 0.0.0.0:5000" > /dev/null 2>&1
echo "Serveur arrêté."
