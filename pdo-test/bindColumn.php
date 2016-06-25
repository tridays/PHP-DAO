<?php
$pdo = new PDO("mysql:dbname=test;host=127.0.0.1", "root", "root");

$sql = "SELECT `id`, `name` FROM `A`;";
$stmt = $pdo->prepare($sql);

$id = 0;
$name = '';
$stmt->bindColumn(1, $id, PDO::PARAM_INT);
$stmt->bindColumn(2, $name, PDO::PARAM_STR);

$stmt->execute();

while (($row = $stmt->fetch(PDO::FETCH_BOUND)) !== false) {
    var_dump($id); var_dump($name);
    var_dump($row);
}