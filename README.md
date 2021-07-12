# BitrixEntityManager

## Установка
Подключить `enoffspb/bitrix-entity-manager` через composer.

## Подключение и настройка

```php
use enoffspb\BitrixEntityManager\BitrixEntityManager;

$entityManager = new BitrixEntityManager([
    'entitiesConfig' => [
        MyEntity::class => [
            'tableName' => 'my_table_name'
        ]
    ]
]);
```

## Использование
```php
// Сохранение
$entity = new Entity(); // Любой класс для сущности
$entity->name = 'entity name';

$entityManager->save($entity);

// Обновление

$entity->name = 'new name';
$entityManager->update($entity);

// Удаление
$entityManager->delete($entity);

// Получение сущностей через Repository
$repository = $entityManager->getRepository(Entity::class);

$entity = $repository->getById(1);
$entities = $repository->getList(/** D7 parameters from getList() */);
```
