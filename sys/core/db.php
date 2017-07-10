<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Db_Core {
    public static $db;
	public static $driver;
    public static $instance;
    
    private $config = array();
    
    public function __construct()
    {
        self::init();
    }
    
    public static function object()
    {
        return self::$db;
    }
    
    public static function init($name)
    {
        //加载配置
        $config = self::load_config($name);
        
        if(!empty($config['dbdriver'])){
            self::$driver = $config['dbdriver'];
    		self::$db = new self::$driver; //实例化驱动
    		self::$db->set_config($config); //设置配置
    		self::$db->connect(); //连接数据库
        }
    }

    public static function load_config($name='')
    {
        $config = array();
        $name = empty($name) ? 'DEFAULT' : $name;
        $file = APPPATH . 'config/database.php';
        if (is_file($file)) {
            $config = include($file);
        } else {
            error('Not found database config file : ' . $file);
        }
        return $config;
    }

	public static function insert_id() 
    {
		return self::$db->insert_id();
	}

	public static function fetch($resourceid, $type = MYSQL_ASSOC) 
    {
		return self::$db->fetch_array($resourceid, $type);
	}

	public static function fetch_first($sql, $arg = array(), $silent = false) 
    {
		$res = self::query($sql, $arg, $silent, false);
		$ret = self::$db->fetch_array($res);
		self::$db->free_result($res);
		return $ret ? $ret : array();
	}

	public static function fetch_all($sql, $arg = array(), $keyfield = '', $silent=false) 
    {
		$data = array();
		$query = self::query($sql, $arg, $silent, false);
		while ($row = self::$db->fetch_array($query)) {
			if ($keyfield && isset($row[$keyfield])) {
				$data[$row[$keyfield]] = $row;
			} else {
				$data[] = $row;
			}
		}
		self::$db->free_result($query);
		return $data;
	}
    
	public static function result($resourceid, $row = 0) 
    {
		return self::$db->result($resourceid, $row);
	}
    
	public static function result_first($sql, $arg = array(), $silent = false) 
    {
		$res = self::query($sql, $arg, $silent, false);
		$ret = self::$db->result($res, 0);
		self::$db->free_result($res);
		return $ret;
	}
    
	public static function query($sql, $arg = array(), $silent = false, $unbuffered = false) 
    {
		if (!empty($arg)) {
			if (is_array($arg)) {
				$sql = self::format($sql, $arg);
			} elseif ($arg === 'SILENT') {
				$silent = true;

			} elseif ($arg === 'UNBUFFERED') {
				$unbuffered = true;
			}
		}
		self::checkquery($sql);

		$ret = self::$db->query($sql, $silent, $unbuffered);
		if (!$unbuffered && $ret) {
			$cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
			if ($cmd === 'SELECT') {

			} elseif ($cmd === 'UPDATE' || $cmd === 'DELETE') {
				$ret = self::$db->affected_rows();
			} elseif ($cmd === 'INSERT') {
				$ret = self::$db->insert_id();
			}
		}
		return $ret;
	}
    
	public static function num_rows($resourceid) 
    {
		return self::$db->num_rows($resourceid);
	}
    
	public static function affected_rows() 
    {
		return self::$db->affected_rows();
	}
    
	public static function free_result($query) 
    {
		return self::$db->free_result($query);
	}
    
	public static function error() 
    {
		return self::$db->error();
	}
    
	public static function errno() 
    {
		return self::$db->errno();
	}
    
	public static function checkquery($sql) 
    {
		return $sql;
	}
}
