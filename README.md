# Portfolio ESGI - Projet PHP & MySQL

## 📝 Description

Application web complète de gestion de portfolios développée en PHP avec MySQL, respectant les exigences pédagogiques de l'ESGI (année 2024/2025). Cette plateforme permet aux utilisateurs de créer et gérer leurs portfolios professionnels avec un système d'authentification sécurisé et une interface d'administration.

## ✨ Fonctionnalités principales

### 🔐 Authentification & Sécurité
- **Inscription** avec validation des champs et persistance en cas d'erreur
- **Connexion** sécurisée avec `password_hash()` et option "se souvenir de moi"
- **Gestion des rôles** : Administrateur et Utilisateur
- **Réinitialisation de mot de passe** (fonctionnalité bonus)
- **Protection avancée** : XSS, injections SQL, CSRF avec expiration automatique
- **Sessions sécurisées** avec nettoyage automatique

### 👥 Gestion des utilisateurs
- **Profils utilisateurs** complets avec photo et biographie
- **Tableau de bord** personnalisé avec statistiques
- **Mise à jour du profil** avec upload d'image sécurisé
- **Déconnexion** sécurisée avec suppression des sessions

### 🛠️ Gestion des compétences
- **Interface administrateur** pour créer/modifier/supprimer les compétences
- **Système de niveaux** : Débutant → Intermédiaire → Avancé → Expert
- **Catégorisation** des compétences (Langages, Frameworks, Outils, etc.)
- **Assignation personnalisée** par utilisateur avec niveaux

### 📁 Gestion des projets
- **CRUD complet** : Création, lecture, modification, suppression
- **Upload d'images** sécurisé avec validation (format, taille)
- **Informations détaillées** : titre, description, technologies, liens
- **Organisation** par utilisateur avec aperçu public

### 🎨 Interface & Design
- **Design moderne** et responsive (mobile-first)
- **Interface intuitive** avec navigation claire
- **Animations CSS** subtiles et micro-interactions
- **Système de couleurs** cohérent et professionnel
- **Accessibilité** optimisée

## 🚀 Installation locale

### Prérequis
- **PHP 7.4+** avec extensions PDO et GD
- **MySQL 5.7+** ou MariaDB
- **Serveur web** (Apache/Nginx) ou environnement local (XAMPP/WAMP)

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone [URL_DU_REPO]
   cd portfolio-esgi
   ```

2. **Configuration de la base de données**
   ```bash
   # Créer la base de données MySQL
   mysql -u root -p
   CREATE DATABASE projetb2;
   CREATE USER 'projetb2'@'localhost' IDENTIFIED BY 'password';
   GRANT ALL PRIVILEGES ON projetb2.* TO 'projetb2'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   
   # Importer le schéma et les données
   mysql -u projetb2 -p projetb2 < config/database.sql
   ```

3. **Configuration du projet**
   - Vérifier les paramètres dans `config/database.php`
   - Créer le dossier `uploads/` avec permissions d'écriture
   ```bash
   mkdir uploads
   chmod 755 uploads
   ```
    - Installer les dépendances avec Composer
    ```bash
    composer install
    ```
4. **Configuration des variables d'environnement**
5. Créer un fichier `.env` à la racine du projet avec les variables suivantes :
   ```plaintext
   DB_HOST=localhost
   DB_NAME=projetb2
   DB_USER=projetb2
   DB_PASSWORD=password
   ```
    - Assurez-vous que le fichier `.env` est dans le `.gitignore` pour éviter de le pousser sur le dépôt.
    - Si vous n'avez pas Composer, installez-le depuis [getcomposer.org](https://getcomposer.org/download/).
    - Si vous utilisez Supabase, placez les migrations dans le dossier `supabase/migrations/` et exécutez-les via l'interface Supabase.
    - Si vous utilisez un environnement local comme XAMPP ou WAMP, placez le projet dans le dossier `htdocs/` ou `www/` respectivement. 

6. **Lancement du serveur**
   ```bash
   # Avec PHP intégré
   php -S localhost:8000
   
   # Ou avec XAMPP/WAMP
   # Placer le projet dans htdocs/ et accéder via http://localhost/portfolio-esgi
   ```

7. **Accès à l'application**
   - Ouvrir http://localhost:8000 dans votre navigateur
   - Utiliser les comptes de test ci-dessous

## 👤 Comptes de test

### Administrateur
- **Email** : admin@example.com
- **Mot de passe** : password
- **Accès** : Panel d'administration complet

### Utilisateurs
- **Email** : user@example.com / **Mot de passe** : password
- **Email** : marie@example.com / **Mot de passe** : password

> Tous les mots de passe sont hashés avec `password_hash()` pour la sécurité.

## 📂 Structure du projet

```
portfolio-esgi/
├── config/
│   ├── database.php       # Configuration base de données
│   └── base_de_données.sql # Schéma et données de test
├── includes/
│   ├── functions.php      # Fonctions utilitaires
│   ├── header.php         # En-tête commun
│   └── footer.php         # Pied de page
├── admin/                 # Interface d’administration
│   ├── index.php          # Dashboard admin
│   ├── users.php          # Gestion des utilisateurs
│   ├── skills.php         # Gestion des compétences
│   ├── projects.php       # Gestion des projets
│   ├── add-skill.php
│   ├── delete-skill.php
│   ├── delete-project.php
│   ├── edit-user.php
│   ├── edit-project.php
│   └── get-project.php
├── assets/
│   ├── css/
│   │   └── style.css      # Styles principaux
│   └── js/
│       └── script.js      # JavaScript global
├── supabase/              # Migrations Supabase
│   └── migrations/
│       └── base_de_données.sql
├── uploads/               # Dossier des fichiers uploadés
├── vendor/                # Dépendances Composer
├── .env                   # Variables d’environnement
├── .gitignore
├── composer.json
├── composer.lock
├── index.php              # Page d’accueil
├── login.php              # Connexion
├── register.php           # Inscription
├── forgot-password.php    # Mot de passe oublié
├── reset-password.php     # Réinitialisation de mot de passe
├── dashboard.php          # Tableau de bord utilisateur
├── portfolio.php          # Affichage public des portfolios
├── portfolios.php         # Liste des portfolios
├── manage-projects.php    # Gestion des projets (front)
├── manage-skills.php      # Gestion des compétences (front)
├── profile.php            # Modification du profil
├── delete-profile-image.php # Suppression de l’image de profil
├── delete-project.php     # Suppression d’un projet (front)
├── edit-project.php       # Modification d’un projet (front)
├── logout.php             # Déconnexion
└── README.md              # Documentation du projet

