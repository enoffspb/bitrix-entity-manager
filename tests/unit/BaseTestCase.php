<?php

namespace enoffspb\BitrixEntityManager\Tests\Unit;

use enoffspb\BitrixEntityManager\Tests\Table\ExampleTable;
use Bitrix\Main\Application;

abstract class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    private $tables = [
        ExampleTable::class
    ];

    private \Bitrix\Main\DB\Connection $connection;

    public function setUp(): void
    {
        parent::setUp();

        $this->connection = Application::getConnection();

        $this->dropTables();
        $this->createTables();
    }

    public function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        $this->dropTables();
    }

    private function createTables()
    {
        foreach($this->tables as $table) {
            $sqlQueries = $table::getEntity()->compileDbTableStructureDump();

            foreach($sqlQueries as $sql) {
                $this->connection->queryExecute($sql);
            }
        }
    }

    private function dropTables()
    {
        foreach($this->tables as $table) {
            $tableName = $table::getTableName();

            $sql = "DROP TABLE IF EXISTS `$tableName`";
            $this->connection->queryExecute($sql);
        }
    }
}
