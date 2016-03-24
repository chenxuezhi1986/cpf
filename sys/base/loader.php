<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Loader_Base
{
    /**
     * 载入扩展类
     * @param string $class 类名
     * @param array $config 配置参数
     * @return obj
     */
    public function lib($class, $config = array())
    {
        static $_classes = array();

        if (isset($_classes[$class])) {
            return $_classes[$class];
        }

        $filename = APPPATH . 'lib/' . $class . '.php';
        if(is_file($filename)){
            $class_name = $class . '_extlib';
        }else{
            $filename = BASEPATH . 'lib/' . $class . '.php';
            $class_name = $class . '_lib';
        }
        
        if(!is_file($filename)){
            error('Not found file '.$filename);
        }
        
        if (class_exists($class_name) === false) {
            error('Class \'' . $class_name . '\' not found ');
        }else{            
            $_classes[$class] = new $class_name($config);
            Kernel::__this()->$class = $_classes[$class];
            return $_classes[$class];
        }
    }

    /**
     * 载入模型类
     * @param string $model 模型
     * @param string $name  变量名称
     * @return obj
     */
    public function model($model, $name = '', $db_conn = false)
    {
        static $_classes = array();
        
        if ($pos = strpos($model, '/')) {
            $class_name = substr($model, $pos + 1);
        }else{
            $class_name = $model;
        }

        if ($name == '') {
            $name = $class_name;
        }

        if (isset($_classes[$class_name])) {
            return $_classes[$class_name];
        }

        $filename = APPPATH . 'models/' . $model . '.php';
        if (is_file($filename)) {
            if (class_exists($class_name) === false) {
                error('Class \'' . $class_name . '\' not found ');
            } else {
                $_classes[$class_name] = new $class_name;
                Kernel::__this()->$name = $_classes[$class_name];
                return $_classes[$class_name];
            }
        } else {
            error('Not found file ' . $filename);
        }
    }
}
