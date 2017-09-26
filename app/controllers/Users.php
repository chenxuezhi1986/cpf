<?php

/**
 * @author chenxuezhi
 * @copyright 2017
 */

class Users_Controller extends Controller_Core{
    
    public function index()
    {
    	/*$sql = "select * from  c_user";
        $data = $this->db->query($sql, 'username');
        
        $this->load->model('welcome_model');
        $data['title'] = 'ooo';
        $data['content'] = $this->welcome_model->say();
        $data['cc'] = '111';
        $this->display('welcome', $data);*/
    }
    
    public function register()
    {
        
    }
    
    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $users_model = Users_Model::get_instance();
        if(!$users = $users_model->get_user_info($username)){
            $data = array('err'=>1, 'msg'=>'用户名不存在');
            $this->json($data);
        }else if($users['password'] != md5(md5($password).$users['salt'])){
            $data = array('err'=>2, 'msg'=>'用户名或密码不正确');
            $this->json($data);
        }else{
            $str = $users['username'].','.$users['password'];
            setcookie('auth', authcode($str, 'ENCODE'));
        }
    }
}