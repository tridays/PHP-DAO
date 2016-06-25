<?php
$pdo = new PDO("mysql:dbname=test;host=127.0.0.1", "root", "root");
$drivers = $pdo->getAvailableDrivers();
var_dump($drivers);