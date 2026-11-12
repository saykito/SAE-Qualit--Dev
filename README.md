# OLDating, rencontres amicales entre seniors

Projet d'équipe, module Qualité de développement, BUT Informatique 1re année, IUT de Marne-la-Vallée (2026).

Application web qui permet aux seniors de créer un profil, de découvrir d'autres membres, de discuter par messagerie et de s'inscrire à des après-midis de rencontre. Un espace administrateur gère les comptes et les événements.

## Fonctionnalités

- Inscription, connexion, mot de passe oublié
- Profil modifiable, page de découverte des membres
- Messagerie instantanée en AJAX
- Événements : création, inscription, liste des participants, agenda personnel
- Espace administrateur : gestion des utilisateurs et des événements

## Technologies

- PHP, architecture MVC (`model/`, `view/`, `controller/`)
- PostgreSQL via PDO, requêtes préparées
- JavaScript (AJAX pour le chat), HTML, CSS

## Lancer en local (XAMPP)

1. Copier le dossier `oldating_v5/oldating_v5` dans `C:\xampp\htdocs\` et le renommer `oldating`.
2. Créer une base PostgreSQL et exécuter `sql/oldating_v5_postgres.sql`.
3. Renseigner la connexion dans `config/bdd.php`.
4. Ouvrir http://localhost/oldating.

Les comptes de démonstration sont définis dans le script SQL.

## Documents

- `OLDating_presentation_annotee.pptx.pptx` : support de soutenance
- `oldating_v5/oldating_v5/README.md` : détail de l'architecture fichier par fichier
