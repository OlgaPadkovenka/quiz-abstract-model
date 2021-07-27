# M2i CDA 05/21 PHP Framework

> ⚠️ This is a PHP MVC micro-framework created for educational purposes.

## Installation

### Prérequis

- Une installation de PHP: https://www.php.net/downloads.
- Une installation de Composer: https://getcomposer.org/download/.
- Une installation d'un système de base de données (MySQL, PostgreSQL…).

### Lancer une application exemple

- Depuis le dossier d'une application exemple (par exemple: `examples/quiz-mvc`), installer les dépendances:

> `composer install`

- Puis, lancer le serveur local de PHP:

> `php -S localhost:8000`

- Ajouter un fichier `database.json` dans ce dossier (sur le modèle de `database.example.json`) afin de spécifier l'adresse de votre serveur de base de données, le nom de la base de données, ainsi que les identifiants permettant de s'y connecter.

L'application est alors accessible à partir de http://localhost:8000.


Nous avons réussi à créer un service indépendant qui centralise toutes les interactions à la base de données. Cependant, nous avons toujours beaucoup de code dupliqué entre les différentes modèles. Il faudrait trouver un moyen d'écrire un modéle générique contenant le code des opérations les plus courantes, de sorte que celui-ci puisse être réutilisé par chaque modèle particulier.



- Écrire une superclasse **AbstractModel** qui contienne les méthodes actuellement codées dans les classes particulières (findAll, findById…). Tous les modèles particuliers doivent étendre cette classe et faire appel à son code, sans le réimplémenter elles-mêmes.



Liens utiles:



- Les différents modes de `fetch`: https://www.php.net/manual/fr/pdostatement.fetch.php

- Les fonctions variadiques: https://www.phptutorial.net/php-tutorial/php-variadic-functions/

- Les attributes: https://www.php.net/manual/fr/language.attributes.overview.php

- Les classes abstraites: https://www.php.net/manual/fr/language.oop5.abstract.php