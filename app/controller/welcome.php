<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Controller extends Controller_Core{
    
    public function index()
    {
        load::model('welcome_model');
        $data['title'] = 'ooo';
        $data['content'] = $this->welcome_model->hi();
        tpl::display('welcome', $data);
    }
}