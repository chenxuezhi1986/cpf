<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Test_Controller extends Controller_Base {
    
    public function index()
    {
        $this->load->model('test_model');
        $this->tpl->assign('data','12333');
        $this->tpl->assign('name','我是谁！？');
        $this->tpl->assign('user',$this->test_model->say());
        $this->tpl->display('index.html');
    }
    
    public function test()
    {
        //echo $this->param->get('uid','float');
        echo $this->param->uri_string(0);
        $this->tpl->caching = false;
        if($this->tpl->isCached('test.html') === false){
            $this->load->model('test_model');
            $this->load->model('user/user_model_view');
            $this->tpl->assign('title','12333');
            $this->tpl->assign('name','我是谁！？');
            $this->tpl->assign('user',$this->test_model->say());    
        }
        $this->tpl->display('test.html');
    }
}