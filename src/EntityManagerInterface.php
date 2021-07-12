<?php

namespace enoffspb\BitrixEntityManager;

interface EntityManagerInterface
{
    public function getRepository($entityClass);

    public function save(object $entity): bool;
    public function update(object $entity): bool;
    public function delete(object $entity): bool;
}
