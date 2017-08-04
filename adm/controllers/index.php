<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Index_Controller extends Common_Controller {
    
    public function index()
    {
    	/*$sql = "select * from  user";
        $data = $this->db->query($sql, 'username');
        $this->load->model('welcome_model');
        $data['title'] = 'ooo';
        $data['content'] = $this->welcome_model->say();
        $data['cc'] = '111';
		*/
		$data = array();
        $this->display('index', $data);
    }
}