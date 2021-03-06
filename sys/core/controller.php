<?php if (!defined('SYSPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Controller_Core
{
    static $objs = array();
    static $instance;

    public function __construct()
    {
        self::$instance = &$this;
    }

    public static function &get_instance()
    {
        return self::$instance;
    }
    
    //控制器快捷方法映射如：$this->load
    public function __get($name) {
        $classs['db'] = 'Db_Core';
        $classs['load'] = 'Loader_Core';
        $classs['params'] = 'Params_Core';

        if(isset($classs[$name])){
            if(!isset(self::$objs[$name])){
                self::$objs[$name] = new $classs[$name];
            }
            return self::$objs[$name];
        }
    }
    
    protected function display($filename, $data = array(), $cached = false)
    {
        $view = View_Core::get_instance();
        return $view->display($filename, $data, $cached);
    }
    
    protected function json($content, $options = 0)
    {
        echo json_encode($content, $options);
        exit(0);
    }
}
