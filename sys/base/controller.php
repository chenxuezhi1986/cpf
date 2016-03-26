<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Controller_Base
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

    
    //��������ݷ���ӳ���磺$this->load
    public function __get($name) {
        $classs['tpl'] = 'Template_Base';
        $classs['load'] = 'Loader_Base';
        $classs['param'] = 'Param_Base';
        
        if(isset($classs[$name])){
            if(!isset(self::$objs[$name])){
                self::$objs[$name] = new $classs[$name];
            }
            return self::$objs[$name];
        }        
    }
    
    //�෴��
    public static function class_alias(){
        class_alias('loader_base','load');
    }
}
