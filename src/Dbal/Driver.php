<?php

namespace Runn\Dbal;

use Runn\Core\InstanceableInterface;

/**
 * Abstract DBAL driver class
 * Contains InstanceableInterface implementation
 *
 * Class Driver
 * @package Runn\Dbal
 */
abstract class Driver implements DriverInterface, InstanceableInterface
{

    /**
     * @param string $class
     * @return static
     * @throws \Runn\Dbal\Exception
     *
     * @todo: wrong arguments list! either $class is set or all args are not required?
     */
    public static function instance($class = null, ...$args)
    {
        if (null === $class) {
            $class = get_called_class();
        }
        if (!class_exists($class)) {
            throw new Exception('Driver class "' . $class . '" does not exists');
        }
        if (!is_subclass_of($class, DriverInterface::class)) {
            throw new Exception('Class "' . $class . '" is not a DBAL driver');
        }
        return new $class(...$args);
    }

    /**
     * Returns DSN class name for this driver
     *
     * @return string
     * @throws Exception
     */
    public static function getDsnClassName(): string
    {
        $className = '\\' . implode('\\', array_slice(explode('\\', get_called_class()), 0, -1)) . '\\Dsn';
        if (!class_exists($className) || !is_subclass_of($className, Dsn::class)) {
            throw new Exception('This driver has not DSN class');
        }
        return $className;
    }

    /**
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueAfterLoad(Column $column, $value)
    {
        return $value;
    }

    /**
     * @param \Runn\Dbal\Column $column
     * @param mixed $value
     * @return mixed
     */
    public function processValueBeforeSave(Column $column, $value)
    {
        return $value;
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function existsTable(Connection $connection, string $tableName): bool
    {
        return (bool)$connection->query($this->getQueryBuilder()->getExistsTableQuery($tableName))->fetchScalar();
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Columns|null $columns
     * @param \Runn\Dbal\Indexes|null $indexes
     * @param array $extensions
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function createTable(Connection $connection, string $tableName, Columns $columns = null, Indexes $indexes = null, $extensions = []): bool
    {
        return $connection->execute($this->getQueryBuilder()->getCreateTableQuery($tableName, $columns, $indexes, $extensions));
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableOldName
     * @param string $tableNewName
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function renameTable(Connection $connection, string $tableOldName, string $tableNewName): bool
    {
        return $connection->execute($this->getQueryBuilder()->getRenameTableQuery($tableOldName, $tableNewName));
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function truncateTable(Connection $connection, string $tableName): bool
    {
        return $connection->execute($this->getQueryBuilder()->getTruncateTableQuery($tableName));
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function dropTable(Connection $connection, string $tableName): bool
    {
        return $connection->execute($this->getQueryBuilder()->getDropTableQuery($tableName));
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Column $column
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function addColumn(Connection $connection, string $tableName, Column $column): bool
    {
        return $connection->execute($this->getQueryBuilder()->getAddColumnQuery($tableName, $column));
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param \Runn\Dbal\Columns $columns
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function addColumns(Connection $connection, string $tableName, Columns $columns): bool
    {
        return $connection->execute($this->getQueryBuilder()->getAddColumnsQuery($tableName, $columns));
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function renameColumn(Connection $connection, string $tableName, string $oldColumnName, string $newColumnName): bool
    {
        return $connection->execute($this->getQueryBuilder()->getRenameColumnQuery($tableName, $oldColumnName, $newColumnName));
    }

    /**
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @param string $columnName
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function dropColumn(Connection $connection, string $tableName, string $columnName): bool
    {
        return $connection->execute($this->getQueryBuilder()->getDropColumnQuery($tableName, $columnName));
    }

}