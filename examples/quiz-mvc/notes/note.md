1. Je crée le fichier AbstractModel.php dans src/Database
2. Je voudrais créer deux méthodes ans la classe AbstractModel qui me permettront de chercher les question et les réponses. 

namespace Cda0521Framework\Database;

class AbstractModel
{
    static public function FindAllInTable()
    {
    }

    static public function FindByIdInTable(int $id)
    {
    }
}

3. Le problème: je dois savoir le nom de classe et le nom de la table.
Je pourrais préciser le nom de la table et le nom de la classe en les passant en paramètres.

class AbstractModel
{
    static public function FindAllInTable(string $tableName, string $classname)
    {
    }

    static public function FindByIdInTable(int $id, string $tableName, string ////$classname)
    {
    }
}

4. J'importe SqlDatabaseHandler dans AbstractModel.php

use Cda0521Framework\Database\Sql\SqlDatabaseHandler;

5. Je construis la méthode FindAllInTable()
  static public function FindAll(string $tableName, string $classname)
    {
        $data = SqlDatabaseHandler::fetchAll($tableName);
        foreach ($data as $item) {
            $result[] = new $classname();
        }
        return $result;
    }

6. Answer est un type particulier d'AbstractModel. Donc, Answer hérite de toutes les caractèristiques d'AbstractModel. J'ajoute à la classe Answer extends AbstractModel.

class Answer extends AbstractModel

7. Je réécris la méthode findAll de l'Answer. J'appelle la méthode FindAllInTable du parent de l'Answer (ce qui est AbstractModel) en précisant le nom de la table et le nom de la classe.

