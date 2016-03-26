<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Controller extends Controller_Base{
    
    public function index()
    {
        load::model('welcome_model');
        //$this->load->model('welcome_model');
        $content = $this->welcome_model->hi();
        $this->tpl->assign('content',$content);
        $this->tpl->assign('title','欢迎使用CPF框架~！');
        $this->tpl->display('welcome.html');
    }
}