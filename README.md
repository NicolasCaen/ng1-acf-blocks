# NG1 ACF Blocks Manager

Un plugin WordPress qui facilite la gestion des blocs ACF (Advanced Custom Fields) en automatisant leur chargement et leur configuration.

## Description

Ce plugin offre deux fonctionnalités principales :

1. **Chargement Automatique des Blocs ACF** (`Ng1LoadThemeAcfBLocks`)
   - Charge automatiquement les blocs ACF depuis le dossier `acf-blocks` du thème
   - Gère l'enregistrement des scripts JS associés aux blocs
   - Configure les catégories de blocs personnalisées
   - Implémente un système de cache pour optimiser les performances

2. **Gestion des Champs ACF** (`Ng1AcfFieldsJsonToBock`)
   - Synchronise les fichiers JSON des champs ACF avec les dossiers des blocs
   - Copie automatiquement les configurations de champs vers les dossiers appropriés
   - Facilite la gestion des versions et la migration des champs

## Structure des Dossiers

ng1-acf-blocks :

theme/
├── acf-blocks/
│ └── mon-block/
│ ├── block.json
│ ├── assets/
│ │ └── js/
│ │ └── function.js
│ └── acf/
│ └── fields.json
└── acf-json/
└── group_xxx.json

## Installation

1. Téléchargez le plugin dans le dossier `wp-content/plugins/`
2. Activez le plugin dans l'administration WordPress
3. Créez un dossier `acf-blocks` dans votre thème

## Utilisation

### Création d'un Nouveau Bloc

1. Créez un nouveau dossier dans `acf-blocks/`
2. Ajoutez un fichier `block.json` avec la configuration du bloc
3. Les champs ACF seront automatiquement synchronisés

### Scripts JavaScript

- Placez vos scripts JS dans `assets/js/function.js`
- Ils seront automatiquement chargés avec le bloc

## Fonctionnalités

- Chargement automatique des blocs ACF
- Synchronisation des champs ACF
- Gestion des dépendances JavaScript
- Système de cache intégré
- Interface d'administration pour la synchronisation

## Prérequis

- WordPress 5.8+
- Advanced Custom Fields PRO 5.0+
- PHP 7.4+

## Support

Pour toute question ou problème, veuillez créer une issue sur le dépôt GitHub.

## Licence

Ce plugin est sous licence GPL v2 ou ultérieure.
