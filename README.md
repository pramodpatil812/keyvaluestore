# keyvaluestore
Implementation of key value store using redis and mysql/memcached

<b>Prerequisites to run</b>

You should have a web server running - apache or nginx.<br/>
You should have running instance of redis, mysql and memcached server.

<b>Steps to run</b>

1. Clone the repository using <code>git clone</code>
2. Go to root and run <code>composer install</code>. This will download the dependencies in vendor folder.
3. Change the configuration in <code>web/play.php</code> and <code>web/playwithmysql.php</code>. Slave configuration can also be provided in <code>playwithmysql.php</code>
4. To test with redis run <code>web/play.php</code>
5. To test with mysql run <code>web/playwithmysql.php</code>
