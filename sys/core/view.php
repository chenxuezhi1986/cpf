<?php if (!defined('SYSPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */
 
class View_Core {
    
    static $instance;
    protected $dir = './app/views';
    protected $exts = '.php';
    protected $cached = false;
    
    function __construct()
    {
        $this->_init();
    }
    
    private function _init()
    {
		$file = APPPATH . 'configs/view.php';
		if(is_file($file)) {
            $config = include ($file);
            foreach ($config as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
		}
    }

    public function display($filename, $data = array(), $cached = false)
    {
        $view = $this->dir.'/'.$filename.$this->exts;
        $this->cached = $cached;
        
        if (defined('C_DEBUG') && C_DEBUG) {
            static $views = array();
            array_push($views, $view);
            Kernel::$debug_info['views'] = $views;
        }
        
        if($this->cached){
            ob_start();
        }
        
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){  
                $$k = $v;
            }
        }
        
        include($view);
        
        if($this->cached){
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }
    
    public static function get_instance()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        self::$instance = new self;
        return self::$instance;
    }
}
