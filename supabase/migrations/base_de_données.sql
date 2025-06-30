-- Configuration de la base de données pour le projet Portfolio ESGI
-- Créer la base de données et l'utilisateur

DROP TABLE IF EXISTS users, skills, user_skills, projects, sessions, password_resets;

CREATE DATABASE IF NOT EXISTS projetb2;
USE projetb2;

-- Créer l'utilisateur si nécessaire
-- CREATE USER 'projetb2'@'localhost' IDENTIFIED BY 'password';
-- GRANT ALL PRIVILEGES ON projetb2.* TO 'projetb2'@'localhost';
-- FLUSH PRIVILEGES;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    bio TEXT,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des compétences
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(100) DEFAULT 'Général',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table de liaison utilisateur-compétences
CREATE TABLE user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    level ENUM('debutant', 'intermediaire', 'avance', 'expert') DEFAULT 'debutant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_skill (user_id, skill_id)
);

-- Table des projets
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    link VARCHAR(255),
    technologies VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des sessions
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE password_resets
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT         NOT NULL,
    token      VARCHAR(64) NOT NULL,
    expires_at DATETIME    NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    UNIQUE KEY (token)
);

-- Insertion des données de test

-- Insertion des utilisateurs (mots de passe : 'password')
INSERT INTO users (email, password, first_name, last_name, role, bio) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Système', 'admin', 'Administrateur du système de portfolio'),
('user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean', 'Dupont', 'user', 'Développeur Full Stack passionné par les nouvelles technologies'),
('marie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie', 'Martin', 'user', 'Designer UX/UI créative avec 5 ans d\'expérience');

-- Insertion des compétences
INSERT INTO skills (name, category) VALUES
('PHP', 'Langages'),
('JavaScript', 'Langages'),
('HTML/CSS', 'Langages'),
('MySQL', 'Bases de données'),
('React', 'Frameworks'),
('Vue.js', 'Frameworks'),
('Node.js', 'Backend'),
('Photoshop', 'Design'),
('Figma', 'Design'),
('Git', 'Outils');

-- Insertion des compétences utilisateurs
INSERT INTO user_skills (user_id, skill_id, level) VALUES
-- Jean Dupont
(2, 1, 'avance'), -- PHP
(2, 2, 'expert'), -- JavaScript
(2, 3, 'expert'), -- HTML/CSS
(2, 4, 'avance'), -- MySQL
(2, 5, 'intermediaire'), -- React
-- Marie Martin
(3, 2, 'intermediaire'), -- JavaScript
(3, 3, 'expert'), -- HTML/CSS
(3, 8, 'expert'), -- Photoshop
(3, 9, 'expert'), -- Figma
(3, 10, 'avance'); -- Git

-- Insertion des projets
INSERT INTO projects (user_id, title, description, link, technologies) VALUES
-- Projets de Jean Dupont
(2, 'E-commerce en PHP', 'Application e-commerce complète développée en PHP avec panier, paiement et gestion des stocks.', 'https://github.com/jean/ecommerce', 'PHP, MySQL, JavaScript, Bootstrap'),
(2, 'API REST Node.js', 'API RESTful pour une application mobile avec authentification JWT et documentation Swagger.', 'https://github.com/jean/api-rest', 'Node.js, Express, MongoDB, JWT'),
(2, 'Dashboard Analytics', 'Tableau de bord temps réel avec graphiques interactifs pour analyser les données de vente.', 'https://github.com/jean/dashboard', 'React, Chart.js, PHP, MySQL'),
-- Projets de Marie Martin
(3, 'Redesign Application Mobile', 'Refonte complète de l\'UX/UI d\'une application mobile de fitness avec 50k+ utilisateurs.', 'https://dribbble.com/marie/fitness-app', 'Figma, Principle, Adobe XD'),
(3, 'Site Vitrine Restaurant', 'Création d\'un site web élégant pour un restaurant gastronomique avec réservation en ligne.', 'https://marie-designs.com/restaurant', 'HTML/CSS, JavaScript, WordPress'),
(3, 'Système de Design', 'Développement d\'un design system complet pour une startup fintech avec composants réutilisables.', 'https://github.com/marie/design-system', 'Figma, Storybook, CSS, React');