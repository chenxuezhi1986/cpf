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
        echo '<pre>';
        print_r($data);
        $this->load->model('welcome_model');
        //$data['title'] = 'ooo';
        $data['content'] = $this->welcome_model->say();
        //tpl::display('welcome', $data);
        //print_r($data);
    }
}