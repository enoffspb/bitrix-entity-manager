# Тестирование

Возможно только совместно с рабочей установкой битрикс, добавленным загрузчиком тестов и рядом дополнительных действий.

1. Установить рабочую систему на битрикс.

   
2. Получить репозиторий `enoffspb/bitrix-entity-manager`
   

3. Выполнить `composer install`

   
3. Запустить тесты из директории `enoffspb/bitrix-entity-manager/tests/unit` используя представленный bootstrap-файл для PHPUnit `--bootstrap tests/bootstrap.php` 
   и указанием дополнительного параметра `--bitrix-dir /path/to/bitrix` или переменной окружения `BITRIX_DIR` (укажите путь к вашему битрикс-проекту)
   
   
4. Итоговая команда для запуска:

   ```phpunit --bootstrap tests/bootstrap.php tests/unit -- --bitrix-dir /path/to/bitrix-project```
   
