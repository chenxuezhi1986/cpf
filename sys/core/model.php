<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Model_Core {
    static $objs = array();
    static $instance;
    
    protected $table;

    public function __construct()
    {
        
    }

    //控制器快捷方法映射如：$this->load
    public function __get($name) {
        $classs['db'] = 'Db_Core';
        $classs['tpl'] = 'Template_Core';
        $classs['load'] = 'Loader_Core';
        $classs['param'] = 'Param_Core';
        
        if(isset($classs[$name])){
            if(!isset(self::$objs[$name])){
                self::$objs[$name] = new $classs[$name];
            }
            return self::$objs[$name];
        }
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
