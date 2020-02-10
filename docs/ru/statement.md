Statement
=========

Общее описание
--------------

В библиотеке Runn Me! DBAL имеется класс `\Runn\Dbal\Statement` расширяющий возможности стандартного класса `PDOStatement`.

По умолчанию именно этот класс используется в запросах к базе данных для представления запросов. Впрочем, вы можете
использовать и свой класс запросов, указав его в [конфигурации соединения с БД](./dbh.md).

Дополнительные методы получения результатов запросов
----------------------------------------------------

Класс `\Runn\Dbal\Statement` добавляет ряд полезных методов для получения результатов запросов к БД.

**`fetchScalar()`**

Получение из БД результата запроса в виде одного (скалярного) значения

```php
$sth = $dbh->query('SELECT count(*) FROM sometable');
echo $sth->fetchScalar();
```

**`fetchAllObjects()`**

Получение из БД результата в виде массива объектов заданного класса

```php
$sth = $dbh->query('SELECT * FROM sometable LIMIT 5');
echo $sth->fetchAllObjects(SomeEntry::class); // массив из 5 объектов класса SomeEntry
```

**`fetchAllObjectsCollection()`**

Получение из БД результата в виде типизированной коллекции

```php
class SomeEntryCollection extends TypedCollection
{
    public static function getType()
    {
        return SomeEntry::class;
    }
}

$sth = $dbh->query('SELECT * FROM sometable LIMIT 5');
echo $sth->fetchAllObjectsCollection(SomeEntryCollection::class); // коллекция класса SomeEntryCollection, состоящая из 5 объектов класса SomeEntry
```