```

## 🔧 Technologies utilisées

- **Backend** : PHP 7.4+ avec PDO
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript ES6
- **Sécurité** : Protection XSS, CSRF, injection SQL
- **Design** : CSS Grid, Flexbox, responsive design
- **Icons** : Font Awesome 6

## 🛡️ Sécurité implémentée

- **Hachage des mots de passe** avec `password_hash()`
- **Tokens CSRF** avec expiration (1 heure)
- **Validation et échappement** de toutes les entrées utilisateur
- **Requêtes préparées** pour éviter les injections SQL
- **Upload sécurisé** avec validation de type et taille
- **Sessions sécurisées** avec nettoyage automatique
- **Gestion des cookies** HttpOnly pour "se souvenir de moi"

## 📊 Base de données

### Tables principales
- **users** : Comptes utilisateurs avec rôles
- **skills** : Compétences disponibles
- **user_skills** : Association utilisateur-compétences avec niveaux
- **projects** : Projets des utilisateurs
- **sessions** : Sessions persistantes pour "se souvenir de moi"
- **password_resets** : Réinitialisation de mot de passe

### Données de test incluses
- 3 comptes utilisateurs (1 admin + 2 utilisateurs)
- 6 projets répartis sur les utilisateurs
- 10 compétences dans différentes catégories
- Associations compétences-utilisateurs avec niveaux variés

## 🎯 Conformité ESGI

Ce projet respecte intégralement les exigences du sujet :

✅ **Structure obligatoire** : `config/database.sql` avec constantes requises  
✅ **Authentification complète** : inscription, connexion, rôles, cookies  
✅ **Sécurité avancée** : XSS, CSRF, injections SQL  
✅ **Gestion des compétences** : CRUD admin + niveaux utilisateur  
✅ **Gestion des projets** : CRUD complet avec upload sécurisé  
✅ **Données de test** : 3 comptes, projets, compétences  
✅ **README complet** : installation, utilisation, structure  

## 📄 Licence

Ce projet est développé dans le cadre pédagogique de l'ESGI. Libre d'utilisation pour l'apprentissage.


---

**Projet réalisé dans le cadre du cursus ESGI 2024/2025**