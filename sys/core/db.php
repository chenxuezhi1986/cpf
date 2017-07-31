<?php if (!defined('SYSPATH'))
    exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Db_Core
{
    protected $db;
    protected $config = array();

    public function __construct($config_name = 'DEFAULT')
    {
        $this->initialize($config_name);
    }

    public function initialize($config_name)
    {
        //加载配置文件
        $this->_load_config($config_name);

        if (!empty($this->config['dbdriver'])) {
            $driver = ucfirst($this->config['dbdriver']) . '_Driver';
            $this->db = new $driver; //实例化驱动
            $this->db->set_config($this->config); //设置配置
            $this->db->connect(); //连接数据库
        }
    }

    private function _load_config($config_name)
    {
        $com_file = './../configs/database.php';
        $app_file = APPPATH . 'configs/database.php';
        if (is_file($com_file)) {
            $configs = include ($com_file);
            $this->config = $configs[$config_name];
        } else if (is_file($app_file)) {
            $configs = include ($app_file);
            $this->config = $configs[$config_name];
        } else {
            error('The database configuration file was not found');
        }
    }

    public function get_config()
    {
        return $this->config;
    }

    public function transaction()
    {
        $this->db->query('START TRANSACTION');
    }

    public function rollback()
    {
        $this->db->query('ROLLBACK');
    }

    public function commit()
    {
        $this->db->query('COMMIT');
    }

    public function rows($sql, $arg = array())
    {
        $res = $this->db->query($sql, $arg);
        $ret = $this->db->fetch_array($res);
        $this->db->free_result($res);
        return $ret ? $ret : array();
    }

    public function insert($table, $data)
    {
        $fields = array();
        $valus = array();
        foreach ($data as $k => $v) {
            $fields[] = $k;
            $valus[] = "'$v'";
        }
        $fields && $fields = implode(',', $fields);
        $valus && $valus = implode(',', $valus);
        $sql = "INSERT INTO $table($fields) VALUES ($valus)";
        return $this->query($sql);
    }

    public function update($table, $data, $Wsql = '')
    {
        $sets = array();
        foreach ($data as $k => $v) {
            $sets[] = "$k = '$v'";
        }
        $sets && $sets = implode(',', $sets);
        $Wsql && $Wsql = 'WHERE ' . $Wsql;
        $sql = "UPDATE $table SET $sets $Wsql";
        return $this->query($sql);
    }

    public function delete($table, $Wsql)
    {
        $sql = "DELETE FROM $table WHERE $Wsql";
        return $this->query($sql);
    }

    public function query($sql, $arg = array(), $keyfield = '')
    {
        if (!empty($arg) && is_array($arg)) {
            $sql = $this->_format($sql, $arg);
        }

        $this->_checkquery($sql);

        $data = array();
        $query = $this->db->query($sql);
        if ($query) {
            $cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
            if ($cmd === 'SELECT') {
                while ($row = $this->db->fetch_array($query)) {
                    if ($keyfield && isset($row[$keyfield])) {
                        $data[$row[$keyfield]] = $row;
                    } else {
                        $data[] = $row;
                    }
                }
            } elseif ($cmd === 'UPDATE' || $cmd === 'DELETE') {
                $data = $this->db->affected_rows();
            } elseif ($cmd === 'INSERT') {
                $data = $this->db->insert_id();
            }
        }
        $this->db->free_result($query);
        return $data;
    }

    private function _format($sql, $arg = array())
    {
        return $sql;
    }

    private function _checkquery($sql)
    {
        return $sql;
    }
}
