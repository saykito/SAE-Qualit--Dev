# OLDating

SAE Qualité de développement du BUT Informatique 1re année (semestre 2), IUT de Marne-la-Vallée, Université Gustave Eiffel (2025 - 2026). Projet réalisé en équipe.

Application web de rencontres amicales pour les seniors : inscription à des après-midis de rencontre, messagerie, agenda et espace administrateur pour gérer les comptes et les événements.

## Fonctionnalités

- Inscription, connexion, mot de passe oublié et changement de mot de passe
- Découverte des autres membres et modification de son profil
- Messagerie instantanée (AJAX)
- Événements : consultation, inscription, liste des participants, agenda personnel
- Notifications
- Espace administrateur : gestion des utilisateurs (promotion, suppression, comptes inactifs depuis plus de 6 mois) et des événements (calendrier FullCalendar)

## Technologies

PHP (architecture MVC), PostgreSQL, PDO, HTML / CSS / JavaScript.

Sécurité : mots de passe hachés (`password_hash()`), requêtes préparées, échappement des sorties (`htmlspecialchars()`), contrôle des rôles sur chaque page.

## Structure du dépôt

```
.
├── OLDating_presentation_annotee.pptx.pptx   Présentation de soutenance
└── oldating_v5/oldating_v5/                   Code de l'application
    ├── index.php       Point d'entrée
    ├── config/         Connexion à la base (bdd.php)
    ├── model/          Accès aux données
    ├── controller/     Traitements
    ├── view/           Pages affichées
    ├── assets/         CSS et JavaScript
    └── sql/            Scripts de création de la base
```

Le README détaillé de l'application (arborescence complète, sécurité) est dans [oldating_v5/oldating_v5/README.md](oldating_v5/oldating_v5/README.md). Il décrit l'ancienne installation MySQL sous WampServer : la version actuelle utilise PostgreSQL, voir ci-dessous.

## Lancer en local (XAMPP + PostgreSQL)

1. Copier le dossier `oldating_v5/oldating_v5` dans le dossier `htdocs` de XAMPP (par exemple sous le nom `oldating`).
2. Activer l'extension `pdo_pgsql` dans `php.ini`, puis redémarrer Apache.
3. Créer une base PostgreSQL et y exécuter `sql/oldating_v5_postgres.sql`.
4. Renseigner le nom de la base, l'utilisateur et le mot de passe dans `config/bdd.php`.
5. Ouvrir http://localhost/oldating/.

## Comptes de démonstration

| Rôle | Email | Mot de passe |
| --- | --- | --- |
| Administrateur | `admin@oldating.fr` | `admin123` |
| Senior | `chantal@mail.fr` | `senior123` |
| Senior | `andre@mail.fr` | `senior123` |
| Senior | `marie@mail.fr` | `senior123` |

Le compte de Marie a une dernière connexion de plus de 6 mois : il apparaît comme inactif dans la gestion des utilisateurs.
