#!/bin/bash
# Script de déploiement automatique de l'appli

echo "--- Début du déploiement ---"

# 1. Récupération de la dernière version du code
git pull origin main

# 2. Installation des dépendances sans les outils de dev
composer install --no-dev --optimize-autoloader

# 3. Mise à jour de la BDD
bin/console doctrine:migrations:migrate --no-interaction

# 4. Nettoyage du cache
bin/prod cache:clear --env=prod
bin/prod cache:warmup --env=prod

# 5. Compilation des assets
bin/console asset-map:compile

echo "--- Déploiement terminée avec succès ---"