<?php
namespace xporm\Db\Drivers;

use PDO;

class Mysql extends PDO {

    public function __construct(array $config, array $options) {
        parent::__construct(
            self::makeDSN($config),
            $config['username'],
            $config['password'],
            $options
        );
    }

    public function getTables() {
        $stmt = $this->prepare("SHOW TABLES");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_NUM);
        return array_map(function &(&$v) {
            return $v[0];
        }, $tables);
    }

    public function getTableFields($tableName) {
        $stmt = $this->prepare("SHOW COLUMNS FROM `{$tableName}`");
        $stmt->execute();
        $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($fields as $field) {
            $field = array_change_key_case($field, CASE_LOWER);
            $name = $field['field'];
            $results[$name] = [
                'name' => $name,
                'type' => $field['type'],
                'nullable' => $field['null'] === 'YES',
                'default' => $field['default'],
                'primary' => $field['key'] === 'PRI',
                'unique' => in_array($field['key'], ['PRI', 'UNI']),
                'autoinc' => $field['extra'] === 'auto_increment',
            ];
        }
        return $results;
    }

    public static function makeDSN(array $config) {
        $prefix = 'mysql:';

        $arr = [];

        $arr[] = "host={$config['host']}";
        if (!empty($config['port'])) {
            $arr[] = "port={$config['port']}";
        } else if (!empty($config['socket'])) {
            $arr[] = "unix_socket={$config['socket']}";
        }
        $arr[] = "dbname={$config['database']}";
        if (!empty($config['charset'])) {
            $arr[] = "charset={$config['charset']}";
        }

        return $prefix . implode(';', $arr);
    }

}