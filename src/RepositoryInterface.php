<?php

namespace enoffspb\BitrixEntityManager;

interface RepositoryInterface
{
    public function getById($id): ?object;

    /**
     * @param $criteria see getList() from D7 core
     * @return array|null
     */
    public function getList(array $criteria = []): ?array;

    public function attach(object $entity): void;
    public function detach(object $entity): void;
}
