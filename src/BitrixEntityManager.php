<?php

namespace enoffspb\BitrixEntityManager;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\DeleteResult;
use Bitrix\Main\Entity\UpdateResult;

class BitrixEntityManager implements EntityManagerInterface
{
    private $defaultConfig = [
        'autoloadScheme' => true,
    ];

    private array $config;
    private array $entitiesConfig;
    private \Bitrix\Main\DB\Connection $connection;

    private $metaDatas = [];
    private $repositories = [];

    /**
     * BitrixEntityManager constructor.
     * @param array $config
     * @param bool $config['autoloadScheme']
     * @param bool $config['entitiesConfig'] see setEntitiesConfig() description
     */
    public function __construct(array $config = [])
    {
        $entitiesConfig = null;
        if(isset($config['entitiesConfig'])) {
            $entitiesConfig = $config['entitiesConfig'];
            unset($config['entitiesConfig']);
        }
        $this->config = array_merge($this->defaultConfig, $config);

        $this->connection = Application::getConnection();

        if($entitiesConfig) {
            $this->setEntitiesConfig($entitiesConfig);
        }

        if($this->config['autoloadScheme']) {
            $this->loadSchema();
        }
    }

    /**
     * @param array $entitiesConfig Format: [Entity::class => [* entity config, properties of EntityMetadata *]]
     */
    public function setEntitiesConfig(array $entitiesConfig)
    {
        $this->entitiesConfig = $entitiesConfig;
    }

    public function getEntitiesConfig(): array
    {
        return $this->entitiesConfig;
    }

    public function getRepository($entityClass): RepositoryInterface
    {
        if(isset($this->repositories[$entityClass])) {
            return $this->repositories[$entityClass];
        }

        $metadata = $this->getMetadata($entityClass);
        $repository = new Repository($metadata);

        $this->repositories[$entityClass] = $repository;

        return $repository;
    }

    public function save(object $entity): bool
    {
        $metadata = $this->getMetadata(get_class($entity));

        $tableName = $metadata->tableName;
        $columns = $metadata->getMapping();

        $fields = [];
        $attribute = null;
        foreach($columns as $column) {
            $attribute = $column->attribute;

            $fields[$column->name] = $entity->$attribute;
        }

        $pk = $metadata->primaryKey;

        /**
         * @todo Проверка успешного добавления
         */
        $insertedId = $this->connection->add($tableName, $fields);
        if($metadata->$pk === null) {
            $entity->$pk = $insertedId;
        }

        return true;
    }

    public function update(object $entity): bool
    {
        $metadata = $this->getMetadata(get_class($entity));

        $columns = $metadata->getMapping();

        $pk = $metadata->primaryKey;
        if($entity->$pk === null) {
            return false;
        }

        /**
         * @TODO Обновлять только измененные поля, для этого необходимо наблюдать объект, отданный Repository
         */
        $fields = [];
        $attribute = null;
        foreach($columns as $column) {
            $attribute = $column->attribute;

            $fields[$column->name] = $entity->$attribute;
        }

        /**
         * @todo См. выше, необходимо проверить, изменилось ли состояние объекта
         */
        $hasChanged = true;
        if(!$hasChanged) {
            return true;
        }

        /**
         * @var $res UpdateResult
         */
        $res = $metadata->tableClass::update($entity->$pk, $fields);
        $affectedRows = $res->getAffectedRowsCount();

        if($res->isSuccess() && $affectedRows > 0) {
            return true;
        } else {
            /**
             * @TODO get error and set it to error messages
             */
            return false;
        }
    }

    public function delete(object $entity): bool
    {
        $metadata = $this->getMetadata(get_class($entity));
        $pk = $metadata->primaryKey;

        if($entity->$pk === null) {
            return false;
        }

        /**
         * @var $res DeleteResult
         */
        $res = $metadata->tableClass::delete($entity->$pk);
        if($res->isSuccess()) {
            return true;
        }

        return false;
    }

    public function getMetadata($entityClass): EntityMetadata
    {
        if(isset($this->metaDatas[$entityClass])) {
            return $this->metaDatas[$entityClass];
        }

        $entityConfig = $this->entitiesConfig[$entityClass] ?? null;
        if(!$entityConfig) {
            throw new \Exception('Config for entity ' . $entityClass . ' is not exists.');
        }
        if(!is_array($entityConfig)) {
            throw new \Exception('Config for entity ' . $entityClass . ' must be an array.');
        }

        if(!isset($entityConfig['tableClass'])) {
            throw new \Exception('Parameter tableClass must be exists in entityConfig.');
        }

        $metadata = new EntityMetadata();
        $metadata->entityClass = $entityClass;
        foreach($entityConfig as $k => $v) {
            $metadata->$k = $v;
        }

        $this->metaDatas[$entityClass] = $metadata;

        return $metadata;
    }

    public function loadSchema(): int
    {
        $count = 0;

        foreach($this->entitiesConfig as $entityClass => $entityConfig) {
            $metadata = $this->getMetadata($entityClass);

            if(isset($entityConfig['tableClass'])) {
                $metadata->createColumnsFromTableClass($entityConfig['tableClass']);
            } else if(isset($entityConfig['tableName'])) {
                $sql = "SHOW COLUMNS FROM `{$metadata->tableName}`";
                $rows = $this->connection->query($sql)->fetchAll();
                $metadata->createColumnsFromDescribe($rows);
            }

            $count++;
        }

        return $count;
    }

}
