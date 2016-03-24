<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Test_Model extends Model_Base{
    
    public $db_server_id = 1;
    
    public function __construct()
    {
        $this->table = 'bus_users';
    }
    
    public function say()
    {
        $opt['cached'] = true;
        $opt['select'] = '*';
        $opt['from'] = 'user';
        //$opt['where'] = 'id in(1,2,3) and userid = 123';
        //$opt['order_by'] = 'id desc';
        $opt['limit'] = '1,2';
        //$this->db->cache_off();
        return $this->db->get($opt);
    }
    
    public function test($data)
    {
        $d = $this->add($data);
        print_r($d);
    } 
    
    public function say1()
    {
        //$this->db->set_cur_db_config(1);
        //return $this->db->get_cur_db_config();
        //$data['id'] = 112;
        //$data['username'] = 'test1986';
        return $this->db->delete('user','id=3');
    }
}