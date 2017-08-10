<?php
declare(strict_types=1);

namespace Framework\Repository\Repository;

use Exception;
use PDO;

class QueryExecutor
{
    static private $connection = null;
    static private $config = null;

    private $prepareStatement = null;

    public function query($query, $data)
    {
        $connection = self::getConnection();
        $prepareStatement = $connection->prepare($query);
        $ret = $prepareStatement->execute($data);
        if ($ret) {
            $this->prepareStatement = $prepareStatement;
        } else {
            throw new Exception(self::formatMessage($prepareStatement));
        }
    }

    public function execute($data)
    {
        $this->prepareStatement->execute($data);
    }

    public function fetch()
    {
        return $this->prepareStatement->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll()
    {
        return $this->prepareStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastId()
    {
        return self::getConnection()->lastInsertId();
    }

    static public function beginTransaction()
    {
        self::getConnection()->beginTransaction();
    }

    static public function commit()
    {
        self::getConnection()->commit();
    }

    static public function rollback()
    {
        self::getConnection()->rollback();
    }

    static public function transaction($transaction)
    {
        $connection = self::getConnection();
        $connection->beginTransaction();
        $result = call_user_func($transaction);
        if ($result) {
            $connection->commit();
        } else {
            $connection->rollback();
        }
    }

    static public function queryAndFetch($query, $data)
    {
        $connection = self::getConnection();
        $prepareStatement = $connection->prepare($query);
        $ret = $prepareStatement->execute($data);
        if ($ret) {
            return $prepareStatement->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception(self::formatMessage($prepareStatement));
        }
    }

    static public function queryAndFetchAll($query, $data)
    {
        $connection = self::getConnection();
        $prepareStatement = $connection->prepare($query);
        $ret = $prepareStatement->execute($data);
        if ($ret) {
            return $prepareStatement->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw new Exception(self::formatMessage($prepareStatement));
        }
    }

    static private function getConnection()
    {
        if (self::$connection === null) {
            $config = self::getConfig();
            $type = $dsn = $user = $password = null;
            extract($config);
            foreach ($dsn as $name => $value) {
                $dsn[$name] = $name . '=' . $value;
            }
            $dsnStr = join(';', $dsn);
            $connection = sprintf('%s:%s', $type, $dsnStr);
            self::$connection = new PDO($connection, $user, $password);
        }
        return self::$connection;
    }

    static public function getConfig()
    {
        return self::$config;
    }

    static public function setConfig($config)
    {
        self::$config = $config;
    }

    static private function formatMessage($prepareStatement)
    {
        return $prepareStatement->errorInfo();
    }
}
