<?php
$pdo = new PDO("mysql:dbname=test;host=127.0.0.1", "root", "root", [
    PDO::ATTR_STRINGIFY_FETCHES => false,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$sql = "SELECT `id`, `name` FROM `A`;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

while (($row = $stmt->fetch(PDO::FETCH_OBJ)) !== false) {
    var_dump($row);
}