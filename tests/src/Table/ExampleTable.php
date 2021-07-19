<?php

namespace enoffspb\BitrixEntityManager\Tests\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

class ExampleTable extends DataManager
{
    public static function getTableName()
    {
        return '_enoffspb_bem_test_entity_example';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),
            (new StringField('NAME'))
                ->configureRequired(),
            (new StringField('NULLABLE'))
        ];
    }
}
