<?php
namespace xporm\Query;

use PDO;
use PDOStatement;
use PDOException;
use xporm\ResultSet;

class SelectQuery {

    protected $distinct = false;
    protected $fields = [];
    protected $tables = [];
    protected $conditions = [];
    protected $orderBys = [];
    protected $groupBy = [];

    protected $sql = 'SELECT %DISTINCT% %FIELDS% FROM %TABLE% %WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%;';

    protected function parseFields($fields) {
        if (is_string($fields)) {
            $fields = mb_split(',', $fields);
        }
        if (!is_array($fields)) {
            throw new PDOException("输入不合法");
        }
        return $fields;
    }

    public function select($fields) {
        $this->fields += $this->parseFields($fields);

        return $this;
    }

    public function distinct($value = true) {
        $this->distinct = ($value != false);

        return $this;
    }

    public function from($table, $alias = null) {
        if ($alias === null) {
            if (!is_string($table)) {
                throw new PDOException("派生表必须显式定义别名");
            }
            $alias = $table;
        }
        if (!is_string($alias)) {
            throw new PDOException("别名必须为字符串");
        }
        $this->tables[] = [$table, $alias];

        return $this;
    }

    public function where(array $map, $logic = 'AND') {
        return $this;
    }

    public function groupBy($fields) {
        // group by 必须放在 order by 和 limit 之前，不然会报错。

        return $this;
    }

    public function having() {

        return $this;
    }

    public function orderBy($fields) {
        $fields = $this->parseFields($fields);
        $orderBys = [];
        foreach ($fields as $field) {
            $part = mb_split(' ', $field, 2);
            switch (count($part)) {
                case 0:
                    throw new PDOException("输入不合法");
                    break;
                case 1:
                    $part[1] = 'ASC';
                    break;
            }
            $orderBys[] = [$part[0] => $part[1]];
        }
        $this->orderBys += $orderBys;
        return $this;
    }

    public function limit($offset, $limits) {
        return $this;
    }

    public function makeSql() {

    }

    public function execute(PDO $pdo) {
        $stmt = $pdo->prepare($this->makeSql());
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

}