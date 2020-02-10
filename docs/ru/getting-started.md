Быстрый старт
=============

Библиотека Runn Me! DBAL Library предназначена для работы с реляционными базами данных путем абстрагирования
от различий БД за общими интерфейсами. 

Шаг 1. Конфигурация
-------------------

Создайте конфигурацию подключения к базе данных. Параметр 'driver' представляет собой имя класса драйвера выбранной 
вами БД и является обязательным. Остальные параметры зависят от конкретного драйвера.

**Пример для SQLite (база данных в памяти):**

```php
use Runn\Core\Config;

$config = new Config([
    'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 
    'file' => ':memory:'
]);
```

**Пример для SQLite (база данных в файле):**

```php
use Runn\Core\Config;

$config = new Config([
    'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 
    'file' => '/tmp/mydb.sqlite'
]);
```

**Пример для MySQL:**

```php
use Runn\Core\Config;

$config = new Config([
    'driver' => \Runn\Dbal\Drivers\Mysql\Driver::class,
    'host' => '127.0.0.1',
    'dbname' => 'mydb',
    'user' => 'myusername',
    'password' => 'mypassword',
]);
```

**Пример для Postgres:**

```php
$config = new Config([
    'driver' => \Runn\Dbal\Drivers\Pgsql\Driver::class,
    'host' => '127.0.0.1',
    'dbname' => 'mydb',
    'user' => 'postgres',
    'password' => 'postgres',
]);
```

Шаг 2. Подключение к базе данных
--------------------------------

Создайте объект подключения к БД:

```php
use Runn\Dbal\Connection;

$connection = new Connection($config);
```

Шаг 3. Запрос к БД
------------------

Создайте запрос к базе данных. Самый простой способ - это написать просто SQL и обернуть его в объект
класса `\Runn\Dbal\Query`. В этот же объект можно передать и подставляемые параметры запроса:

```php
use Runn\Dbal\Query;
use Runn\Dbal\Dbh;

$q1 = new Query(
    'CREATE TABLE persons (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100))' // Пример для SQLite!
);

$q2 = (new Query(
    'INSERT INTO persons (name) VALUES (:name)'
))->params([':name' => 'Иванов']);

$q3 = (new Query(
    'SELECT * FROM persons WHERE id=:id'
))->param(':id', 1, Dbh::PARAM_INT);
```

Шаг 4. Выполнение запросов и получение результатов
--------------------------------------------------

Выполните запросы. Для выполнения запросов, не требующих возврата результата, используйте метод `execute()`, 
если же нужно получить от БД данные - метод `query()`.

При этом вы можете сразу же получить результат в виде объектов интересующего вас класса и даже в виде
типизированной коллекции:

```php
use Runn\Core\TypedCollection;

$connection->execute($q1);
$connection->execute($q2);

class Person {
    public $id;
    public $name;
}

$sth = $connection->query($q3);
$data = $sth->fetchAllObjects(Person::class);

class Persons extends TypedCollection 
{
    public static function getType()
    {
        return Person::class;
    }
}

$sth = $connection->query($q3);
$data = $sth->fetchAllObjectsCollection(Persons::class);
```