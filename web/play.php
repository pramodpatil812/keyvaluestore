<?php
/**
 * Created by PhpStorm.
 * User: pramod
 * Date: 20/5/18
 * Time: 5:04 PM
 */

require_once __DIR__.'/../vendor/autoload.php';

$client = new Predis\Client([
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 7379,
]);

$kvStore = new \Src\KVStore($client);

$kvStore->put('name','pramod');
$kvStore->put('city','gurgaon');
echo "<br/>"."Name: ".$kvStore->get('name');
echo "<br/>"."City: ".$kvStore->get('city');
$kvStore->delete('name');
echo "<br/>"."Size after deletion of key 'name': ".$kvStore->size();
$kvStore->clear();
echo "<br/>"."Size after clear operation: ".$kvStore->size();

