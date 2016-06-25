<?php
require './xporm/Db.php';
use xporm\Db;

$config = "mysql://root:root@127.0.0.1:3306/test";

$db = new Db();
$db->connect($config);

//$ret = $db->getTables();
//$ret = $db->getTableFields('A');

$stmt = $db->prepare("SELECT * FROM `A`");
$stmt->execute();
while (($row = $stmt->fetch(PDO::FETCH_OBJ)) !== false) {
    var_dump($row);
}