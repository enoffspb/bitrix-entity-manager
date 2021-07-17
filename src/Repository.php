<?php

namespace enoffspb\BitrixEntityManager;

use Bitrix\Main\Entity\DataManager;

class Repository implements RepositoryInterface
{
    private DataManager $table;
    private string $tableClass;
    private EntityMetadata $metadata;

    private array $entitiesCache = [];

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
            /**
             * @todo Watch the entity
             */
            $entity = $this->buildEntityFromBxArray($row);

            $result[] = $entity;
        }

        return $result;
    }

    public function getById($id): ?object
    {
        $row = $this->tableClass::getById($id)->fetch();
        if(!$row) {
            return null;
        }

        $entity = $this->buildEntityFromBxArray($row);
        if($entity) {
            $this->attach($entity);
        }

        return $entity;
    }

    public function attach(object $entity)
    {
        $hash = spl_object_hash($entity);
        $this->entitiesCache[$hash] = $entity;
    }

    public function detach(object $entity)
    {
        $hash = spl_object_hash($entity);
        if(isset($this->entitiesCache[$hash])) {
            unset($this->entitiesCache[$hash]);
        }
    }

    protected function buildEntityFromBxArray(array $data): object
    {
        $entity = new $this->metadata->entityClass;
        foreach($data as $k => $v) {
            $attr = $this->metadata->bxNameToAttribute($k);

            /**
             * @todo Приведение типов в соответствии с Column
             */

            $entity->$attr = $v;
        }

        return $entity;
    }
}
