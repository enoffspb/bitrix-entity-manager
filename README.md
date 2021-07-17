# BitrixEntityManager

## Установка
Подключить `enoffspb/bitrix-entity-manager` через composer.

## Подключение и настройка

```php
use enoffspb\BitrixEntityManager\BitrixEntityManager;

$entityManager = new BitrixEntityManager([
    'entitiesConfig' => [
        MyEntity::class => [
            'tableClass' => MyEntityTable::class
        ]
    ]
]);


// Вы можете зарегистрировать компонент через сервис-локатор в битрикс:

$serviceLocator = \Bitrix\Main\DI\ServiceLocator::getInstance();
$serviceLocator->addInstance('app.entityManager', $entityManager);

// И в дальнейшем использовать в приложении

/**
* @var $entityManager BitrixEntityManager
 */
$entityManager = $serviceLocator->get('app.entityManager');
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

## Тестирование
Тестирование при разработке описано в [tests/README.md](tests/README.md).