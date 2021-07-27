<?php

namespace Cda0521Framework\Database;

use Cda0521Framework\Database\Sql\SqlDatabaseHandler;

/**
 * Récupére tous les éléments de la table associée à la classe appelante sous forme d'objets
 */
class AbstractModel

{
    static public function FindAllInTable(string $tableName, string $classname)
    {
        // Récupère tous les enregistrements de la table concernée
        $data = SqlDatabaseHandler::fetchAll($tableName);
        // Pour chaque enregistrement
        foreach ($data as $item) {
            // Construit un objet de la classe concernée
            // Dans la mesure où chaque table posséde un nombre de colonnes différent
            // (et donc que chaque classe attend un nombre de propriétés différent),
            // utilise l'opérateur ... pour "déplier" la liste des données de l'enregistrement
            // afin de les passer comme des paramètres séparés au constructeur de la classe
            dump($item);
            $result[] = new $classname(...$item);
        }
        return $result;
    }
    /**
     * Récupère un élément de la table associée à la classe appelante en fonction de son identifiant en base de données sous forme d'objet
     *
     * @param integer $id Identifiant en base de données de l'élément désiré
     */

    static public function FindByIdInTable(int $id, string $tableName, string $classname)
    {
        $item = SqlDatabaseHandler::fetchById($tableName, $id);
        if (is_null($item)) {
            return null;
            //Si item qu'on a récupéré est null, on renvoie null
        }
        return new $classname(...$item);
    }
}
