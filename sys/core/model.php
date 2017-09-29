<?php if (!defined('SYSPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Model_Core {
    static $objs = array();
    static $instance;
    
    protected $table;
    protected $pk = 'id';

    public function __get($var)
    {
        return Controller_Core::$instance->$var;
    }

    protected function find($id)
    {
        $sql = "SELECT * FROM $this->table WHERE $this->pk = '$id'";
        return $this->db->rows($sql);
    }
    
    public static function get_instance()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        $class_name = get_called_class(); //获取子类类名，需要PHP>=5.3.0才支持
        self::$instance = new $class_name();
        return self::$instance;
    }
}
