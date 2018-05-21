<?php
/**
 * Created by PhpStorm.
 * User: pramod
 * Date: 20/5/18
 * Time: 12:25 PM
 */

namespace Src;

interface IKVStore
{
    public function get(string $key) : string;
    public function put(string $key, string $value) : void;
    public function delete(string $key) : void;
    public function clear() : void;
    public function size() : int;
}