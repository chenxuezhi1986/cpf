<?php if (!defined('SYSPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Controller_Core
{
    static $objs = array();
    const TOKEN = 'API';
    
    public function __construct()
    {
        $this->_check_signature();
    }
    
    //控制器快捷方法映射如：$this->load
    public function __get($name) {
        $classs['db'] = 'Db_Core';
        $classs['load'] = 'Loader_Core';
        $classs['params'] = 'Params_Core';

        if(isset($classs[$name])){
            if(!isset(self::$objs[$name])){
                self::$objs[$name] = new $classs[$name];
            }
            return self::$objs[$name];
        }
    }
     
    private function _check_signature()
    {
        $echostr = $_GET['echostr'];
        $sign = $_GET['sign'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token = self::TOKEN;
        $tmp_arr = array($token, $timestamp, $nonce);
        sort($tmp_arr, SORT_STRING);
        $tmp_str = implode($tmp_arr);
        $tmp_str = sha1($tmp_str);
        if($tmp_str != $sign){
            throw new Exception('签名验证失败');
        }
    }
    
    protected function json($content, $options = 0)
    {
        echo json_encode($content, $options);
        exit(0);
    }
}
