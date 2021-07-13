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

            $columnName = $field->getParameter('column_name');
            $attribute = $field->getName();

            if(!$columnName) {
                $columnName = $attribute;
            }

            $attribute = $this->bxNameToAttribute($attribute);

            $column->name = $columnName;
            $column->attribute = $attribute;

            $column->nullable = !$field->getParameter('required');

            $this->mapping[$column->name] = $column;
        }
    }

    public function bxNameToAttribute(string $str): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($str)))));
    }

    public function attributeToBxName($str): string
    {
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }
}
