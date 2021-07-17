<?php

namespace enoffspb\BitrixEntityManager\Tests\Unit;

use PHPUnit\Framework\TestCase;

use enoffspb\BitrixEntityManager\BitrixEntityManager;
use enoffspb\BitrixEntityManager\EntityMetadata;
use enoffspb\BitrixEntityManager\RepositoryInterface;

use enoffspb\BitrixEntityManager\Tests\Entity\Example;
use enoffspb\BitrixEntityManager\Tests\Table\ExampleTable;

/**
 * @TODO Код перенесен из другого проекта, необходимо дописать тесты и README по запуску.
 * @TODO Добавить tests/src в автозагрузку
 */

class EntityManagerTest extends BaseTestCase
{
    // Черный список глобальных переменных, которые восстанавливаются после каждого теста
    // @see https://phpunit.readthedocs.io/ru/latest/fixtures.html

    protected $backupGlobalsBlacklist = ['DB'];

    private $entitiesConfig = [
        Example::class => [
            'tableClass' => ExampleTable::class,
        ]
    ];

    private function createManager(array $config = [], bool $skipEntitiesConfig = false): BitrixEntityManager
    {
        if(!$skipEntitiesConfig) {
            $config['entitiesConfig'] = $this->entitiesConfig;
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

        $entityManager->setEntitiesConfig($this->entitiesConfig);
        $entitiesConfig = $entityManager->getEntitiesConfig();

        $this->assertEquals($this->entitiesConfig, $entitiesConfig);
    }

    public function testLoadSchema()
    {
        $entityManager = $this->createManager([
            'autoloadScheme' => false,
        ]);

        $cntTables = $entityManager->loadSchema();
        $this->assertEquals(count($this->entitiesConfig), $cntTables);
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

        $res = $entityManager->save($example);
        $this->assertTrue($res);

        $this->assertNotNull($example->id);
    }

    public function testGetRepository()
    {
        $entityManager = $this->createManager();
        $repository = $entityManager->getRepository(Example::class);

        $this->assertInstanceOf(RepositoryInterface::class, $repository);
    }

    public function testRepository()
    {
        $entityManager = $this->createManager();
        $repository = $entityManager->getRepository(Example::class);

        $entities = $repository->getList();

        $this->assertTrue(count($entities) > 0);
    }
}
