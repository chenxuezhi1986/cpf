<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Model_Base
{
    
    protected $db_server_id = 1; //Êý¾Ý¿âÅäÖÃºÅ£¬Ä¬ÈÏÑ¡Ôñ1ºÅÅäÖÃ
    protected $table;
    protected $db;
    
    public function __construct()
    {
        $this->db = Database_Base::get_instance();
        $this->db->set_cur_db_config($this->db_server_id);
    }
    
    protected function get_hash_table($table, $field) {
        $str = crc32($field);
        if($str < 0){   
            $hash = "0".substr(abs($str), 0, 1);   
        }else{   
            $hash = substr($str, 0, 2);   
        }
        return $table."_".$hash;
    }
    
    protected function add($data)
    {
        $d['table'] = $this->table;
        $d['data'] = $data;
        return $d;
    }   
    
    protected function delete()
    {
        
    }
    
    protected function update()
    {
        
    }
    
    protected function find()
    {
        
    }
}
