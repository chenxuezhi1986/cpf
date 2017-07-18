<?php if (!defined('SYSPATH')) exit('Access Denied');

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
        require_once (SYSPATH . 'core/common.php'); //函数库
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

            $paths['core'] = SYSPATH . 'core/';
            $paths['driver'] = SYSPATH . 'driver/';
            $paths['lib'] = APPPATH . 'lib/';
            $paths['model'] = APPPATH . 'model/';
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

    public static function debug_output()
    {
        $html = '';
        foreach(Kernel::$debug_info as $key=>$val){ 
            $html .= "<h2>$key</h2><pre>\r\n".var_export($val, true).'</pre>';
        }
$contents = <<<EOT
<style type="text/css">#debug_info h1,h2{font-family:sans-serif;font-weight:400;font-size:.9em;margin:1px;padding:0}#debug_info h1{margin:0;text-align:left;padding:2px;background-color:#f0c040;color:#000;font-weight:700;font-size:1.2em}#debug_info h2{background-color:#9B410E;color:#fff;text-align:left;font-weight:700;padding:2px;border-top:1px solid #000}#debug_info{margin-top:20px}#debug_info pre{background:#f0ead8;margin:0;padding:5px}
</style><div id="debug_info"><h1>Debug Console</h1>{$html}</div>
EOT;
    echo $contents;
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
            self::debug_output();
        }
    }
}
