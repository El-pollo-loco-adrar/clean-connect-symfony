# Projet Symfony + Vue + Tailwind CSS

Ce projet est une application **Symfony** avec un front en **Vue 3**, stylé avec **Tailwind CSS** et bundlé via **Webpack Encore**.

---
## 🚀 Prérequis

Avant de lancer le projet, assure-toi d’avoir installé :

### Backend
- PHP ≥ 8.1
- Composer
- Symfony CLI (recommandé)
- Un serveur de base de données si nécessaire (MySQL / MariaDB / PostgreSQL)

### Frontend
- Node.js ≥ 18
- npm (fourni avec Node.js)

Vérification rapide :
```bash
php -v
composer -V
node -v
npm -v
symfony -v
```

### Installer les dépendances PHP
```bash
composer install
```

### Installer les dépendances FRONT
```bash
npm install

```
## Base de données

### Installer fixtures et Faker
```bash
composer require --dev orm-fixtures
composer require fakerphp/faker
```

```bash
Modifier le fichier /src/DataFixtures 
```

### Changer l'adresse de la bdd
```bash
Dans le fichier .env
DATABASE_URL="mysql://root:@127.0.0.1:3306/clean-test?serverVersion=8.0.32&charset=utf8mb4"
```

## ▶️ Lancer le projet en développement
1️⃣ Lancer le serveur Symfony
```bash
symfony serve
```

2️⃣ Lancer le watcher frontend
```bash
npm run watch
```

### Ce watcher compile automatiquement :

Vue

Tailwind

JavaScript

CSS