<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Controller extends Controller_Base{
    
    public function index()
    {
        load::model('welcome_model');
        $data['title'] = '欢迎使用CPF框架';
        $data['content'] = $this->welcome_model->hi();
        tpl::init()->display('welcome', $data);
    }
}