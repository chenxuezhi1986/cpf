<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Controller extends Controller_Base{
    
    public function index()
    {
        $this->load->model('test_model');
        $data['user'] = 'test';
        $data['pass'] = 123;
        $this->test_model->test($data);
    }
}