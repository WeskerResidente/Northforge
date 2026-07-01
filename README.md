# Northforge

Northforge est une application Symfony 6.4 basée sur Doctrine, Twig, Webpack Encore et AssetMapper.
Le projet utilise aussi EasyAdmin, VichUploaderBundle et Symfony UX.

## Stack

- PHP 8.1+
- Symfony 6.4
- Doctrine ORM / Migrations
- Twig
- Webpack Encore + Sass
- AssetMapper pour les images et les icônes
- VichUploaderBundle pour l'upload d'images
- EasyAdmin

## Installation

```bash
composer install
npm install
```

## Configuration

1. Copier le fichier d'environnement si besoin :

```bash
cp .env .env.local
```

2. Configurer la base de données dans `DATABASE_URL`.

3. Créer et migrer la base de données :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Lancer le projet

### Serveur Symfony

```bash
symfony server:start
```

### Compilation des assets

```bash
npm run dev
```

En mode développement, tu peux aussi utiliser :

```bash
npm run watch
```

## Commandes utiles

```bash
php bin/console cache:clear
php bin/console debug:router
php bin/console debug:asset-map
php bin/console lint:yaml config/packages/vich_uploader.yaml
```

## Structure principale

- `src/Controller` : contrôleurs
- `src/Entity` : entités Doctrine
- `templates/` : vues Twig
- `assets/` : styles, scripts et images gérés par Symfony
- `icons/` : icônes utilisées dans la navigation
- `public/` : fichiers publics exposés au navigateur
- `migrations/` : migrations Doctrine

## Fonctionnalités déjà en place

- Page d'accueil configurée comme page principale
- Barre de navigation personnalisée dans le layout principal
- Gestion des images et icônes via AssetMapper
- Upload d'images préparé avec VichUploaderBundle

## Notes

- Les images du header sont lues depuis `assets/images` et `icons/`.
- Les styles du projet sont compilés avec Sass via Webpack Encore.
- Si une icône ou une image n'apparaît pas, vérifier d'abord le chemin dans `debug:asset-map`.
