DSN
===

Общее описание
--------------

Библиотека Runn Me! DBAL содержит специальный класс для описания атрибутов подключения к базе данных
в формате DSN (Data Source Name).

Это абстрактный класс `\Runn\Dbal\Dsn`. Для его использования драйвер базы данных должен унаследовать
данный класс и определить в нем константы `REQUIRED` (обязательные атрибуты подключения), 
`OPTIONAL` (необязательные атрибуты) и метод `public function getDriverDsnName(): string`, возвращающий
строковое имя драйвера БД.

К примеру рассмотрим DSN для базы данных SQLite:

```php
namespace Runn\Dbal\Drivers\Sqlite;

class Dsn extends \Runn\Dbal\Dsn
{

    protected const REQUIRED = ['file'];
    protected const OPTIONAL = [];

    public function getDriverDsnName(): string
    {
        return 'sqlite';
    }

}

```
Мы видим, что у данного DSN только один обязательный атрибут: `file`.

Немного сложнее DSN базы данных Postgres:

```php
namespace Runn\Dbal\Drivers\Pgsql;

class Dsn extends \Runn\Dbal\Dsn
{

    protected const REQUIRED = ['host'];
    protected const OPTIONAL = ['port', 'dbname', 'user', 'password'];

    public function getDriverDsnName(): string
    {
        return 'pgsql';
    }

}
```
Тут мы видим гораздо больше атрибутов.

Создание объектов DSN
---------------------

Класс `Runn\Dbal\Sqlite` является инстанцируемым с помощью конфига (реализует интерфейс `InstanceableByConfigInterface`).
Это означает, что нельзя напрямую создать объект такого класса с помощью оператора new, необходимо сначала
определить конфиг.

Можно создавать объект класса DSN непосредственно из базового класса `\Runn\Dbal\Dsn::instance()`,
указав в конфиге атрибут `class`:
```php
$config = new \Runn\Core\Config([
    'class' => \Runn\Dbal\Drivers\Sqlite\Dsn::class, 
    'file' => '/tmp/test.sqlite'
]);
$dsn = \Runn\Dbal\Dsn::instance($config);
```

Можно, указав драйвер (в этом случае библиотека попробует найти классы данного драйвера, а в них - 
реализацию DSN):
```php
$config = new \Runn\Core\Config([
    'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 
    'file' => '/tmp/test.sqlite' 
]);
$dsn = \Runn\Dbal\Dsn::instance($config);
```

Ну или, наконец, можно явно вызывать метод `::instance()` класса DSN, входящего в состав нужного драйвера:
```php
$config = new \Runn\Core\Config([
    'file' => '/tmp/test.sqlite'
]);
$dsn = \Runn\Dbal\Drivers\Sqlite\Dsn::instance($config);
```

Преобразование к строке 
-----------------------

Каждый объект класса `\Runn\Dbal\Dsn` содержит магический метод `__toString`, обеспечивающий
корректное преобразование к строковому значению (например, для дальнейшего использования в PDO):

```php
$config = new \Runn\Core\Config([
    'class' => \Runn\Dbal\Drivers\Sqlite\Dsn::class, 
    'file' => '/tmp/test.sqlite' 
]);
$dsn = Dsn::instance($config);

echo $dsn; // Результат: sqlite:/tmp/test.sqlite
```

Нужно отметить, что в итоговом строковом представлении будут только те атрибуты, что указаны 
в константах `REQUIRED` и `OPTIONAL` в классе DSN. Все остальные атрибуты конфига будут проигнорированы.