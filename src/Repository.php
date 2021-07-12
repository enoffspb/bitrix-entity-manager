<?php

namespace enoffspb\BitrixEntityManager;

use Bitrix\Main\Entity\DataManager;

class Repository implements RepositoryInterface
{
    private DataManager $table;

    public function __construct(EntityMetadata $metadata)
    {
        $tableName = $metadata->tableName;

        $this->table = new class($tableName) extends DataManager {

            private static $tableName;

            public function __construct($tableName)
            {
                self::$tableName = $tableName;
            }

            public static function getTableName()
            {
                return self::$tableName;
            }
        };
    }

    public function getList(array $criteria = []): ?array
    {
        $res = $this->table::getList($criteria);
        while($row = $res->fetchRaw()) {
            var_dump($res);
        }
    }

    public function getById($id): ?object
    {

    }
}
