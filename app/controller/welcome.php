<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Controller extends Controller_Core{
    
    public function index()
    {
    	$sql = "INSERT INTO USER(username) VALUES('chenxuezhi')";
        $this->db->query($sql);
        //Load::model('welcome_model');
        $data['title'] = 'ooo';
        $data['content'] = $this->welcome_model->hi();
        //tpl::display('welcome', $data);
        print_r($data);
    }
}