<?php
/**
 * Created by PhpStorm.
 * User: pramod
 * Date: 20/5/18
 * Time: 12:30 PM
 */

namespace Src;


use Predis\Client;

class KVStore implements IKVStore
{
    private $redisClient;

    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function get(string $key): string
    {
        return $this->redisClient->get($key);
    }

    public function put(string $key, string $value): void
    {
        $this->redisClient->set($key, $value);
    }

    public function delete(string $key): void
    {
        $this->redisClient->del(array($key));
    }

    public function clear(): void
    {
        $this->redisClient->flushdb();
    }

    public function size(): int
    {
        return $this->redisClient->dbsize();
    }

}