  static public function findAll(): array
    {
        return parent::findAll('answer', Answer::class);
    }

8. Si je fais dd(Question::class); dans l'index, ca me renvoie "App\Model\Question". 

9. Answer::class ça produit bien la classe. Si dans Answer.php j'écris "return parent::findAll('answer', Answer::class);", ca m'appelle la méthode findAll de l'AbstractModel en lui passant le nom de la table et le nom de la classe.

10. Si je fais dd(Answer::class); dans l'index, ca m'appelle la méthode findAll() de l'Answer qui va lui-même appeler la méthode findAll() de l'AbstractModel

P.S. J'ai renommé findAll() de l'AbstractModel à findAllInTable()

11. Si je fais dans l'index dd(Answer::findAll());, je peux voir les réponses vides.
^ array:20 [▼
  0 => App\Model\Answer {#4 ▼
    -id: null
    -text: ""
    -questionId: null
  }
  1 => App\Model\Answer {#5 ▶}
  2 => App\Model\Answer {#6 ▶}
  3 => App\Model\Answer {#7 ▶}
  4 => App\Model\Answer {#8 ▶}
  5 => App\Model\Answer {#9 ▶}
  6 => App\Model\Answer {#10 ▶}
  7 => App\Model\Answer {#11 ▶}
  8 => App\Model\Answer {#12 ▶}
  9 => App\Model\Answer {#13 ▶}
  10 => App\Model\Answer {#14 ▶}
  11 => App\Model\Answer {#15 ▶}
  12 => App\Model\Answer {#16 ▶}
  13 => App\Model\Answer {#17 ▶}
  14 => App\Model\Answer {#18 ▶}
  15 => App\Model\Answer {#19 ▶}
  16 => App\Model\Answer {#20 ▶}
  17 => App\Model\Answer {#21 ▶}
  18 => App\Model\Answer {#22 ▶}
  19 => App\Model\Answer {#23 ▶}
]

12. Le problème est que le constructeur de l'Answer attend 3 paramètres, le constructeur de la Question attend 4 paramètres. Je dois trouver la manière d'écrire le code qui s'adaapte à toutes les situations.

13. Je dois utiliser une fonction variadique qui accèpte le nombre variable de paramètres. Il suffit d'avoir un tableau. $item de la méthode FindAllInTable est un tableau.

 static public function FindAllInTable(string $tableName, string $classname)
    {
        $data = SqlDatabaseHandler::fetchAll($tableName);
        foreach ($data as $item) {
            $result[] = new $classname();
        }
        return $result;
    }

14. Si j'ajoute dd($item); dans la méthode FindAllInTable

static public function FindAllInTable(string $tableName, string $classname)
    {
        $data = SqlDatabaseHandler::fetchAll($tableName);
        foreach ($data as $item) {
            dd($item);
            $result[] = new $classname();
        }
        return $result;
    }

    Je peux voir le résultat qui est la première question:

^ array:8 [▼
  "id" => "1"
  0 => "1"
  "text" => "Combien de joueurs y a-t-il dans une équipe de football?"
  1 => "Combien de joueurs y a-t-il dans une équipe de football?"
  "right_answer_id" => "4"
  2 => "4"
  "rank" => "1"
  3 => "1"
]

P.S. Ce résultat, si j'ai un dd(Question::findAll());

15. L'idée de la fonction variadique est de dire: je prends le contenu de ce tableau ($item), et je vais le distribuer par le principe que chaque élément du tableau est un paramètre.

16. Le problème est le suivant: j'ai un tableau de 8 paramètres alors que la table question contient 4. Il en y a 8, parce que il y a des doublons. Il y a toutes les colonnes indéxée par un numéro de colonne et par le nom de la colonne.

17. Je voudrais laisser que le tableau indexé par le numéro de colonnes. C'est le PDO qui produit un tableau associatif avec des doublons.

18. Dans SqlDatabaseHandler.php, je cherche la méthode fetchAll:

  static public function fetchAll(string $tableName): array
    {
        $statement = self::getInstance()->pdo->query('SELECT * FROM `' . $tableName . '`');
        return $statement->fetchAll();
    }

    Je lui précise:  return $statement->fetchAll(PDO::FETCH_NUM);

    Ca me renvoie le tableau indexé que par le numéro de la colonne:

    0 => "1"
  1 => "Combien de joueurs y a-t-il dans une équipe de football?"
  2 => "4"
  3 => "1"
]

19. Je je veux voir les résultats, je peux faire dump($item); dans l'AbstractModel qui me permet de voir toutes les questions.

  static public function FindAllInTable(string $tableName, string $classname)
    {
        $data = SqlDatabaseHandler::fetchAll($tableName);
        foreach ($data as $item) {
            dump($item);
            $result[] = new $classname();
        }
        return $result;
    }

20. Avec la fonction variadique, je vais pouvoir distribuer le contenu du tableau dans les paramètres d'une fonction. 
...$item

   static public function FindAllInTable(string $tableName, string $classname)
    {
        $data = SqlDatabaseHandler::fetchAll($tableName);
        foreach ($data as $item) {
            dump($item);
            $result[] = new $classname(...$item);
        }
        return $result;
    }

Ca veut dire que cette
ligne $result[] = new $classname(...$item);
s'adapte à n'importe quelle classe qui est passée en paramètre et au niveau des paramètres du constructeur elle s'adapte à n'importe quel tableau $item. 

21. Si je fais dd(Answer::findAll()); à index.php, je peux voir toutes les réponses bien remplies.

22. Je crée la méthode FindByIdInTable dans le fichier AbstractModel.php

   static public function FindByIdInTable(int $id, string $tableName, string $classname)
    {
        $item = SqlDatabaseHandler::fetchById($tableName, $id);
        if (is_null($item)) {
            return null;
            //Si item qu'on a récupéré est null, on renvoie null
        }
        return new $classname(...$item);
    }

23. Je vais dans SqlDatabaseHandler.php où je dois adapter la fonction fetchById.
Quand on demande de cherche un item en fonction de son id, il renvoie un tableau associatif indéxé par le numéro de colonne et indexé par le nom de la colonne. 
 return $statement->fetchAll();

 Je change à return $statement->fetchAll(PDO::FETCH_NUM);
 pour que ca me renvoie les données indexées par le mumero de colonne.

24. J'adapte findById() de la Question et de la Répopnse(return parent::FindByIdInTable($id, 'question', Question::class);).

 static public function findById(int $id): ?Question
    {
        $item = SqlDatabaseHandler::fetchById('question', $id);
        if (is_null($item)) {
            return $item;
        }
        return parent::FindByIdInTable($id, 'question', Question::class);
    }

      static public function findById(int $id): ?Answer
    {
        $item = SqlDatabaseHandler::fetchById('answer', $id);
        if (is_null($item)) {
            return $item;
        }
        return parent::FindByIdInTable($id, 'answer', Answer::class);
    }

25. Si dans l'index, je fais dd(Question::findById(1));, cela me cherche la question 1.
^ App\Model\Question {#4 ▼
  -id: 1
  -text: "Combien de joueurs y a-t-il dans une équipe de football?"
  -rank: 1
  -rightAnswerId: 4
}

J'ai réduis mon appel findAll et findById à une seule ligne.

26. Je remarque que les méthodes findAll et FindById de la classe Réponse et de la classe Question sont ressembles. 

27. Je crée une propriété statique de la classe qui va appartenir que à cette classe.
class Question extends AbstractModel
{
static string $tableName = 'question';

class Answer extends AbstractModel
{
    static string $tableName = 'answer';

28. Je voudrais récupérer le nom de la classe sans le passer en paramètres de la méthode findAllInTable dans AbstractModel.php. Pour le faire, je dois récuperer le nom de la classe qui a appelé cette méthode.

29. Je crée une variable $className qui 
get_called_class() - Retourne le nom de la classe depuis laquelle une méthode statique a été appelée, tel que le Late State Binding le détermine.

  static public function FindAllInTable(string $tableName, string $classname)
    {
        // Récupère le nom de la classe qui a appelé cette méthode
        $className = get_called_class();

30. J'ajoute className à mon fetch $data = SqlDatabaseHandler::fetchAll($tableName);
 $data = SqlDatabaseHandler::fetchAll($className::$tableName);

31. Je supprime les paramètres de la méthode findAll dans AbstractModel et je peux changer son nom de findAllInTable à findAll.

    static public function findAll()
    {
        // Récupère le nom de la classe qui a appelé cette méthode
        $className = get_called_class();
        dd($className);

32. Pour voir le résultat de get_called_class(), je peux faire 
dd($className);
et à l'index dd(Answer::findAll());

Ca doit me renvoier "App\Model\Answer"

Mais cela me produit une erreur parce que je dois supprimer les méthodes findAll() et findById() dans l'Answer, car l'Answer hérite findAll() de la class AbstractModel.

Après avoir supprimé, je peux voir "App\Model\Answer". 

P.S. A l'index j'ai fait dd(Answer::findAll());, Answer n'a pas de findAll, mais Answer est l'enfant de l'AbstractModel qui a findAll(), et Answer hérite cette méthode.

Je supprime également les méthodes findAll et findById de la Question.

33. J'adapte la méthode findById de l'AbstractModel.

  static public function findById(int $id)
    {
        // Récupère le nom de la classe qui a appelé cette méthode
        $className = get_called_class();

        $item = SqlDatabaseHandler::fetchById($className::$tableName, $id);
        if (is_null($item)) {
            return null;
            //Si item qu'on a récupéré est null, on renvoie null
        }
        return new $className(...$item);
    }

34. Pour l'instant $tableName est la propriété statique dans la Classe.
Si tableName est la propriété de la Question, caveut dir que c'est une propriété qui est modifiable.
On peut transformer en const.
Un autre problème est que je peux écrire un model et il n'y a pas de const tableName.
L'idée est d'écrire le coe de telle manière pour dire que j'ai besoin de la propriété   static string $tableName = 'question';

35. L'intérieur de l'AbstractModel, je pourrais écrire une méthode qui sera protected, parce qu'elle servira que à AbstractModel que je pourrais appeler getTableName().
Cette méthode getTableName() pourrait renvoyer la propriété $tableName de la classe appelante.

 static protected function getTableName(): string
    {
        return static::$tableName;
    }

36. Si $tableName n'est pas défini, je retourne une erreur.

  static protected function getTableName(): string
    {
        if(!isset(static::$tableName)){
            throw new \Exception('Models must have a $tableName static proerty.')
        } 
        return static::$tableName;
    }

37. Maintenant, je peux changer cette ligne
 // Récupère tous les enregistrements de la table concernée
        $data = SqlDatabaseHandler::fetchAll($className::$tableName);

à cela

// Récupère tous les enregistrements de la table concernée
        $data = SqlDatabaseHandler::fetchAll(static::getTableName());

Et pareil pour 
$item = SqlDatabaseHandler::fetchById($className::$tableName, $id);
que je change à
$item = SqlDatabaseHandler::fetchById(static::getTableName(), $id);

38. Maintenant, si j'enlève la propriété tableName de la classe.
 static string $tableName = 'question';
 Cela me produit une erreur que j'ai écrit dans la méthode getTableName() de l'AbstractModel;

//ATTRIBUTE
39. Je voudrais changer static string $tableName = 'question'; par un attribut de la classe.
Les attibuts offrent une possibilité de rajouter des méta-données strusturées et lisibles par la machine.
J'ai la classe, mais en plus de son contenu, je peux ajouter des méta-données, des informations qui vont me permettre de savoir comment interpréter certaine parties des classes.
Cela me permet de donner les propriétés spéciales à une classe.

En écrivant ceci, je donne une attribut à la classe Answer.
#[Table('answer')]

Quand je donne cet atribut à une classe, ca permet de savoir quel est la table dans la basse de donées associé à cette classe.

40. Dans le dossier Sql, je crée un fichier Table.php où je crée la classe Table qui aura la propriété name.
Elle aura le consructeur avec name dans les paramètres, et qui va le stocker dans le propriété l'objet.
J'ajoute un get pour pouvoir accéder à cette classe par la suite.

<?php

namespace Cda0521Framework\Database\Sql;

class Table
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }


    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }
}

41. Mais comment on accède à l'attribut?
La classe Answer a un attrubut, mais comment y accéder?
Le principe de la Reflection est que ca permet d'écrire du code qui permet d'analyser le code.
ReflectionObject rapporte l'info sur un object.
ReflectionClass rapport l'info sur une classe.

dd(new ReflectionClass(Answer::class));
Cela me donne l'objet du type ReflectionClass qui contient l'info sur la classe consernée (y compris les méthodes, les propriétés et les attributs).

42. Dans AbstractModel.php j'ai la méthode getTableName() qui me permet de chercher la valeur de la propriété statique tableName.

Je change la méthode, et j'importe ReflectionClass:
use ReflectionClass;

    static protected function getTableName(): string
    {
        // if (!isset(static::$tableName)) {
        //     throw new \Exception('Models must have a $tableName static proerty.');
        // }
        // return static::$tableName;

        $reflection = new ReflectionClass(get_called_class());
    }

Si je suis arrivé à la méthode getTableName(), cela veut dire que cette méthode a été appelé par Question ou Answer.
Donc, je crée un objet ReflectionClass qui va me renvoyer des informations sur la classe appelante.

Pour voir les attrubuts de la classe applante, j'ajoute un dd à AbstractModel:
  static protected function getTableName(): string
    {
        // Crée un objet permettant d'accéder aux propriétés de la classe appelante
        $reflection = new ReflectionClass(get_called_class());

        dd($reflection->getAttributes());
    }

    A l'index j'ajoute Answer::findAll();

    Je vois le résultat suivant: 

    ^ array:1 [▼
  0 => ReflectionAttribute {#2 ▼
    name: "App\Model\Table"
    arguments: array:1 [▼
      0 => "answer"
    ]
  }
]

43. Si je regarde ReflectionAttribute
newInstant ca sert à instancier l'attribut représanté par la classe et l'argument de ReflectionAttribute.
newInstant est censé d'envoyer l'attribut sous form d'objet.

  static protected function getTableName(): string
    {
        // Crée un objet permettant d'accéder aux propriétés de la classe appelante
        $reflection = new ReflectionClass(get_called_class());

        // Pour chaque attribut associé à la classe
        foreach ($reflection->getAttributes() as $reflectionAttribute) {
            dump($reflectionAttribute->newInstance());
        }
        die();
    }

Chaque classe est représentee un attribut doit avoir un attrubut disant que c'est un attrubet #[Attribute]
Je vérifie si la classe Table possè de #[Attribute]. Et je l'importe avec
 use Attribute;

 J'importe Table dans l'AbstractModel et dans Answer et Question.
  use Cda0521Framework\Database\Sql\Table;

Le dump m'envoie

^ Cda0521Framework\Database\Sql\Table {#4 ▼
  -name: "question"
}

a) le faite d'écrire l'attribut de la question #[Table('question')]

b) Une fois j'ai récupéré la classe, je vais chercher dans les attrubuts 
$reflection = new ReflectionClass(get_called_class());

c) et cet attribut je l'instansie
$reflectionAttribute->newInstance()
Cela me fait la propriété table avec sa propriété question.
Cela m'execute #[Table('question')] comme c'était new Question.

44. Je fais la boucle et si j'arrive au bout de la boucle, cela veut dire qu'il n'y a pas d'attribut table, dans ce cas-là je mets une erreur.

    static protected function getTableName(): string
    {
        // Crée un objet permettant d'accéder aux propriétés de la classe appelante
        $reflection = new ReflectionClass(get_called_class());

        // Pour chaque attribut associé à la classe
        foreach ($reflection->getAttributes() as $reflectionAttribute) {

            // Instancie l'attribut tel qu'il est écrit dans le code de la classe
            $attribute = $reflectionAttribute->newInstance();

            // S'il s'agit d'un attribut "table"
            if ($attribute instanceof Table) {
                // Renvoie le nom de l'attribut
                return $attribute->getName();
            }
        }
        // Si la boucle s'est terminée sans avoir trouvé d'attribut "table", envoie une erreur
        throw new \Exception('Models must have a Table attribute.');
    }

