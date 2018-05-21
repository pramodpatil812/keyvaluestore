<?php

/**
 * Created by PhpStorm.
 * User: pramod
 * Date: 21/5/18
 * Time: 6:37 PM
 */

namespace Lib;

class Memcached
{
    /** default TTL(in seconds) for objects when the same in not provided via constructor argument. */
    const DEFAULT_EXPIRATION = 3600;    //1 hour

    /** @var object $m holder for native memcached class object */
    protected $m;

    /** @var array $c holds an array of configurations */
    private $c;


    public function __construct(array $config)
    {
        if (!extension_loaded('memcached')) {
            throw new \RuntimeException("Extension memcached not loaded.");
        }

        if (isset($config['expiration']) && (!ctype_digit($config['expiration']) || $config['expiration']<0)) {
            throw new \InvalidArgumentException("Default expiration time is not valid.");
        }

        $this->c = $config;

        if (!isset($this->c['expiration']) || $this->c['expiration'] == '') {
            $this->c['expiration'] = self::DEFAULT_EXPIRATION;
        }

        if (!isset($this->c['namespace'])) {
            $this->c['namespace'] = '';
        }

        if (!isset($this->c['version'])) {
            $this->c['version'] = '';
        }

        $this->m = new \Memcached();

        if (isset($config['server']) && is_array($config['server'])) {
            foreach ($config['server'] as $server) {
                if (isset($server['host']) && isset($server['port']) && isset($server['weight'])) {
                    $this->addServer($server['host'], $server['port'], $server['weight']);
                }
            }
        }

    }

    public function set($key, $value, $expiration = null)
    {
        if (is_null($expiration)) {
            $expiration = $this->c['expiration'];
        }

        if (!$this->m->set($this->createKey($key), $value, $expiration)) {
            $this->logError(__METHOD__.":".__LINE__.":Unable to set key $key. Result code: ".$this->m->getResultCode().". Result message: ".$this->m->getResultMessage());

            return false;
        }

        return true;
    }

    public function get($key, callable $cb = null, &$cas_token = null)
    {
        $value = $this->m->get($this->createKey($key), $cb, $cas_token);

        $result_code = $this->m->getResultCode();
        //check for result code since value may have been stored FALSE
        if ($value === false && $result_code === \Memcached::RES_NOTFOUND) {
            $this->logError(__METHOD__.":".__LINE__.":Key $key does not exists. Result code: $result_code. Result message: ".$this->m->getResultMessage());
        }

        return $value;
    }

    public function delete($key)
    {
        if (!$this->m->delete($this->createKey($key))) {
            $result_code = $this->m->getResultCode();
            if ($result_code === \Memcached::RES_NOTFOUND) {
                $this->logError(__METHOD__.":".__LINE__.":Unable to delete key $key. Key doesn't exist. Result code: $result_code. Result message: ".$this->m->getResultMessage());
            } else {
                $this->logError(__METHOD__.":".__LINE__.":Unable to delete key $key. Result code: $result_code. Result message: ".$this->m->getResultMessage());
            }

            return false;
        }

        return true;
    }

    public function addServer($host, $port, $weight = 0)
    {
        if (!$this->m->addServer($host, $port, $weight)) {
            $this->logError(__METHOD__.":".__LINE__.":Unable to add server. Result code: ".$this->m->getResultCode().". Result message: ".$this->m->getResultMessage());    //__METHOD__ gives "classname:methodname"

            return false;
        }

        return true;
    }

    public function createKey($key)
    {
        return md5($this->c['namespace'].$this->c['version'].$key);
    }

    protected function logError($msg)
    {
        if ($this->c['logerror']) {
            error_log($msg);
        }
    }

}