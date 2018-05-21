<?php
/**
 * Created by PhpStorm.
 * User: pramod
 * Date: 20/5/18
 * Time: 12:30 PM
 */

namespace Src;


use Lib\Memcached;

class KVStoreMysql implements IKVStore
{
    private $writeConn;
    private $readConn;
    private $memcached;

    public function __construct(\PDO $writeConn, \PDO $readConn, Memcached $memcached)
    {
        $this->conn = $writeConn;
        $this->readConn = $readConn;
        $this->memcached = $memcached;
    }

    public function get(string $key): string
    {
        $val = $this->memcached->get($key);
        if($val) {
            return $val;
        }

        $query = "select v from kv where k='$key'";
        $pdoStmt = $this->readConn->query($query);

        foreach ($pdoStmt as $row) {
            //set in cache
            $this->memcached->set($key, $row['v'], Memcached::DEFAULT_EXPIRATION);
            return $row['v'];
        }

        return null;
    }

    public function put(string $key, string $value): void
    {
        $query = "replace into kv(k,v) VALUE ('$key','$value')";
        $this->conn->exec($query);

        $this->memcached->delete($key);
    }

    public function delete(string $key): void
    {
        $query = "delete from kv where k='$key'";
        $this->conn->exec($query);

        $this->memcached->delete($key);
    }

    public function clear(): void
    {
        $query = "truncate table kv";
        $this->conn->exec($query);

        //delete all cache keys
    }

    public function size(): int
    {
        $query = "select count(*) as row_count from kv";
        $pdoStmt = $this->readConn->query($query);
        foreach ($pdoStmt as $row) {
            return $row['row_count'];
        }

        return null;
    }

}