<?php
require './xporm/Db.php';
use xporm\Db;

$config = "mysql://root:root@127.0.0.1:3306/test";

$db = new Db();
$db->connect($config);

$ret = $db->getTables();
var_dump($ret);

$ret = $db->getTableFields('A');
var_dump($ret);

var_dump($db);