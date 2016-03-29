<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

//类别名
class_alias('Model_Base','db',false);

class Model_Base
{
    private static $_instance;
    private $config = array();
    
    private $dbhost = '127.0.0.1';
    private $dbuser = 'root';
    private $dbpwd = '';
    private $dbname = 'zhoumo';
    private $dbdriver = 'mysql';
    private $dbprefix = 'c_';
    private $pconnect = FALSE;
    private $dbcharset = 'utf8';
    private $cache_dir = '';
    
    public $driver = '';
    protected $db_config_id = 1; //数据库配置号，默认选择1号配置


    public function __construct()
    {
        $this->_initialize();
    }

    private function _load_config()
    {
        $file = APPPATH . 'config/database.php';
        if (is_file($file)) {
            require ($file);
            $this->config = $db;
            unset($db);
        } else {
            error('Not found database config file : ' . $file);
        }
    }
    
    
    private function _set_mem_var()
    {
        if (isset($this->config[$this->db_config_id])) {
            $this->cur_db_config = $this->config[$this->db_config_id];
            foreach ($this->cur_db_config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        } else {
            error('Not found database is config : ' . $this->db_config_id);
        }
    }

    private function _load_driver()
    {
        $class_name = 'db_' . $this->dbdriver . '_driver';
        $file = BASEPATH . 'driver/db/db_' . $this->dbdriver . '_driver.php';
        if (is_file($file)) {
            require_once ($file);
            if (class_exists($class_name, false)) {
                $this->driver = new $class_name;
            }
        } else {
            error('Not found database driver file : ' . $file);
        }
    }
    
    private function _initialize()
    {
        //加载配置
        $this->_load_config();
        //设置成员变量
        $this->_set_mem_var();
        //加载驱动
        $this->_load_driver();
        //连接数据库
        $this->driver->connect($this->dbhost, $this->dbuser, $this->dbpwd, $this->dbcharset, $this->dbname, $this->pconnect);
    }
    
    public function set_db_config_id($db_config_id)
    {
        $this->db_config_id = $db_config_id;
        $this->_initialize();
    }

    public function get_cur_db_config()
    {
        return $this->cur_db_config;
    }

    public function delete($table, $where)
    {
        if ($this->pconnect === false) {
            $this->driver->connect($this->dbhost, $this->dbuser, $this->dbpwd, $this->
                dbcharset, $this->dbname, $this->pconnect);
        }

        if ($where) {
            $where = ' where ' . $where;
        }
        $sql = 'delete from ' . $this->dbprefix . $table . $where;
        $result = $this->driver->query($sql);

        if ($this->pconnect === false) {
            $this->driver->close();
        }

        return $result;
    }

    public function update($table, $set, $where = '')
    {
        if ($this->pconnect === false) {
            $this->driver->connect($this->dbhost, $this->dbuser, $this->dbpwd, $this->
                dbcharset, $this->dbname, $this->pconnect);
        }

        if (is_array($set)) {
            $fields = '';
            foreach ($set as $key => $val) {
                $fields .= ',' . $key . ' = ' . "'$val'";
            }

            if ($where) {
                $where = ' where ' . $where;
            }

            $sql = 'update ' . $this->dbprefix . $table . ' set ' . ltrim($fields, ',') . $where;
            $result = $this->driver->query($sql);

            if ($this->pconnect === false) {
                $this->driver->close();
            }

            return $result;
        }

    }

    public function insert($table, $data, $ret_insert_id = false)
    {
        if ($this->pconnect === false) {
            $this->driver->connect($this->dbhost, $this->dbuser, $this->dbpwd, $this->
                dbcharset, $this->dbname, $this->pconnect);
        }

        if (is_array($data)) {
            $fields = '';
            $values = '';
            foreach ($data as $key => $val) {
                $fields .= ',' . $key;
                $values .= ',\'' . $val . '\'';
            }

            $sql = 'insert into ' . $this->dbprefix . $table . ' (' . ltrim($fields, ',') .
                ') ' . 'values(' . ltrim($values, ',') . ')';
            $result = $this->driver->query($sql);

            if ($ret_insert_id) {
                $result = $this->driver->insert_id();
            }

            if ($this->pconnect === false) {
                $this->driver->close();
            }

            return $result;
        }
    }

    public function get($opt)
    {
        if ($this->pconnect === false) {
            $this->driver->connect($this->dbhost, $this->dbuser, $this->dbpwd, $this->
                dbcharset, $this->dbname, $this->pconnect);
        }

        $data = array();
        $sql = $this->_build_select_sql($opt);

        if (isset($opt['cached']) && $opt['cached'] === true) {
            $cache_time = isset($opt['cache_time']) ? intval($opt['cache_time']) : 1800; //缓存时间/秒
            //检查目录是否存在，否则创建
            if(!is_dir($this->cache_dir)){
                @mkdir($this->cache_dir);
            }
            $file = $this->cache_dir . md5($sql) . '.txt';
            if (is_file($file) && C_TIMESTAMP - filemtime($file) < $cache_time) {
                $fp = fopen($file, "r");
                $str = fread($fp, filesize($file));
                $data = unserialize($str);
            } else {
                $query = $this->driver->query($sql);
                while ($rows = $this->driver->fetch_array($query)) {
                    $data[] = $rows;
                }
                $fp = fopen($file, 'w');
                fwrite($fp, serialize($data));
            }
            fclose($fp);
        } else {
            $query = $this->driver->query($sql);
            while ($rows = $this->driver->fetch_array($query)) {
                $data[] = $rows;
            }
        }

        if ($this->pconnect === false) {
            $this->driver->close();
        }

        return $data;
    }

    private function _build_select_sql($opt)
    {
        $sql = '';
        $sql_tpl = array(
            'select' => '',
            'from' => $this->dbprefix,
            'where' => '',
            'group_by' => '',
            'order_by' => '',
            'limit' => '');
        foreach ($opt as $key => $val) {
            if (isset($sql_tpl[$key])) {
                $dft_val = $sql_tpl[$key];
                if (strpos($key, '_')) {
                    $key = str_replace('_', ' ', $key);
                }
                $sql .= "{$key} {$dft_val}{$val} ";
            }
        }
        return $sql;
    }

    public function show_tables()
    {
        $query = $this->driver->query('show tables');
        $data = array();
        while ($row = $this->driver->fetch_array($query)) {
            $data[] = $row['Tables_in_' . $this->dbname];
        }
        $this->driver->close();
        return $data;
    }
    
    public static function init()
    {
        return self::get_instance();
    }

    public static function get_instance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }
        self::$_instance = new Model_Base();
        return self::$_instance;
    }
}
