<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */
 
require (BASEPATH . '/smarty/Smarty.class.php');
class Template_Base extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        $this->template_dir = APPPATH . 'templates'; //设置模版目录
        $this->compile_dir = './data/smarty/templates_compile'; //设置编译目录
        $this->caching = false; //是否使用缓存
        $this->cache_lifetime = 60;  //缓存时间
        $this->cache_dir = './data/smarty/templates_cache'; //缓存文件夹
        $this->left_delimiter = "<{";
        $this->right_delimiter = "}>";
    }
}
