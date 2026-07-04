-- =====================================================================
--  Projet : OLDating - Site de rencontre amicale des seniors
--  Base de données : oldating
--  SGBD : MySQL / MariaDB (WampServer)
--  Encodage : utf8mb4 (gestion complète des accents et emojis)
--
--  Pour installer : ouvrir phpMyAdmin > onglet "Importer" > choisir ce
--  fichier, OU coller son contenu dans l'onglet "SQL".
-- =====================================================================

DROP DATABASE IF EXISTS oldating;
CREATE DATABASE oldating CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE oldating;

-- ---------------------------------------------------------------------
-- Table : utilisateur
-- Contient les seniors adhérents ET les administrateurs (champ role).
-- date_creation et date_derniere_connexion servent à repérer les
-- comptes inactifs depuis plus de 6 mois (besoin 4 du cahier des charges).
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur (
    id_utilisateur        INT AUTO_INCREMENT PRIMARY KEY,
    nom                   VARCHAR(100)  NOT NULL,
    prenom                VARCHAR(100)  NOT NULL,
    date_naissance        DATE          NOT NULL,
    mail                  VARCHAR(255)  NOT NULL UNIQUE,
    mot_de_passe          VARCHAR(255)  NOT NULL,           -- haché avec password_hash()
    ville                 VARCHAR(100)  NOT NULL,
    role                  ENUM('senior','admin') NOT NULL DEFAULT 'senior',
    statut                ENUM('en ligne','absent','ne pas déranger') NOT NULL DEFAULT 'en ligne',
    date_creation         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_derniere_connexion DATETIME    NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : profil
-- Informations publiques que le senior renseigne lui-même (pseudo,
-- avatar, centres d'intérêt...). Relation 1-1 avec utilisateur.
-- ---------------------------------------------------------------------
CREATE TABLE profil (
    id_profil        INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur   INT NOT NULL UNIQUE,
    pseudo           VARCHAR(100) NOT NULL,
    photo            TEXT NULL,                     -- URL ou nom de l'avatar
    description      TEXT NULL,
    situation        VARCHAR(100) NULL,                     -- situation familiale
    centres_interet  VARCHAR(255) NULL,
    CONSTRAINT fk_profil_utilisateur
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : evenement
-- Après-midis de rencontre, sorties, et événements personnels.
-- id_createur NULL = événement public créé par l'administrateur.
-- id_createur renseigné = événement personnel d'un senior (agenda).
-- ---------------------------------------------------------------------
CREATE TABLE evenement (
    id_evenement         INT AUTO_INCREMENT PRIMARY KEY,
    titre                VARCHAR(150) NOT NULL,
    description          TEXT NULL,
    lieu                 VARCHAR(150) NULL,
    date_heure           DATETIME NOT NULL,
    type                 VARCHAR(50) NOT NULL DEFAULT 'autre',
    nb_max_participants  INT NOT NULL DEFAULT 10,
    id_createur          INT NULL,
    CONSTRAINT fk_evenement_createur
        FOREIGN KEY (id_createur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : participation
-- Table d'association (N-N) entre utilisateur et evenement.
-- La clé primaire composite empêche une double inscription.
-- ---------------------------------------------------------------------
CREATE TABLE participation (
    id_utilisateur    INT NOT NULL,
    id_evenement      INT NOT NULL,
    date_inscription  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilisateur, id_evenement),
    CONSTRAINT fk_participation_utilisateur
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,
    CONSTRAINT fk_participation_evenement
        FOREIGN KEY (id_evenement) REFERENCES evenement(id_evenement)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : message
-- Messagerie privée (chat) entre deux seniors.
-- ---------------------------------------------------------------------
CREATE TABLE message (
    id_message        INT AUTO_INCREMENT PRIMARY KEY,
    id_emetteur       INT NOT NULL,
    id_destinataire   INT NOT NULL,
    contenu           TEXT NOT NULL,
    date_heure        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_message_emetteur
        FOREIGN KEY (id_emetteur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,
    CONSTRAINT fk_message_destinataire
        FOREIGN KEY (id_destinataire) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : notifications
-- Demandes adressées à l'administrateur (ex : mot de passe oublié).
-- ---------------------------------------------------------------------
CREATE TABLE notifications (
    id_notification   INT AUTO_INCREMENT PRIMARY KEY,
    type              VARCHAR(50) NOT NULL,
    contenu           TEXT NOT NULL,
    date_creation     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================================
--  JEU DE DONNÉES DE DÉMONSTRATION
--  Mots de passe en clair (pour vos tests) :
--    - admin@oldating.fr   -> admin123     (rôle administrateur)
--    - chantal@mail.fr      -> senior123
--    - andre@mail.fr        -> senior123
--    - marie@mail.fr        -> senior123
--  Les hachages ci-dessous sont compatibles password_verify() de PHP.
-- =====================================================================

INSERT INTO utilisateur (nom, prenom, date_naissance, mail, mot_de_passe, ville, role, statut, date_derniere_connexion) VALUES
('Tran',    'Louis',   '1965-04-12', 'admin@oldating.fr', '$2y$10$Ap6DBmTXWFmv/7ZOyLqnx.oaas4ArVp4wRzBjAPdpq6InXOxg.aQu', 'Noisy-le-Grand', 'admin',  'en ligne', NOW()),
('Dion',    'Chantal', '1957-09-03', 'chantal@mail.fr',   '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Noisy-le-Grand', 'senior', 'en ligne', NOW()),
('Dupont',  'André',   '1952-01-22', 'andre@mail.fr',     '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Chelles',        'senior', 'absent',   NOW()),
('Cavier',  'Marie',   '1960-11-30', 'marie@mail.fr',     '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Champs-sur-Marne','senior','en ligne', '2024-08-01 10:00:00'); -- compte inactif > 6 mois (test admin)

INSERT INTO profil (id_utilisateur, pseudo, photo, description, situation, centres_interet) VALUES
(2, 'Chantal67', '', 'Passionnée de jardinage et de scrabble.', 'Veuve', 'Scrabble, échecs, jazz'),
(3, 'Dédé',      '', 'Ancien instituteur, amateur de belote.',  'Marié', 'Belote, marche, histoire'),
(4, 'Marie',     '', 'J''aime la danse et les après-midis musicaux.', 'Célibataire', 'Danse, chant, cuisine');

INSERT INTO evenement (titre, description, lieu, date_heure, type, nb_max_participants, id_createur) VALUES
('Après-midi rencontre & galette', 'Speed-dating amical autour d''une galette des rois.', 'Salle municipale, Noisy-le-Grand', '2026-06-15 14:30:00', 'rencontre', 20, NULL),
('Tournoi de belote',              'Venez défier les autres adhérents à la belote.',     'Foyer des seniors, Chelles',       '2026-06-20 15:00:00', 'jeux',      16, NULL),
('Après-midi chants & musique',    'Karaoké et chansons d''autrefois.',                  'EHPAD Croix-Biche',                '2026-06-28 14:00:00', 'musique',   25, NULL),
('Anniversaire de Chantal',        'Note personnelle dans mon agenda.',                  NULL,                               '2026-09-03 00:00:00', 'perso',      1, 2);

INSERT INTO participation (id_utilisateur, id_evenement) VALUES
(2, 1),
(3, 1),
(2, 2),
(4, 3);

INSERT INTO message (id_emetteur, id_destinataire, contenu, date_heure) VALUES
(2, 3, 'Bonjour André, on se voit à la galette ?', '2026-05-28 10:15:00'),
(3, 2, 'Avec plaisir Chantal, j''y serai !',         '2026-05-28 10:20:00');

INSERT INTO notifications (type, contenu, date_creation) VALUES
('mot_de_passe', 'Demande de réinitialisation de mot de passe pour : marie@mail.fr', '2026-05-30 09:00:00');
