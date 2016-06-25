<?php
$pdo = new PDO("mysql:dbname=test;host=127.0.0.1", "root", "root");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
$sql = 'SHOW COLUMNS FROM `A`';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ret = $stmt->fetchAll(PDO::FETCH_OBJ);
var_dump($ret);