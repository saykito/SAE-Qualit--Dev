# OLDating — Site de rencontre amicale des seniors

Projet BUT1 Informatique — Module *Qualité de développement* — Semestre 2.
Application web (PHP / MySQL) permettant aux seniors de se rencontrer, de
s'inscrire à des après-midis de rencontre, de discuter par messagerie et de
gérer leur agenda. Un espace administrateur permet de gérer les comptes et les
événements.

## Architecture (modèle MVC)

```
oldating/
├── index.php                  Point d'entrée (redirige selon connexion)
├── config/
│   └── bdd.php                Connexion PDO à la base
├── model/                     Accès aux données (requêtes préparées)
│   ├── utilisateur.php
│   └── evenement.php
├── controller/                Traitements / logique métier
│   ├── connexion.php          Authentification
│   ├── inscription.php        Création de compte (+ SMS simulé)
│   ├── ajouter_evenement.php
│   ├── inscription_evenement.php
│   ├── envoyer_message.php    Chat (AJAX)
│   ├── fetch_messages.php     Chat (AJAX)
│   ├── agenda.php
│   ├── modifier_profil.php
│   ├── modifier_statut.php
│   ├── promouvoir_utilisateur.php
│   ├── supprimer_utilisateur.php
│   ├── supprimer_evenements.php
│   ├── supprimer_notification.php
│   └── load_events.php        Données pour FullCalendar
├── view/                      Pages affichées (HTML/PHP)
│   ├── authentification.php
│   ├── inscription.php
│   ├── espace_senior.php / espace_admin.php
│   ├── messagerie.php
│   ├── evenement.php / agenda.php / mes_evenements.php
│   ├── admin_utilisateurs.php / admin_evenements.php
│   └── ...
├── assets/
│   ├── style.css
│   └── chat.js
└── sql/
    └── oldating.sql           Création de la base + jeu de données de test
```

## Installation (WampServer)

1. Copier le dossier `oldating/` dans `C:\wamp64\www\`.
2. Démarrer WampServer (icône verte).
3. Ouvrir phpMyAdmin (`http://localhost/phpmyadmin`).
4. Onglet **Importer** → choisir `sql/oldating.sql` → **Exécuter**.
   (La base `oldating` et toutes les tables sont créées avec des données de test.)
5. Ouvrir l'application : `http://localhost/oldating/`.

## Comptes de démonstration

| Rôle          | Email                | Mot de passe |
|---------------|----------------------|--------------|
| Administrateur| `admin@oldating.fr`  | `admin123`   |
| Senior        | `chantal@mail.fr`    | `senior123`  |
| Senior        | `andre@mail.fr`      | `senior123`  |
| Senior        | `marie@mail.fr`      | `senior123`  |

Le compte de Marie a une dernière connexion datée de plus de 6 mois : il
apparaît signalé « Inactif > 6 mois » dans la gestion des utilisateurs (besoin 4).

## Sécurité mise en œuvre

- Mots de passe **hachés** avec `password_hash()` / `password_verify()`.
- **Requêtes préparées** PDO partout (protection contre les injections SQL).
- **Échappement** des sorties avec `htmlspecialchars()` (protection XSS).
- **Contrôle des rôles** sur chaque page (senior / admin) et vérification de session.
