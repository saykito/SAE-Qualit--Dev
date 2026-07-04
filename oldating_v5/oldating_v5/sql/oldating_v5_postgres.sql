-- =====================================================================
--  OLDating v5 - PostgreSQL - avec photos portraits réalistes
-- =====================================================================

DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS message CASCADE;
DROP TABLE IF EXISTS participation CASCADE;
DROP TABLE IF EXISTS evenement CASCADE;
DROP TABLE IF EXISTS profil CASCADE;
DROP TABLE IF EXISTS utilisateur CASCADE;

CREATE TABLE utilisateur (
    id_utilisateur          SERIAL PRIMARY KEY,
    nom                     VARCHAR(100)  NOT NULL,
    prenom                  VARCHAR(100)  NOT NULL,
    date_naissance          DATE          NOT NULL,
    mail                    VARCHAR(255)  NOT NULL UNIQUE,
    mot_de_passe            VARCHAR(255)  NOT NULL,
    ville                   VARCHAR(100)  NOT NULL,
    role                    VARCHAR(10)   NOT NULL DEFAULT 'senior' CHECK (role IN ('senior','admin')),
    statut                  VARCHAR(20)   NOT NULL DEFAULT 'en ligne' CHECK (statut IN ('en ligne','absent','ne pas déranger')),
    date_creation           TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_derniere_connexion TIMESTAMP     NULL
);

