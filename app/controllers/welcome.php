<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Controller extends Controller_Core{
    
    public function index()
    {
    	$sql = "select * from  c_user";
        $data = $this->db->query($sql, 'username');
        
        $model = welcomme_model::get_instance();
        
        $this->load->model('welcome_model');
        $data['title'] = 'ooo';
        $data['content'] = $this->welcome_model->say();
        $data['cc'] = '111';
        $this->display('welcome', $data);
    }
}