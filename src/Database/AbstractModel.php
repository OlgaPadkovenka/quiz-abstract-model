<?php

namespace Cda0521Framework\Database;

use Cda0521Framework\Database\Sql\SqlDatabaseHandler;
use Cda0521Framework\Database\Sql\Table;
use ReflectionClass;

/**
 * Récupére tous les éléments de la table associée à la classe appelante sous forme d'objets
 */
class AbstractModel

{
    static public function findAll()
    {
        // Récupère le nom de la classe qui a appelé cette méthode
        $className = get_called_class();

        // Récupère tous les enregistrements de la table concernée
        $data = SqlDatabaseHandler::fetchAll(static::getTableName());
        //$className::$tableName
        // Pour chaque enregistrement
        foreach ($data as $item) {
            // Construit un objet de la classe concernée
            // Dans la mesure où chaque table posséde un nombre de colonnes différent
            // (et donc que chaque classe attend un nombre de propriétés différent),
            // utilise l'opérateur ... pour "déplier" la liste des données de l'enregistrement
            // afin de les passer comme des paramètres séparés au constructeur de la classe
            dump($item);
            $result[] = new $className(...$item);
        }
        return $result;
    }
    /**
     * Récupère un élément de la table associée à la classe appelante en fonction de son identifiant en base de données sous forme d'objet
     *
     * @param integer $id Identifiant en base de données de l'élément désiré
     */

    static public function findById(int $id)
    {
        // Récupère le nom de la classe qui a appelé cette méthode
        $className = get_called_class();

        $item = SqlDatabaseHandler::fetchById(static::getTableName(), $id);
        if (is_null($item)) {
            return null;
            //Si item qu'on a récupéré est null, on renvoie null
        }
        return new $className(...$item);
    }

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
}
