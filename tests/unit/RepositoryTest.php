<?php

namespace enoffspb\BitrixEntityManager\Tests\Unit;

use enoffspb\BitrixEntityManager\BitrixEntityManager;
use enoffspb\BitrixEntityManager\Tests\Entity\Example;
use enoffspb\BitrixEntityManager\Tests\Table\ExampleTable;

class RepositoryTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::loadFixtures();
    }

    private function createManager(array $config = []): BitrixEntityManager
    {
        if(!isset($config['entitiesConfig'])) {
            $config['entitiesConfig'] = self::$entitiesConfig;
        }

        $entityManager = new BitrixEntityManager($config);

        return $entityManager;
    }

    public function testGetById()
    {
        $entityManager = $this->createManager();
        $repository = $entityManager->getRepository(Example::class);

        $entityId = 1;
        $entity = $repository->getById($entityId);
        $this->assertInstanceOf(Example::class, $entity);

        $this->assertEquals($entity->id, 1);
        $this->assertEquals($entity->name, 'entity name');

        $entityId = -1;
        $nonExistsEntity = $repository->getById($entityId);
        $this->assertNull($nonExistsEntity);
    }

    private static function loadFixtures()
    {
        $examples = [
            [
                'ID' => 1,
                'NAME' => 'entity name'
            ]
        ];

        foreach($examples as $fields) {
            $insertId = self::$connection->add(ExampleTable::getTableName(), $fields);
            if(!$insertId) {
                throw new \Exception('Cannot save example fields');
            }
        }
    }
}
