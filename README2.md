# 🚀 Projet Symfony + Vue 3 + Tailwind CSS

Ce projet est une application web moderne combinant la puissance de **Symfony 7** pour le backend et la réactivité de **Vue 3** pour le frontend, le tout stylisé avec **Tailwind CSS**.

---

## 📋 Prérequis

Avant de commencer, assure-toi d'avoir installé les outils suivants :

- **PHP** ≥ 8.2
## Modifier le fichier C:\xampp\php\ini
```bash
Enlever le ";" des lignes suivantes :

extension=pdo_mysql
extension=intl
extension=mbstring
extension=zip
```
- **Composer**
- **Node.js** ≥ 18 & **npm**
- **Symfony CLI** (fortement recommandé)
- **Google Chrome** (pour les tests automatisés)

Vérification rapide :
```bash
php -v
composer -V
node -v
npm -v
symfony -v
```

---

## 🛠️ Installation & Configuration

### 1. Cloner le projet et installer les dépendances
```bash
# Dépendances PHP
composer install

# Dépendances JavaScript
npm install

# Installer Tailwind
php bin/console tailwind:build    
```

### 2. Configuration de l'environnement
Créez ou modifiez le fichier .env.local à la racine pour configurer votre base de données
```bash
DATABASE_URL="mysql://root:@127.0.0.1:3306/votre_bdd?serverVersion=8.0.32&charset=utf8mb4"
```

### 3. Base de données & données de test (Développement)
```bash
# Créer la base de données
php bin/console doctrine:database:create

# Appliquer les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Créer les rôles de base (si non présents en fixtures)
# INSERT INTO `role` (name_role) VALUES ('ROLE_ADMIN'), ('ROLE_USER');

# Installer Fixtures et Faker (si non présents)
composer require --dev orm-fixtures
composer require fakerphp/faker

# Charger les fixtures (Faker data)
php bin/console doctrine:fixtures:load --no-interaction

# Efface la bdd si besoin
php bin/console doctrine:database:drop --force
```

## 🧪 Tests (Unitaires & E2E avec Panther)

### Installer le bundle DAMA pour effacer la bdd test à chaque test
```bash
composer require --dev dama/doctrine-test-bundle
```

### Configuration de la base de test
```bash
# Création et mise à jour du schéma de test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --force --env=test
php bin/console doctrine:fixtures:load --env=test --no-interaction

# Efface la bdd test si besoin
php bin/console doctrine:database:drop --force --env=test
```

### Configuration de Symfony Panther (Spécifique Windows)

### 1. Installer le driver Chrome :
```bash
# Créer le dossier drivers s'il n'existe pas
mkdir drivers
# Télécharger le driver binaire
vendor/bin/bdi driver:chromedriver drivers/
```

### 2. Lancer le serveur de test manuellement (obligatoire pour Panther sur Windows) :
Ouvre un terminal séparé et laisse-le tourner :
```bash
php -S 127.0.0.1:8000 -t public
```

### 3. Lancer les tests
```bash
# Tous les tests
php bin/phpunit

# Uniquement les tests E2E (Navigateur)
php bin/phpunit tests/E2E/RegistrationE2ETest.php
```

## ▶️ Lancer le projet en développement

### 1. Serveur Backend :
```bash
symfony serve
```

### 2. Watcher Frontend (Webpack Encore) :
```bash
npm run watch
```