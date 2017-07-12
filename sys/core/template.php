<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */
 
class Template_Core {
    
    static $_instance;
    static $tpl_dir = './app/templates';
    static $tpl_exts = '.php';
    
    function __construct()
    {
        $this->_init();
    }
    
    public static function load_config()
    {
        $file = APPPATH . 'config/template.php';
        if (is_file($file)) {
            $config = include($file);
            if (isset($template) && is_array($template) > 0) {
                foreach ($template as $key => $val) {
                    if (isset($this->$key)) {
                        $this->$key = $val;
                    }
                }
            }
        }
    }

    public function display($filename, $cached = false)
    {   
        $tpl = self::$tpl_dir.'/'.$filename.self::$tpl_exts;
        
        if (defined('C_DEBUG') && C_DEBUG) {
            static $tpls = array();
            array_push($tpls, $tpl);
            Kernel::$debug_info['template'] = $tpls;
        }
        
        if($cached){
            ob_start();
        }
        
        include($tpl);
        
        if($cached){
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }
}

class Tpl extends Template_Core{
    
}
