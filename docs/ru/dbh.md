DBH
===

Общее описание
--------------

В библиотеке Runn Me! DBAL имеется специальный класс `\Runn\Dbal\Dbh` для представления соединения с базой данных (DataBase Handler).

Фактически этот класс представляет собой расширение стандартного класса `\PDO`, добавляющим к нему 
реализацию интерфейса `InstanceableByConfigInterface` и еще ряд особенностей.

Инициализация через конфиг
--------------------------

Для инициализации DBH требуется подготовить конфиг. В общем случае - такой же, как и для [DSN](./dsn.md).

```php
$config = new \Runn\Core\Config([
    'driver' => \Runn\Dbal\Drivers\Sqlite\Driver::class, 
    'file' => '/tmp/test.sqlite'
]);

$dbh = \Runn\Dbal\Dbh::instance($config);
```

Опции и их значения по умолчанию
--------------------------------

В таблице перечислены опции соединения с базой данных, соответствующие им атрибуты конфига и значения опций по умолчанию:

| Опция PDO | Атрибут конфига DBH | Значение по умолчанию |
|-----------|---------------------|-----------------------|
| $username | $config->user | null |
| $passwd | $config->password | null |
| Dbh::ATTR_ERRMODE | $config->errmode | Dbh::ERRMODE_EXCEPTION |
| Dbh::ATTR_STATEMENT_CLASS | $config->statement | [\Runn\Dbal\Statement](./statement.md) |

Все прочие опции передаются из конфига, из атрибута `options` в конструктор объекта класса Dbh. Например, вот как можно 
установить опцию MYSQL_ATTR_INIT_COMMAND:

```php
$config = new \Runn\Core\Config([
    'driver' => \Runn\Dbal\Drivers\Mysql\Driver::class, 
    'host => 'localhost',
    'dbname' => 'test',
    'user' => 'root',
    'password' => '',
    'options => [
        Dbh::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    ],
]);

$dbh = \Runn\Dbal\Dbh::instance($config);
```

Переименование методов работы с транзакциями
--------------------------------------------

Методы работы с транзакциями в классе `Runn\Dbal\Dbh` называются иначе, нежели в `\PDO` для большей консистентности.

| Метод в \PDO | Метод в Dbh |
|--------------|-------------|
| beginTransaction() | transactionBegin() |
| commit() | transactionCommit() |
| rollBack() | transactionRollback() |

