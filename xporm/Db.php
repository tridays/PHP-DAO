<?php
namespace xporm;

use PDO;
use PDOStatement;
use PDOException;

use stdClass as Object;

include 'functions.php';

class Db {

    /**
     * @var PDO
     */
    protected $driver = null;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array|null
     */
    protected $tables = null;

    public function __construct() {
        // 发生错误时抛出异常
        $this->options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        // 默认键名全部小写
        //$this->options[PDO::ATTR_CASE] = PDO::CASE_LOWER;
        // 优先使驱动预处理语句
        $this->options[PDO::ATTR_EMULATE_PREPARES] = false;
        // 禁用强转字符串结果
        $this->options[PDO::ATTR_STRINGIFY_FETCHES] = false;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setPersistent($value = true) {
        if ($this->driver !== null) {
            throw new PDOException("请在连接初始化之前调用 setPersistent()");
        }
        $this->options[PDO::ATTR_PERSISTENT] = $value;
    }

    /**
     * @param $dsn
     * @param $username
     * @param $password
     * @return bool
     */
    public function connect($config) {
        $config = self::parseConfig($config);
        if (!isset($config['type'])) {
            throw new PDOException("不支持的数据库驱动");
        }
        $type = ucfirst($config['type']);
        require __DIR__ . "/Db/Drivers/{$type}.php";
        $class = __NAMESPACE__ . "\\Db\\Drivers\\" . $type;
        $this->driver = new $class($config, $this->options);
    }

    /**
     * @return void
     */
    public function close() {
        $this->pdo = null;
    }

    public function transaction(callable $cb) {
        if (!$this->driver->beginTransaction()) {
            throw new PDOException("当前数据库驱动不支持事务");
        }
        $ret = $cb();
        if ($ret) {
            $this->driver->commit();
        } else {
            $this->driver->rollBack();
        }
    }

    /**
     * @param $sql
     * @return int
     */
    public function execute($sql) {
        return $this->driver->exec($sql);
    }

    /**
     * @param $sql
     * @return PDOStatement
     */
    public function query($sql) {
        return $this->driver->query($sql);
    }

    /**
     * @param $sql
     * @return PDOStatement
     */
    public function prepare($sql) {
        return $this->driver->prepare($sql);
    }

    /**
     * 配置解析
     * 字符串解析: mysql://username:password@localhost:3306/DbName?param1=val1&param2=val2#utf8
     *
     * @static
     * @param array|string $config
     * @return array
     */
    public static function parseConfig($config) {
        if (is_string($config)) {
            $info = parse_url($config);
            if (!$info) return [];

            $config = [
                'type'     => $info['scheme'],
                'username' => get_in_array_or_default($info, 'user', ''),
                'password' => get_in_array_or_default($info, 'pass', ''),
                'host'     => get_in_array_or_default($info, 'host', ''),
                'port'     => get_in_array_or_default($info, 'port', ''),
                'database' => !empty($info['path']) ? ltrim($info['path'], '/') : '',
                'charset'  => get_in_array_or_default($info, 'fragment', ''),
            ];

            if (isset($info['query'])) {
                parse_str($info['query'], $config['params']);
            } else {
                $config['params'] = [];
            }
            return $config;
        } else {
            return $config;
        }
    }

    /**
     * @return array
     */
    public function getTables() {
        if ($this->tables === null) {
            $tables = $this->driver->getTables();
            $values = array_pad([], count($tables), null);
            $this->tables = array_combine($tables, $values);
        }
        return array_keys($this->tables);
    }

    public function isTableExists($tableName) {
        return in_array($tableName, $this->getTables());
    }

    /**
     * @param $tableName
     * @return array
     */
    public function getTableFields($tableName) {
        if (!$this->isTableExists($tableName)) {
            throw new PDOException("表不存在");
        }
        if ($this->tables[$tableName] === null) {
            $this->tables[$tableName] = $this->driver->getTableFields($tableName);
        }
        return array_keys($this->tables[$tableName]);
    }

    public function isTableFieldsExists($tableName, $fieldName) {
        return in_array($fieldName, $this->getTableFields($tableName));
    }

    public function __call($method, array $params) {
        if ($this->driver === null) {
            throw new PDOException("未连接到数据库");
        }
        return call_user_func_array([$this->driver, $method], $params);
    }
}