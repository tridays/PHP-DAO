<?php
namespace xporm;

use PDO;

class BaseRepository {

    /**
     * @var PDO
     */
    protected $pdo;

    protected $columnMeta;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function set($sql, array $data) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

}