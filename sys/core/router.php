<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Router_Core {
    static $_instance;
    const URL_PHPINFO = 1;
    const URL_GET = 0;
    
    private $url_mode = 1;
    private $deft_ctl = '';
    private $deft_act = '';
    private $event_map = array();

    function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $filename = APPPATH . 'config/router.php';
        if (is_file($filename)) {
            include ($filename);
            if (isset($router) && is_array($router) > 0) {
                foreach ($router as $key => $val) {
                    if (isset($this->$key)) {
                        $this->$key = $val;
                    }
                }
                unset($router);
            }
        }
    }

    public function get_event()
    {
        $event['ctl'] = '';
        $event['act'] = '';
        $URI = $this->_get_uri();
        if (strpos($URI, '/')) {
            $data = explode('/', $URI);
        } else {
            $data = array($URI, $this->deft_act);
        }
        list($event['ctl'], $event['act']) = $data;
        return $event;
    }

    private function _get_uri()
    {
        $uri = '';
        switch($this->url_mode) {            
            case Router_Core::URL_GET:
                $uri = $this->_get_to_ctl($_GET);
                break;
                
            case Router_Core::URL_PHPINFO:
                $pathinfo = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/') : NULL;
                $this->_XSS($pathinfo); //防止XSS攻击
                $uri = $this->_pathinfo_to_ctl($pathinfo);
                break;
        }
        
        return $uri;
    }

    private function _get_to_ctl($params)
    {
        $ctl = !empty($params['c']) ? $params['c'] : $this->deft_ctl;
        $act = !empty($params['a']) ? $params['a'] : $this->deft_act;
        $event = $ctl . '/' . $act;
        return $event;
    }

    private function _pathinfo_to_ctl($pathinfo)
    {
        $uri = empty($pathinfo) ? $this->deft_ctl : $pathinfo;
        return $this->_uri_map($uri);
    }

    //URI重定向
    private function _uri_map($uri)
    {
        if (count($this->event_map) > 0) {
            foreach ($this->event_map as $key => $val) {
                if (preg_match('#^' . $key . '$#', $uri)) {
                    $uri = preg_replace('#^' . $key . '$#', $val, $uri);
                    break;
                }
            }
        }
        return $uri;
    }

    private function _XSS($str)
    {
        if ($str && !preg_match("/^[a-z_0-9\/]+$/i", $str)) {
            exit("不能包含中文和特殊字符！");
        }
        return $str;
    }

    public static function get_instance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}
