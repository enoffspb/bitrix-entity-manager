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

        $pkField = $this->metadata->primaryKey;

        $result = [];
        while($row = $res->fetch()) {
            $entity = $this->buildEntityFromBxArray($row);

            $pk = $entity->$pkField;
            if(isset($this->entitiesCache[$pk])) {
                $this->mergeEntities($this->entitiesCache[$pk], $entity);
                $entity = $this->entitiesCache[$pk];
                $this->storeValues($entity);
            } else {
                $this->attach($entity);
            }

            $result[] = $entity;
        }

        return $result;
    }

    public function getById($id): ?object
    {
        if(isset($this->entitiesCache[$id])) {
            return $this->entitiesCache[$id];
        }

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

    public function attach(object $entity): void
    {
        $pk = $this->metadata->primaryKey;

        $this->entitiesCache[$entity->$pk] = $entity;

        $this->storeValues($entity);
    }

    public function detach(object $entity): void
    {
        $pk = $this->metadata->primaryKey;

        if(isset($this->entitiesCache[$entity->$pk])) {
            unset($this->entitiesCache[$entity->$pk]);
        }

        $this->clearStoredValues($entity);
    }

    private array $storedValues = [];
    public function storeValues(object $entity): void
    {
        $columns = $this->metadata->getMapping();
        $pkField = $this->metadata->primaryKey;
        $pk = $entity->$pkField;

        $values = [];
        foreach($columns as $column) {
            $values[$column->attribute] = $entity->{$column->attribute};
        }
        $this->storedValues[$pk] = $values;
    }

    public function getStoredValues(object $entity): ?array
    {
        $pkField = $this->metadata->primaryKey;
        $pk = $entity->$pkField;

        if(!isset($this->storedValues[$pk])) {
            return null;
        }

        return $this->storedValues[$pk];
    }

    public function clearStoredValues(object $entity): void
    {
        $pkField = $this->metadata->primaryKey;
        $pk = $entity->$pkField;

        if(isset($this->storedValues[$pk])) {
            unset($this->storedValues[$pk]);
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

    protected function mergeEntities(object &$object, object $newestObject)
    {
        $columns = $this->metadata->getMapping();
        foreach($columns as $column) {
            $attr = $column->attribute;
            $object->$attr = $newestObject->$attr;
        }
    }
}
