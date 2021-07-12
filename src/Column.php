<?php

namespace enoffspb\BitrixEntityManager;

class Column
{
    const TYPE_INT = 'INT';
    const TYPE_BIGINT = 'BIGINT';
    const TYPE_SMALLINT = 'SMALLINT';
    const TYPE_TINYINT = 'TINYINT';
    const TYPE_VARCHAR = 'VARCHAR';

    public string $name;
    public string $type;
    public string $attribute;
    public ?int $length;
    public bool $nullable;

    public function isInteger(): bool
    {
        $isInteger = false;

        switch($this->type) {
            case self::TYPE_INT:
            case self::TYPE_BIGINT:
            case self::TYPE_SMALLINT:
            case self::TYPE_TINYINT:
                $isInteger = true;
                break;
        }

        return $isInteger;
    }

    /**
     * @param array $describe Array of elements from query "SHOW COLUMNS FROM table"
     */
    public function loadFromDescribe(array $describe)
    {
        $this->name = $describe['Field'];
        $this->attribute = $this->name;

        $type = $describe['Type'];
        $type = strtoupper($type);

        $this->nullable = $describe['Null'] === 'YES';

        // @TODO Retrieve length for other data types
        $length = null;
        if(preg_match('#^(varchar)(?:\((.*?)\))#uis', $type, $out)) {
            $type = $out[1];
            $length = (int) $out[2];
        }
        $this->type = $type;
        $this->length = $length;
    }

    public function loadFromBitrixField(\Bitrix\Main\Entity\Field $field)
    {
        $this->attribute = $field->getName();
        $columnName = $field->getParameter('column_name');

        $this->attribute = strtolower($this->attribute);

        if($columnName !== null) {
            $this->name = $columnName;
        } else {
            $this->name = $this->attribute;
        }

        $this->name = strtolower($this->name);
        $this->attribute = strtolower($this->attribute);

        $this->nullable = !$field->getParameter('required');
    }

}
