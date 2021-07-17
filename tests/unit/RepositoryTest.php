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

        $sameEntity = $repository->getById($entityId);
        $this->assertSame($sameEntity, $entity);

        $entityId = -1;
        $nonExistsEntity = $repository->getById($entityId);
        $this->assertNull($nonExistsEntity);
    }

    public function testGetList()
    {
        $entityManager = $this->createManager();
        $repository = $entityManager->getRepository(Example::class);

        $entities = $repository->getList();
        $this->assertEquals(count(self::$fixtures), count($entities));

        $firstEntity = $entities[0];

        $entities = $repository->getList();
        $secondEntity = $entities[0];

        $this->assertSame($firstEntity, $secondEntity);
    }

    private static $fixtures = [
        [
            'ID' => 1,
            'NAME' => 'entity name'
        ]
    ];

    private static function loadFixtures()
    {
        $examples = self::$fixtures;

        foreach($examples as $fields) {
            $insertedId = self::$connection->add(ExampleTable::getTableName(), $fields);
            if(!$insertedId) {
                throw new \Exception('Cannot save example fields');
            }
        }
    }
}
