<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */
 
class Template_Core {
    
    static $_instance;
    private $tpl_dir = './app/templates';
    private $tpl_exts = '.php';
    
    function __construct()
    {
        $this->_init();
    }
    
    private function _init()
    {
        $filename = APPPATH . 'config/template.php';
        if (is_file($filename)) {
            include ($filename);
            if (isset($template) && is_array($template) > 0) {
                foreach ($template as $key => $val) {
                    if (isset($this->$key)) {
                        $this->$key = $val;
                    }
                }
                unset($template);
            }
        }
    }

    public function display($filename, &$data=array(), $cached = false)
    {
        if(count($data) > 0){
            extract($data);
        }
        
        $this->tpl = $this->tpl_dir.'/'.$filename.$this->tpl_exts;
        
        if (defined('C_DEBUG') && C_DEBUG) {
            static $tpls = array();
            array_push($tpls, $this->tpl);
            Kernel::$debug_info['template'] = $tpls;
        }
        
        if($cached){
            ob_start();
        }
        
        include($this->tpl);
        
        if($cached){
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

    }
    
    public static function get_instance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}

class Tpl extends Template_Core{
    
}
