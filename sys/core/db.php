<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Db_Core {
    protected $db;
    protected $config = array();

    public function __construct($config_name='DEFAULT')
    {
        $this->initialize($config_name);
    }
    
    public function initialize($config_name)
    {
        //加载配置文件
        $this->_load_config($config_name);
        
        if(!empty($this->config['dbdriver'])){
            $driver = ucfirst($this->config['dbdriver']).'_Driver';
    		$this->db = new $driver; //实例化驱动
    		$this->db->set_config($this->config); //设置配置
    		$this->db->connect(); //连接数据库
        }
    }

    private function _load_config($config_name)
    {
        $file = APPPATH . 'config/database.php';
        if (is_file($file)) {
            $configs = include($file);
            $this->config = $configs[$config_name];
        } else {
            error('Not found database config file : ' . $file);
        }
    }
    
    public function get_config()
    {
        return $this->config;
    }
    
	public function insert_id() 
    {
		return $this->db->insert_id();
	}

	public function fetch($resourceid, $type = MYSQL_ASSOC) 
    {
		return $this->db->fetch_array($resourceid, $type);
	}

	public function fetch_first($sql, $arg = array(), $silent = false) 
    {
		$res = $this->query($sql, $arg, $silent, false);
		$ret = $this->db->fetch_array($res);
		$this->db->free_result($res);
		return $ret ? $ret : array();
	}

	public function fetch_all($sql, $arg = array(), $keyfield = '', $silent=false) 
    {
		$data = array();
		$query = $this->query($sql, $arg, $silent, false);
		while ($row = $this->db->fetch_array($query)) {
			if ($keyfield && isset($row[$keyfield])) {
				$data[$row[$keyfield]] = $row;
			} else {
				$data[] = $row;
			}
		}
		$this->db->free_result($query);
		return $data;
	}
    
	public function result($resourceid, $row = 0) 
    {
		return $this->db->result($resourceid, $row);
	}
    
	public function result_first($sql, $arg = array(), $silent = false) 
    {
		$res = $this->query($sql, $arg, $silent, false);
		$ret = $this->db->result($res, 0);
		$this->db->free_result($res);
		return $ret;
	}
    
	public function query($sql, $arg = array(), $silent = false, $unbuffered = false) 
    {
    	$ret = array();
		if (!empty($arg)) {
			if (is_array($arg)) {
				$sql = self::format($sql, $arg);
			} elseif ($arg === 'SILENT') {
				$silent = true;

			} elseif ($arg === 'UNBUFFERED') {
				$unbuffered = true;
			}
		}
        $this->_checkquery($sql);

		$query = $this->db->query($sql, $silent, $unbuffered);
		if (!$unbuffered && $query) {
			$cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
			if ($cmd === 'SELECT') {
				while ($row = $this->db->fetch_array($query)){
					$ret[] = $row;
				}
			} elseif ($cmd === 'UPDATE' || $cmd === 'DELETE') {
				$ret = $this->db->affected_rows();
			} elseif ($cmd === 'INSERT') {
				$ret = $this->db->insert_id();
			}
		}
		return $ret;
	}
    
	public function num_rows($resourceid) 
    {
		return $this->db->num_rows($resourceid);
	}
    
	public function affected_rows() 
    {
		return $this->db->affected_rows();
	}
    
	public function free_result($query) 
    {
		return $this->db->free_result($query);
	}
    
	public function error() 
    {
		return $this->db->error();
	}
    
	public function errno() 
    {
		return $this->db->errno();
	}
    
	private function _checkquery($sql) 
    {
		return $sql;
	}
}
