<?php
use PHPUnit\Framework\TestCase;

use enoffspb\BitrixEntityManager\BitrixEntityManager;
use enoffspb\BitrixEntityManager\EntityMetadata;
use enoffspb\BitrixEntityManager\RepositoryInterface;

/**
 * @TODO Код перенесен из другого проекта, необходимо дописать тесты и README по запуску.
 * @TODO Добавить tests/src в автозагрузку
 */

use enoffspb\BitrixEntityManager\Tests\Entity\Example;

class EntityManagerTest extends TestCase
{
    // Черный список глобальных переменных, которые восстанавливаются после каждого теста
    // @see https://phpunit.readthedocs.io/ru/latest/fixtures.html

    protected $backupGlobalsBlacklist = ['DB'];

    private function createManager(array $config = []): BitrixEntityManager
    {
        $entitiesConfig = [
            Example::class => [
                'tableName' => 'example',
            ],
        ];

        $config['entitiesConfig'] = $entitiesConfig;

        $entityManager = new BitrixEntityManager($config);
        $entityManager->setEntitiesConfig($entitiesConfig);

        return $entityManager;
    }

    // Создание EntityManager
    public function testCreateManager()
    {
        $entityManager = $this->createManager([
            'autoloadScheme' => false,
        ]);

        $this->assertInstanceOf(BitrixEntityManager::class, $entityManager);
    }

    public function testLoadSchema()
    {
        $entityManager = $this->createManager([
            'autoloadScheme' => false,
        ]);

        $cntTables = $entityManager->loadSchema();
        $this->assertGreaterThan(0, $cntTables);
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
