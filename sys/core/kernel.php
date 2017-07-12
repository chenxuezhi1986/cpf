<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Kernel {
    public static $debug_info = array();

    private static function _init()
    {
        define('C_TIMESTAMP', $_SERVER['REQUEST_TIME']);
        define('C_TIMEMICRO', self::_microtime());

        spl_autoload_register(array('Kernel', '_autoload')); //注册自动加载函数
        set_error_handler(array('Error_Core', 'error_handler')); //自定义错误方法
        require_once (BASEPATH . 'core/common.php'); //函数库
    }

    public static function &__this()
    {
        return Controller_Core::get_instance();
    }

    /**
     * 解析控制器
     */
    private static function _parse_ctl()
    {
        $router = Router_Core::get_instance();
        $event = $router->get_event();
        $filename = APPPATH . 'controller/' . $event['ctl'] . '.php';
        if (is_file($filename)) {
            $class_name = $event['ctl'] . '_controller';
            if (class_exists($class_name) === false) {
                error_404();
            } else {
                $obj = new $class_name();
                if (!method_exists($obj, $event['act'])) {
                    error_404();
                }
                $obj->$event['act']();
                unset($obj);
            }
        } else {
            if (isset($_SERVER['PATH_INFO'])) {
                error_404();
            } else {
                error('Not found default controller');
            }
        }
    }

    /**
     * 微秒时间戳
     */
    private static function _microtime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 自动加载
     */
    private static function _autoload($class)
    {
        $class = strtolower($class);

        if ($pos = strrpos($class, '_')) {
            $name = substr($class, 0, $pos);
            $exts = substr($class, $pos + 1);

            if ($exts == 'model') $name = $class;

            $paths['core'] = BASEPATH . 'core/';
            $paths['lib'] = BASEPATH . 'lib/';
            $paths['driver'] = BASEPATH . 'driver/';
            $paths['model'] = APPPATH . 'models/';
            $paths['controller'] = APPPATH . 'controller/';

            if (isset($paths[$exts])) {
                $file = $paths[$exts] . $name . '.php';
                if (defined('C_DEBUG') && C_DEBUG) {
                    self::$debug_info[$exts][] = $file;
                }
                require ($file);
            }
        }
    }

    /**
     * 运行入口
     */
    public static function run()
    {
        self::_init(); //加载文件
        self::_parse_ctl(); //解析控制器

        //Debug模式
        if (defined('C_DEBUG') && C_DEBUG) {
            //内存消耗单位转换
            $unit = array('b','kb','mb','gb','tb','pb');
            $memory_size = memory_get_usage(true);
            $memory_size = @round($memory_size/pow(1024,($i=floor(log($memory_size,1024)))),2).' '.$unit[$i];

            self::$debug_info['memory_usage'] = $memory_size;
            self::$debug_info['runtime'] = self::_microtime() - C_TIMEMICRO;
            require(BASEPATH.'core/debug.php');
        }
    }
}