CREATE TABLE profil (
    id_profil        SERIAL PRIMARY KEY,
    id_utilisateur   INT NOT NULL UNIQUE,
    pseudo           VARCHAR(100) NOT NULL,
    photo            TEXT NULL,
    avatar_emoji     VARCHAR(10)  NOT NULL DEFAULT '👤',
    description      TEXT NULL,
    situation        VARCHAR(100) NULL,
    centres_interet  VARCHAR(255) NULL,
    CONSTRAINT fk_profil_utilisateur
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

CREATE TABLE evenement (
    id_evenement         SERIAL PRIMARY KEY,
    titre                VARCHAR(150) NOT NULL,
    description          TEXT NULL,
    lieu                 VARCHAR(150) NULL,
    date_heure           TIMESTAMP NOT NULL,
    type                 VARCHAR(50) NOT NULL DEFAULT 'autre',
    nb_max_participants  INT NOT NULL DEFAULT 10,
    image                TEXT NULL,
    id_createur          INT NULL,
    CONSTRAINT fk_evenement_createur
        FOREIGN KEY (id_createur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

CREATE TABLE participation (
    id_utilisateur    INT NOT NULL,
    id_evenement      INT NOT NULL,
    date_inscription  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilisateur, id_evenement),
    CONSTRAINT fk_participation_utilisateur
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,
    CONSTRAINT fk_participation_evenement
        FOREIGN KEY (id_evenement) REFERENCES evenement(id_evenement)
        ON DELETE CASCADE
);

CREATE TABLE message (
    id_message        SERIAL PRIMARY KEY,
    id_emetteur       INT NOT NULL,
    id_destinataire   INT NOT NULL,
    contenu           TEXT NOT NULL,
    date_heure        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_message_emetteur
        FOREIGN KEY (id_emetteur) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,
    CONSTRAINT fk_message_destinataire
        FOREIGN KEY (id_destinataire) REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

CREATE TABLE notifications (
    id_notification   SERIAL PRIMARY KEY,
    type              VARCHAR(50) NOT NULL,
    contenu           TEXT NOT NULL,
    date_creation     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
--  UTILISATEURS
-- =====================================================================
INSERT INTO utilisateur (nom, prenom, date_naissance, mail, mot_de_passe, ville, role, statut, date_derniere_connexion) VALUES
('Tran',      'Louis',     '1965-04-12', 'admin@oldating.fr',   '$2y$10$Ap6DBmTXWFmv/7ZOyLqnx.oaas4ArVp4wRzBjAPdpq6InXOxg.aQu', 'Noisy-le-Grand',      'admin',  'en ligne',        NOW()),
('Dion',      'Chantal',   '1957-09-03', 'chantal@mail.fr',     '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Noisy-le-Grand',      'senior', 'en ligne',        NOW()),
('Dupont',    'André',     '1952-01-22', 'andre@mail.fr',       '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Chelles',             'senior', 'absent',          NOW()),
('Cavier',    'Marie',     '1960-11-30', 'marie@mail.fr',       '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Champs-sur-Marne',    'senior', 'en ligne',        '2024-08-01 10:00:00'),
('Lemaire',   'Gérard',   '1948-06-15', 'gerard@mail.fr',      '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Vincennes',           'senior', 'en ligne',        NOW()),
('Bernard',   'Jacqueline','1953-03-28', 'jacqueline@mail.fr',  '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Montreuil',           'senior', 'ne pas déranger', NOW()),
('Moreau',    'Roger',     '1945-11-07', 'roger@mail.fr',       '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Nogent-sur-Marne',    'senior', 'absent',          NOW()),
('Petit',     'Simone',    '1950-07-19', 'simone@mail.fr',      '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Fontenay-sous-Bois',  'senior', 'en ligne',        NOW()),
('Garnier',   'Michel',   '1956-02-14', 'michel@mail.fr',      '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Saint-Maur-des-Fossés','senior','en ligne',        NOW()),
('Roux',      'Yvette',    '1962-09-01', 'yvette@mail.fr',      '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Créteil',             'senior', 'en ligne',        NOW()),
('Fontaine',  'Bernard',   '1943-05-23', 'bernard@mail.fr',     '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Champigny-sur-Marne', 'senior', 'absent',          NOW()),
('Colin',     'Monique',   '1955-12-10', 'monique@mail.fr',     '$2y$10$wi/VXIqRAHPE0ew6lt1Y1.X8ZGeRY1hafzN0qEtU4sata9DrChlrK', 'Ivry-sur-Seine',      'senior', 'en ligne',        NOW());

-- =====================================================================
--  PROFILS avec photos Pixabay libres de droit
-- =====================================================================
INSERT INTO profil (id_utilisateur, pseudo, photo, avatar_emoji, description, situation, centres_interet) VALUES
(2,  'Chantal67',    'https://cdn.pixabay.com/photo/2016/11/21/14/53/adult-1845814_640.jpg',
     '👤', 'Passionnée de jardinage et de scrabble. J''adore les après-midis ensoleillés.', 'Veuve', 'Scrabble, Jardinage, Jazz'),

(3,  'Dédé52',       'https://cdn.pixabay.com/photo/2016/11/21/12/42/beard-1845166_640.jpg',
     '👤', 'Ancien instituteur, amateur de belote et de longues balades en forêt.', 'Marié', 'Belote, Marche, Histoire'),

(4,  'Marie60',      'https://cdn.pixabay.com/photo/2017/08/06/12/52/woman-2596828_640.jpg',
     '👤', 'J''aime la danse et les après-midis musicaux. La vie est trop courte !', 'Célibataire', 'Danse, Chant, Cuisine'),

(5,  'Gégé',         'https://cdn.pixabay.com/photo/2016/11/18/14/05/brick-wall-1834784_640.jpg',
     '👤', 'Passionné de pétanque et de bonne cuisine. Je cherche des amis pour jouer.', 'Divorcé(e)', 'Pétanque, Cuisine, Voyages'),

(6,  'JackyB',       'https://cdn.pixabay.com/photo/2019/04/01/22/09/woman-4096706_640.jpg',
     '👤', 'Ancienne infirmière, j''adore tricoter et regarder les étoiles.', 'Veuve', 'Tricot, Astronomie, Lecture'),

(7,  'Roger_le_sage','https://cdn.pixabay.com/photo/2017/09/25/13/12/man-2785071_640.jpg',
     '👤', 'Retraité de la SNCF, fan de photos et de belote. Toujours partant pour une sortie !', 'Veuf', 'Photo, Belote, Randonnée'),

(8,  'Simone75',     'https://cdn.pixabay.com/photo/2015/07/20/13/22/old-852498_640.jpg',
     '👤', 'Ancienne couturière. J''adore les marchés et les discussions autour d''un café.', 'Veuve', 'Couture, Marchés, Café'),

(9,  'Mimi_Michel',  'https://cdn.pixabay.com/photo/2015/01/08/18/27/man-593333_640.jpg',
     '👤', 'Ancien mécanicien reconverti en jardinier. Mon potager est ma fierté.', 'Marié', 'Jardinage, Bricolage, Pêche'),

(10, 'Yvette_soleil','https://cdn.pixabay.com/photo/2016/03/27/22/06/woman-1284411_640.jpg',
     '👤', 'Danseuse de salon à mes heures. Je cherche un partenaire de tango !', 'Célibataire', 'Danse, Cinéma, Voyages'),

(11, 'BernardF',     'https://cdn.pixabay.com/photo/2017/09/25/13/12/man-2785071_640.jpg',
     '👤', 'Ancien boulanger. Je fais encore le meilleur pain du quartier.', 'Marié', 'Cuisine, Pétanque, Football'),

(12, 'Monique_coeur','https://cdn.pixabay.com/photo/2017/07/18/14/10/person-2515326_640.jpg',
     '👤', 'Grand-mère de 5 petits-enfants. Je cherche des amies pour des sorties culturelles.', 'Veuve', 'Musées, Opéra, Jardins');

-- =====================================================================
--  ÉVÉNEMENTS
-- =====================================================================
INSERT INTO evenement (titre, description, lieu, date_heure, type, nb_max_participants, image, id_createur) VALUES
('Après-midi rencontre & galette',  'Speed-dating amical autour d''une galette des rois.',           'Salle municipale, Noisy-le-Grand',    '2026-06-15 14:30:00', 'rencontre', 20, NULL, NULL),
('Tournoi de belote',               'Venez défier les autres adhérents. Trophée pour le vainqueur !','Foyer des seniors, Chelles',          '2026-06-20 15:00:00', 'jeux',      16, NULL, NULL),
('Après-midi chants & musique',     'Karaoké et chansons d''autrefois. Ambiance garantie !',         'EHPAD Croix-Biche',                   '2026-06-28 14:00:00', 'musique',   25, NULL, NULL),
('Balade en forêt de Vincennes',    'Promenade de 5km. Niveau facile. Pique-nique tiré du sac.',    'Entrée Porte Jaune, Vincennes',       '2026-07-05 09:30:00', 'balade',    15, NULL, NULL),
('Atelier cuisine du monde',        'Chaque participant apporte un plat de sa région.',              'Centre social, Montreuil',            '2026-07-12 11:00:00', 'cuisine',   12, NULL, NULL),
('Sortie cinéma classique',         'Film en noir et blanc suivi d''un débat.',                      'Cinéma Le Kosmos, Fontenay-sous-Bois','2026-07-18 17:00:00', 'culture',   30, NULL, NULL),
('Pétanque & apéro',                'Tournoi amical suivi d''un apéro convivial.',                   'Parc de la Mairie, Nogent-sur-Marne', '2026-07-25 15:00:00', 'jeux',      20, NULL, NULL),
('Cours de danse de salon',         'Initiation au tango et à la valse pour tous niveaux.',          'Salle des fêtes, Saint-Maur',         '2026-08-02 14:00:00', 'danse',     18, NULL, NULL);

INSERT INTO participation (id_utilisateur, id_evenement) VALUES
(2,1),(3,1),(5,1),(8,1),(3,2),(2,2),(7,2),(12,2),(4,3),(10,3),(6,3),
(5,4),(7,4),(9,4),(3,4),(8,5),(4,5),(12,5),(6,6),(10,6),(4,6),
(5,7),(7,7),(9,7),(11,7),(10,8),(4,8),(2,8);

INSERT INTO message (id_emetteur, id_destinataire, contenu, date_heure) VALUES
(2,3,'Bonjour André, on se voit à la galette ?','2026-05-28 10:15:00'),
(3,2,'Avec plaisir Chantal, j''y serai !','2026-05-28 10:20:00'),
(2,3,'Super ! On pourrait y aller ensemble ?','2026-05-28 10:22:00'),
(4,10,'Bonjour Yvette, tu fais de la danse aussi ?','2026-05-29 14:00:00'),
(10,4,'Oui ! Le tango depuis 10 ans. On devrait se retrouver au cours !','2026-05-29 14:05:00');

INSERT INTO notifications (type, contenu, date_creation) VALUES
('mot_de_passe','Demande de réinitialisation pour : marie@mail.fr','2026-05-30 09:00:00');
