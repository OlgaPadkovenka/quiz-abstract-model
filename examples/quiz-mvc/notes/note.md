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

26. 