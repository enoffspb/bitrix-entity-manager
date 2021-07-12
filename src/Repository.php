<?php

namespace enoffspb\BitrixEntityManager;

use Bitrix\Main\Entity\DataManager;

class Repository implements RepositoryInterface
{
    private DataManager $table;
    private string $tableClass;
    private EntityMetadata $metadata;

    public function __construct(EntityMetadata $metadata)
    {
        $this->metadata = $metadata;
        $this->tableClass = $metadata->tableClass;

        /**
         * @TODO можно удалить код. Были попытки создать виртуальные классы EntityTable
         */

//        $this->table = new class($tableName) extends DataManager {
//
//            private static $tableName;
//
//            public function __construct($tableName)
//            {
//                self::$tableName = $tableName;
//            }
//
//            public static function getTableName()
//            {
//                return self::$tableName;
//            }
//        };
    }

    public function getList(array $criteria = []): ?array
    {
        $res = $this->tableClass::getList($criteria);

        $result = [];
        while($row = $res->fetch()) {
            foreach($row as $k => $v) {
                $k = strtolower($k);

                /**
                 * @todo Приведение типов в соответствии с Column
                 */

                $entity = new $this->metadata->entityClass;
                $entity->$k = $v;

                /**
                 * @todo Запомнить Entity
                 */

                $result[] = $entity;
            }
        }

        return $result;
    }

    public function getById($id): ?object
    {

    }
}
