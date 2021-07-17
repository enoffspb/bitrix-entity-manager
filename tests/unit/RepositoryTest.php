<?php

namespace enoffspb\BitrixEntityManager\Tests\Unit;

use enoffspb\BitrixEntityManager\BitrixEntityManager;
use enoffspb\BitrixEntityManager\Tests\Entity\Example;

class RepositoryTest extends BaseTestCase
{
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

//        $entityId = 1;
//        $nonExistsEntity = $repository->getById($entityId);
//        $this->assertNull($nonExistsEntity);

        $entityId = -1;
        $nonExistsEntity = $repository->getById($entityId);
        $this->assertNull($nonExistsEntity);
    }
}
