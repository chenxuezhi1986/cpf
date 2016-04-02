<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */
 
//类别名
class_alias('Template_Base','tpl',false);
 
class Template_Base {
    
    static $_instance;
    private $tpl_dir = './app/templates';
    private $tpl_cache_dir = './app/data/templates_cache';
    private $tpl_compile_dir = './app/data/templates_compile';
    private $tpl_is_cache = false;
    private $tpl_cache_time = 60;
    private $tpl_left = "<{";
    private $tpl_right = "}>";
    private $tpl_exts = '.html';
    
    private $pattern = array();
    private $replacement = array();
    
    private $tpl;
    private $tpl_cache_key;
    private $tpl_cache_content;
    
    function __construct()
    {
        $this->_initialize();
    }
    
    private function _initialize()
    {
        $this->tpl_cache_key = sha1($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
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
        
        if(!is_dir($this->tpl_cache_dir)){
            @mkdir($this->tpl_cache_dir);
        }
        
        if(!is_dir($this->tpl_compile_dir)){
            @mkdir($this->tpl_compile_dir);
        }
    }
    
    public function set_tpl_cache($tpl_is_cache)
    {
        $this->tpl_is_cache = $tpl_is_cache;
    }
    
    public function display($tpl, &$data=array(), $is_return = false)
    {
        $this->tpl = $this->tpl_dir.'/'.$tpl.$this->tpl_exts;
        
        if (defined('C_DEBUG') && C_DEBUG) {
            static $tpls = array();
            array_push($tpls, $this->tpl);
            Kernel::$debug_info['template'] = $tpls;
        }
        
        //加载模板
        $this->_load_tpl($tpl,$data);

    }
    
    /**
     * 加载模板
     * @param $tpl 模板名称
     * @return void
     * */
    private function _load_tpl($tpl, &$data=array(), $is_return = false)
    {
        if(is_file($this->tpl)) {
            
            if(count($data) > 0 ){
                extract($data);
            }

            //是否开启缓存
            if($this->tpl_is_cache === true){
                $cache_tpl = $this->tpl_cache_dir.'/'.$this->tpl_cache_key.'.php'; 
                if(is_file($cache_tpl)) {
                    $cur_time = time(); //当前时间
                    $filectime = filemtime($cache_tpl); //文件上次修改时间
                    if($cur_time - $filectime < $this->tpl_cache_time){
                        require ($cache_tpl);
                        return;
                    }
                }
            }

            //缓存开始
            if($this->tpl_is_cache === true){
                ob_start();
            }
            
            //编译模板
            $__compile_tpl = $this->tpl_compile_dir.'/'.md5_file($this->tpl).'.php';
            
            if(is_file($__compile_tpl)) {
                include($__compile_tpl);
            }else{
                if($__fp = @fopen($this->tpl, 'r')){
                    $__tpl_content = fread($__fp, filesize($this->tpl));
                    fclose($__fp);
                    //解析模板
                    $__content = $this->_parse_tpl($__tpl_content);
                    
                    //写入编译文件
                    if($__fp = @fopen($__compile_tpl, 'w')) {
                        fputs($__fp, $__content);
                        fclose($__fp);
                        include($__compile_tpl);
                    }
                }
            }

            //缓存结束
            if($this->tpl_is_cache === true) {
                $this->tpl_cache_content = ob_get_flush();
            }
        }else{
            error('Not found template '.$this->tpl);
        }
    }
    
    private function _parse_tpl($tpl_content)
    {
        $pattern = array(
            "#{$this->tpl_left}\\$(\w+){$this->tpl_right}#", //变量
            "#{$this->tpl_left}\\$(\w+)(\[.*\]+){$this->tpl_right}#", //数组
            "#{$this->tpl_left}include\s+file\s*=\s*('.*'|\".*\"){$this->tpl_right}#",
            "#{$this->tpl_left}foreach\s+\\$(\w+)\s+as\s+(.+?){$this->tpl_right}#",
            "#{$this->tpl_left}/foreach{$this->tpl_right}#",
            "#{$this->tpl_left}if(.+?){$this->tpl_right}#",
            "#{$this->tpl_left}else{$this->tpl_right}#",
            "#{$this->tpl_left}elseif(.+?){$this->tpl_right}#",
            "#{$this->tpl_left}/if{$this->tpl_right}#",
        );
        
        $replacement = array(
            '<?php echo $\1;?>',
            '<?php echo $\1\2;?>',
            '<?php $this->display(\1);?>',
            "<?php \$i=0;foreach($\\1 as \\2) : \$i++; ?>",
            '<?php endforeach;?>',
            '<?php if \1 : ?>',
            '<?php else:?>',
            '<?php elseif \1 : ?>',
            '<?php endif;?>',
        );
        
        //合并配置文件标签替换数组
        $count_pattern = count($this->pattern);
        $count_replacement = count($this->replacement);
        
        if($count_pattern > 0 && $count_replacement > 0){
            if($count_pattern == $count_replacement){
                $pattern = array_merge($pattern, $this->pattern);
                $replacement = array_merge($replacement, $this->replacement);
            }
        }
  
        $tpl_content = preg_replace($pattern, $replacement, $tpl_content);
        return $tpl_content;
    }
    
    function __destruct()
    {
        //保存缓存
        if($this->tpl_is_cache === true){
            $cache_tpl = dirname(__FILE__).'/../../'.$this->tpl_cache_dir.'/'.$this->tpl_cache_key.'.php';
            if(!is_file($cache_tpl)){
                if($fp = fopen($cache_tpl, 'w')) {
                    fputs($fp, $this->tpl_cache_content);
                    fclose($fp);
                }                
            }
        }
    }
    
    public static function init()
    {
        return self::get_instance();
    }
    
    public static function get_instance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}
