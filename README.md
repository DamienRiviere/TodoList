[![Codacy Badge](https://api.codacy.com/project/badge/Grade/de26aa436f9945ce82fdbd00c1ddc1f3)](https://www.codacy.com/manual/DamienRiviere/TodoList?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=DamienRiviere/TodoList&amp;utm_campaign=Badge_Grade)

# Améliorez une application existante de ToDo & Co

## Installation du projet

En fonction de votre système d'exploitation plusieurs serveurs peuvent être installés :

- Windows : WAMP (http://www.wampserver.com/)
- MAC : MAMP (https://www.mamp.info/en/mamp/)
- Linux : LAMP (https://doc.ubuntu-fr.org/lamp)
- XAMP (https://www.apachefriends.org/fr/index.html)

## Mise en place du projet

Installation de GIT :

    - GIT (https://git-scm.com/downloads) 
    
Une fois GIT installé, il faudra vous placer dans le répertoire de votre choix puis exécuté la commande suivante :

    - git clone https://github.com/DamienRiviere/TodoList.git
    
Le projet sera automatiquement copié dans le répertoire ciblé. Une fois le dossier copié dans votre répertoire il faut installer les dépendances avec la commande suivante :

    - composer install

## Configuration des variables d'environnement

Configurez les variables d'environnement comme la connexion à la base de données dans le fichier env.local qui sera créé à la racine du projet en copiant le fichier .env. Vous pourrez ensuite renseigner les identifiants de votre base de données en suivant le modèle ci-dessous.

    - DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7

## Création de la base de données

Créez la base de données de l'application en tapant la commande ci-dessous :

    - php bin/console doctrine:database:create
    
Puis lancer la migration pour créer les tables dans la base de données :

    - php bin/console doctrine:migrations:migrate    
    
## Gestion des assets

Vous pouvez générer à l'aide de Webpack vos assets Javascript et CSS avec NPM en tapant la commande ci-dessous :

    - npm run dev (en dev)
    - npm run build (en prod)
    
## Lancement du serveur

Vous pouvez lancer le serveur via la commande suivante :

    - symfony server:start

## Générer des fausses données

Vous pouvez générer des fausses données grâce la fixture présente dans le projet avec la commande suivante :

    - php bin/console doctrine:fixtures:load
    
## Qualité du code

Les librairies PHPStan et PHP_codesniffer sont installées dans le projet elle permette d'analyser le code et de corriger les erreurs qu'il contient.

PHP_codesniffer : 

    - vendor\bin\phpcs (analyse le code du projet)
    - vendor\bin\phpcbf (corrige les erreurs trouvées automatiquement)
    
PHPStan : 

    - vendor\bin\phpstan analyse src (analyse le contenu du répertoire src)
    
## Tests

Des tests unitaires et fonctionnels sont présents dans le projet dans le répertoire /tests, vous pouvez les lancer avec la commande suivante :

    - php bin/phpunit
    
# Description du projet

## Contexte du projet

Vous venez d’intégrer une startup dont le cœur de métier est une application permettant de gérer ses tâches quotidiennes. L’entreprise vient tout juste d’être montée, et l’application a dû être développée à toute vitesse pour permettre de montrer à de potentiels investisseurs que le concept est viable (on parle de Minimum Viable Product ou MVP).

Le choix du développeur précédent a été d’utiliser le framework PHP Symfony, un framework que vous commencez à bien connaître ! 

Bonne nouvelle ! ToDo & Co a enfin réussi à lever des fonds pour permettre le développement de l’entreprise et surtout de l’application.

Votre rôle ici est donc d’améliorer la qualité de l’application. La qualité est un concept qui englobe bon nombre de sujets : on parle souvent de qualité de code, mais il y a également la qualité perçue par l’utilisateur de l’application ou encore la qualité perçue par les collaborateurs de l’entreprise, et enfin la qualité que vous percevez lorsqu’il vous faut travailler sur le projet.

Ainsi, pour ce dernier projet de spécialisation, vous êtes dans la peau d’un développeur expérimenté en charge des tâches suivantes :

l’implémentation de nouvelles fonctionnalités ;
la correction de quelques anomalies ;
et l’implémentation de tests automatisés.
Il vous est également demandé d’analyser le projet grâce à des outils vous permettant d’avoir une vision d’ensemble de la qualité du code et des différents axes de performance de l’application.

Il ne vous est pas demandé de corriger les points remontés par l’audit de qualité de code et de performance. Cela dit, si le temps vous le permet, ToDo & Co sera ravi que vous réduisiez la dette technique de cette application.

## Description du besoin

### Corrections d'anomalies

#### Une tâche doit être attachée à un utilisateur

Actuellement, lorsqu’une tâche est créée, elle n’est pas rattachée à un utilisateur. Il vous est demandé d’apporter les corrections nécessaires afin qu’automatiquement, à la sauvegarde de la tâche, l’utilisateur authentifié soit rattaché à la tâche nouvellement créée.

Lors de la modification de la tâche, l’auteur ne peut pas être modifié.

Pour les tâches déjà créées, il faut qu’elles soient rattachées à un utilisateur “anonyme”.

#### Choisir un rôle pour un utilisateur

Lors de la création d’un utilisateur, il doit être possible de choisir un rôle pour celui-ci. Les rôles listés sont les suivants :

    - rôle utilisateur (ROLE_USER) ;
    - rôle administrateur (ROLE_ADMIN).
    
Lors de la modification d’un utilisateur, il est également possible de changer le rôle d’un utilisateur.

#### Implémentation de nouvelles fonctionnalités

##### Autorisation

Seuls les utilisateurs ayant le rôle administrateur (ROLE_ADMIN) doivent pouvoir accéder aux pages de gestion des utilisateurs.

Les tâches ne peuvent être supprimées que par les utilisateurs ayant créé les tâches en question.

Les tâches rattachées à l’utilisateur “anonyme” peuvent être supprimées uniquement par les utilisateurs ayant le rôle administrateur (ROLE_ADMIN).

##### Implémentation de tests automatisés

Il vous est demandé d’implémenter les tests automatisés (tests unitaires et fonctionnels) nécessaires pour assurer que le fonctionnement de l’application est bien en adéquation avec les demandes.

Ces tests doivent être implémentés avec PHPUnit ; vous pouvez aussi utiliser Behat pour la partie fonctionnelle.

Vous prévoirez des données de tests afin de pouvoir prouver le fonctionnement dans les cas explicités dans ce document.

Il vous est demandé de fournir un rapport de couverture de code au terme du projet. Il faut que le taux de couverture soit supérieur à 70 %.


### Documentation technique

Il vous est demandé de produire une documentation expliquant comment l’implémentation de l'authentification a été faite. Cette documentation se destine aux prochains développeurs juniors qui rejoindront l’équipe dans quelques semaines. Dans cette documentation, il doit être possible pour un débutant avec le framework Symfony de :

    - comprendre quel(s) fichier(s) il faut modifier et pourquoi ;
    - comment s’opère l’authentification ;
    - et où sont stockés les utilisateurs.
    
S’il vous semble important de mentionner d’autres informations , n’hésitez pas à le faire.

Par ailleurs, vous ouvrez la marche en matière de collaboration à plusieurs sur ce projet. Il vous est également demandé de produire un document expliquant comment devront procéder tous les développeurs souhaitant apporter des modifications au projet.

Ce document devra aussi détailler le processus de qualité à utiliser ainsi que les règles à respecter.

### Audit de qualité du code & performance de l'application

Les fondateurs souhaitent pérenniser le développement de l’application. Cela dit, ils souhaitent dans un premier temps faire un état des lieux de la dette technique de l’application.

Au terme de votre travail effectué sur l’application, il vous est demandé de produire un audit de code sur les deux axes suivants : la qualité de code et la performance.

Bien évidemment, il vous est fortement conseillé d’utiliser des outils vous permettant d’avoir des métriques pour appuyer vos propos.

Concernant l’audit de performance, l’usage de Blackfire est obligatoire. Ce dernier vous permettra de produire des analyses précises et adaptées aux évolutions futures du projet.
