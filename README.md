🔗 Lien vers le dépôt d'origine : 

Le dépôt original de l'application est disponible ici : [MediatekFormation](https://github.com/CNED-SLAM/mediatekformation). Celui-ci contient la présentation complète de l'application initiale.

📌 Présentation

Ce dépôt est une version améliorée de MediatekFormation, un site développé avec Symfony 6.4 permettant d'accéder aux vidéos d'auto-formation d'une chaîne de médiathèques.
Cette version intègre plusieurs nouvelles fonctionnalités et améliorations détaillées ci-dessous.

🚀 Fonctionnalités ajoutées

Filtrage avancé : Ajout de filtres pour retrouver les formations plus facilement.
![image](https://github.com/user-attachments/assets/9baa8015-20ec-417d-b128-ccb7537a7a5a)


Gestion administrateur : Implémentation d'un système d'authentification et d'autorisation.
![image](https://github.com/user-attachments/assets/61c1ec79-70c2-4abf-8957-44e040d0e165)


Optimisation des performances : Réduction du temps de chargement des pages.

Ajout d'un espace administrateur : Permet la gestion des formations, playlists et utilisateurs.
![image](https://github.com/user-attachments/assets/493540ff-dc8a-4f7b-b11d-71ba95615053)
![image](https://github.com/user-attachments/assets/c3ffce01-b68d-4d31-b3c0-226ba58e211e)
![image](https://github.com/user-attachments/assets/61c235ee-ca97-41c6-8381-848501d0b8ae)


🛠 Installation en local

Avant de commencer, assurez-vous que vous avez installé les outils suivants :

Composer

Git

WampServer (ou équivalent : XAMPP, MAMP...)

Étapes d'installation

Cloner le dépôt :

git clone https://github.com/byadrien0/Mediatekformation

Se déplacer dans le dossier du projet :

cd mediatekformation

Installer les dépendances :

composer install

Configurer la base de données :

Se connecter à phpMyAdmin.

Créer une base de données mediatekformation.

Importer le fichier mediatekformation.sql.

Modifier le fichier .env pour y renseigner les accès à la BDD.

Lancer le serveur Symfony :

symfony server:start

Accéder à l'application :
Ouvrir un navigateur et se rendre sur :

http://localhost/mediatekformation/public/index.php

🌍 Tester l'application en ligne

L'application est également accessible en ligne. Voici les étapes pour l'utiliser :

Se rendre à l'URL suivante : http://localhost/mediatekformation/public/index.php

Naviguer sur les différentes pages et tester les nouvelles fonctionnalités.

⚠️ Attention : Les identifiants d'accès à l'administration ne sont pas communiqués ici.
