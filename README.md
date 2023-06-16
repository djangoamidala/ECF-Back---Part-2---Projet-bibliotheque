# ECF-Back---Part-2-et-3---Projet-bibliotheque

ECF permettant de gérer une bibliothèque avec l'empreint des livres par les utilisateurs

#Prérequis
- Importé le fichier de la BDD de l'ecf PART 1 
- PHP
- Composer
- MariaDB

#Installation

- Cloner le dépôt avec la commande
 git clone https://github.com/djangoamidala/ECF-Back---Part-2-et-3---Projet-bibliotheque.git
- Se rendre dans le dossier avec la commande cd ECF-Back---Part-2-et-3---Projet-bibliotheque
- Exécuter la commande 'composer install' pour installé les dépendances
- configurer l'identifiant créer à l'ecf 1 dans le fichier .env à la ligne suivante: DATABASE_URL=mysql://votre_utilisateur:votre_mot_de_passe@localhost/votre_base_de_donnees 
- Lancement du serveur avec symfony serve http://127.0.0.1:8000/
- visité la route pour les données de test http://127.0.0.1:8000/generate-fake-data
- Enjoy
    



