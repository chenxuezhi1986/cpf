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
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if(!preg_match('//',$username)){
            $data = array('err'=>1, 'msg'=>'�û���������');
            $this->json($data);
        }
    }
    
    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $users_model = Users_Model::get_instance();
        if(!$users->get_user_info($username)){
            $data = array('err'=>1, 'msg'=>'�û���������');
            $this->json($data);
        }else if($users['password'] != md5(md5($password).$users['salt'])){
            $data = array('err'=>2, 'msg'=>'�û��������벻��ȷ');
            $this->json($data);
        }else{
            //���µ�½ʱ��
            $this->db->update(
                'users',
                array('last_login_time'=>time()),
                "username = '$username'"
            );
            
            $str = $users['uid'].','.$users['username'].','.$users['password'].','.$users['last_login_time'];
            setcookie('auth', authcode($str, 'ENCODE'));
        }
    }
}