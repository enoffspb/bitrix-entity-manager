<?php

namespace enoffspb\BitrixEntityManager;

class EntityMetadata
{
    public string $entityClass;
    public string $tableName;
    public string $tableClass;
    public string $primaryKey = 'id';

    private array $mapping = [];

    /**
     * @return Column[]
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    public function createColumnsFromDescribe(array $describeRows)
    {
        foreach($describeRows as $row) {
            $column = new Column();
            $column->loadFromDescribe($row);
            $this->mapping[$column->name] = $column;
        }
    }

    public function createColumnsFromTableClass($tableClass)
    {
        $this->tableName = $tableClass::getTableName();
        $bxMapping =  $tableClass::getMap();
        foreach($bxMapping as $field) {
            $column = new Column();
            $column->loadFromBitrixField($field);
            $this->mapping[$column->name] = $column;
        }
    }
}
