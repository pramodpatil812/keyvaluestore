<?php
/**
 * Created by PhpStorm.
 * User: pramod
 * Date: 20/5/18
 * Time: 5:30 PM
 */

require_once __DIR__.'/../vendor/autoload.php';


$client = null;
$m = null;

try {
    $client = new PDO('mysql:dbname=kvstore;host=127.0.0.1;port=3306', 'root', '123456');
}
catch(PDOException $exception) {
    die("Connection failed.".$exception->getMessage().", code:".$exception->getCode());
}


$config = array(
    'server'=>array(
        array('host'=>'localhost', 'port'=>11211, 'weight'=>100)
    ),
    'namespace' => 'kvstorewithmysql',
    'version' => '1',
    'expiration' => 3600,	//1 hour
    'logerror' => true
);

try {
    $m = new \Lib\Memcached($config);
}
catch(Exception $e) {
    die($e->getMessage());
}

//var_dump($m);die;
$kvStore = new \Src\KVStoreMysql($client, $client, $m);
//var_dump($kvStore);die;
$kvStore->put('name', 'pramod');
$kvStore->put('city','gurgaon');
echo "<br/>"."Name: ".$kvStore->get('name');
echo "<br/>"."City: ".$kvStore->get('city');
$kvStore->delete('name');
echo "<br/>"."Size after deletion of key 'name': ".$kvStore->size();
$kvStore->clear();
echo "<br/>"."Size after clear operation: ".$kvStore->size();
$kvStore->put('address', 'sector 12');
echo "<br/>"."Address: ".$kvStore->get('address');
$kvStore->clear();