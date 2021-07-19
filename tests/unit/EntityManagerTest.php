<?php

namespace enoffspb\BitrixEntityManager\Tests\Unit;

use PHPUnit\Framework\TestCase;

use enoffspb\BitrixEntityManager\BitrixEntityManager;
use enoffspb\BitrixEntityManager\EntityMetadata;
use enoffspb\BitrixEntityManager\RepositoryInterface;

use enoffspb\BitrixEntityManager\Tests\Entity\Example;

class EntityManagerTest extends BaseTestCase
{
    // Черный список глобальных переменных, которые восстанавливаются после каждого теста
    // @see https://phpunit.readthedocs.io/ru/latest/fixtures.html

    protected $backupGlobalsBlacklist = ['DB'];

    private function createManager(array $config = [], bool $skipEntitiesConfig = false): BitrixEntityManager
    {
        if(!$skipEntitiesConfig) {
            $config['entitiesConfig'] = self::$entitiesConfig;
        }

        $entityManager = new BitrixEntityManager($config);

        return $entityManager;
    }

    public function testCreateManager()
    {
        $entityManager = $this->createManager([
            'autoloadScheme' => false,
        ]);

        $this->assertInstanceOf(BitrixEntityManager::class, $entityManager);
    }

    public function testSetEntitiesConfig()
    {
        $entityManager = $this->createManager([
            'autoloadScheme' => false,
        ], true);

        $entityManager->setEntitiesConfig(self::$entitiesConfig);
        $entitiesConfig = $entityManager->getEntitiesConfig();

        $this->assertEquals(self::$entitiesConfig, $entitiesConfig);
    }

    public function testLoadSchema()
    {
        $entityManager = $this->createManager([
            'autoloadScheme' => false,
        ]);

        $cntTables = $entityManager->loadSchema();
        $this->assertEquals(count(self::$entitiesConfig), $cntTables);
    }

    public function testGetMetadata()
    {
        $entityManager = $this->createManager();

        $exampleMetadata = $entityManager->getMetadata(Example::class);
        $this->assertInstanceOf(EntityMetadata::class, $exampleMetadata);
    }

    public function testSaveEntity()
    {
        $entityManager = $this->createManager();

        $example = new Example();
        $example->name = 'entity name';
        $example->nullable = null;

        $res = $entityManager->save($example);
        $this->assertTrue($res);

        $this->assertNotNull($example->id);
    }

    public function testUpdateEntity()
    {
        $entityManager = $this->createManager();
        $entities = $entityManager->getRepository(Example::class)->getList();
        $entity = $entities[0];
        if(!$entity) {
            throw new \Exception('Example entity is not found.');
        }

        $entity->name = 'new name';
        $res = $entityManager->update($entity);
        $this->assertTrue($res);

        // Повторное обновление сущности без изменений
        $res = $entityManager->update($entity);
        $this->assertTrue($res);

        // Изменение null на not null
        $entity->nullable = 'not null';
        $res = $entityManager->update($entity);
        $this->assertTrue($res);
    }

    public function testUpdateEntityFromRepo()
    {
        $entityManager = $this->createManager();
        $entities = $entityManager->getRepository(Example::class)->getList();
        $entity = $entities[0];
        if(!$entity) {
            throw new \Exception('Example entity is not found.');
        }

        $entity->name = 'another name';
        $res = $entityManager->update($entity);
        $this->assertTrue($res);

        $res = $entityManager->update($entity);
        $this->assertTrue($res);
    }

    public function testDeleteEntity()
    {
        $entityManager = $this->createManager();
        $repository = $entityManager->getRepository(Example::class);
        $entities = $repository->getList();
        $entity = $entities[0];
        if(!$entity) {
            throw new \Exception('Example entity is not found.');
        }

        $id = $entity->id;

        $res = $entityManager->delete($entity);
        $this->assertTrue($res);

        $removedEntity = $repository->getById($id);
        $this->assertNull($removedEntity);
    }

    public function testGetRepository()
    {
        $entityManager = $this->createManager();
        $repository = $entityManager->getRepository(Example::class);

        $this->assertInstanceOf(RepositoryInterface::class, $repository);

        $sameRepository = $entityManager->getRepository(Example::class);
        $this->assertSame($sameRepository, $repository);
    }
}
