<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Controller extends Controller_Core{
    
    public function index()
    {
    	$sql = "select * from  user";
        $data = $this->db->query($sql, 'username');
        $this->load->model('welcome_model');
        $data['title'] = 'ooo';
        $data['content'] = $this->welcome_model->say();
        $data['cc'] = '111';
        $this->view->display('welcome', $data);
    }
}