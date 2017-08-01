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
abstract class Driver
    implements DriverInterface, InstanceableInterface
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
     * @param \Runn\Dbal\Connection $connection
     * @param string $tableName
     * @return bool
     *
     * @codeCoverageIgnore
     */
    public function existsTable(Connection $connection, string $tableName): bool
    {
        return (bool)$connection->query($this->getExistsTableQuery($tableName))->fetchScalar();
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
        return $connection->execute($this->getCreateTableQuery($tableName, $columns, $indexes, $extensions));
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
        return $connection->execute($this->getRenameTableQuery($tableOldName, $tableNewName));
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
        return $connection->execute($this->getTruncateTableQuery($tableName));
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
        return $connection->execute($this->getDropTableQuery($tableName));
    }

}