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
    private $tpl_vars = array();
    
    function __construct()
    {
        $this->_initialize();
    }
    
    private function _initialize()
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
        
        if(count($data) > 0 ){
            $this->tpl_vars = $data;
        }
        
        //加载模板
        $this->load_tpl($tpl);

    }
    
    /**
     * 加载模板
     * @param $tpl 模板名称
     * @return void
     * */
    private function load_tpl($tpl, $is_return = false)
    {
        if(is_file($this->tpl)) {

            //是否开启缓存
            if($this->tpl_is_cache === true){
                $sha1_name = sha1($this->tpl); //模板路径sha1值
                $cache_tpl = $this->tpl_cache_dir.'/'.$tpl.'_'.$sha1_name.'.php';
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
            $compile_tpl = $this->tpl_compile_dir.'/'.md5_file($this->tpl).'.php';
            
            if(is_file($compile_tpl)) {
                require($compile_tpl);
            }else{
                if($fp = @fopen($this->tpl, 'r')){
                    $tpl_content = fread($fp, filesize($this->tpl));
                    fclose($fp);
                    //解析模板
                    $content = $this->parse_tpl($tpl_content);
                    
                    //写入编译文件
                    if($fp = @fopen($compile_tpl, 'w')) {
                        fputs($fp, $content);
                        fclose($fp);
                        require($compile_tpl);
                    }
                }
            }

            //缓存结束
            if($this->tpl_is_cache === true) {
                $tpl_cache_content = ob_get_clean();               

                //写入缓存文件
                if($fp = @fopen($cache_tpl, 'w')) {
                    fputs($fp, $tpl_cache_content);
                    fclose($fp);
                    require($cache_tpl);
                }
            }
        }else{
            error('Not found template '.$this->tpl);
        }
    }
    
    private function parse_tpl($tpl_content)
    {
        $pattern = array(
            "#{$this->tpl_left}\\$(\w*){$this->tpl_right}#", //解析变量
            "#{$this->tpl_left}\\include file\s?=\s?('.*'|\".*\"){$this->tpl_right}#" //<{include file = ''}>
        );
        
        $replacement = array(
            '<?php echo $this->tpl_vars[\'\1\'];?>',
            '<?php $this->display(\1);?>'
        );
        
        //合并标签替换
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
