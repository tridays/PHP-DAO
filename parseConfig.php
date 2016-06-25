<?php
require './xporm/Db.php';
use xporm\Db;

$config = "mysql://username:passwd@localhost:3306/DbName?param1=val1&param2=val2";

$ret = Db::parseConfig($config);
var_dump($ret);