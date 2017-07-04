<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Db_Core {
    public static $db;
	public static $driver;
    public static $instance;
    
    public function __construct()
    {
        $this->_initialize();
    }
    
    public static function init($driver, $config)
    {
        self::$driver = $driver;
		self::$db = new $driver;
		self::$db->set_config($config);
		self::$db->connect();
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
        $class_name = $this->dbdriver . '_driver';
        $file = BASEPATH . 'driver/' . $this->dbdriver . '_driver.php';
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

    public static function get_instance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }
        $class_name = get_called_class(); //获取子类类名，需要PHP>=5.3.0才支持
        self::$_instance = new $class_name();
        return self::$_instance;
    }
}
