<?php
phpinfo();
header("Content-Type:text/html; charset=utf-8");
echo "Hellow World";
$host = 'mysql';
$user = 'root';
$pass = 'root';
 
$conn = mysqli_connect($host, $user, $pass);
if (!$conn) {
    exit('Connection failed: '.mysqli_connect_error().PHP_EOL);
}
 
echo 'Successful database connection!'.PHP_EOL;

$redis = new Redis();    
$redis->connect('redis', '6379') || die("连接失败！");
// $redis->set('test', 200);
$res = $redis->get("test");
var_dump($res);
