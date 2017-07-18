<?php if (!defined('SYSPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Param_Core {
    public function get($var, $type = '')
    {
        $this->_settype($_GET[$var], $type);
        return $_GET[$var];
    }

    public function post($var, $type = '')
    {
        $this->_settype($_GET[$var], $type = '');
        return $_POST[$var];
    }

    public function cookie($var, $type = '')
    {
        $this->_settype($_GET[$var], $type);
        return $_COOKIE[$var];
    }

    public function request($var, $type = '')
    {
        $this->_settype($_GET[$var], $type);
        return $_REQUEST[$var];
    }

    public function uri_string($length = '')
    {
        $uri_string = trim($_SERVER['PATH_INFO'], '/');
        $uri_arr = explode('/', $uri_string);
        if (isset($uri_arr[$length]) && $uri_arr[$length]) {
            return $uri_arr[$length];
        } elseif ($length == '') {
            return $uri_string;
        }
    }

    private function _settype(&$var, $type = '')
    {
        switch ($type) {
            case 'str':
                $var = htmlspecialchars($var);
                settype($var, 'string');
                break;

            case 'int':
                settype($var, 'integer');
                break;

            case 'bool':
                settype($var, 'boolean');
                break;

            case 'float':
                settype($var, 'float');
                break;

            case 'txt':
                $var = preg_replace('/<.*>(.*)<.*>/', '${1}', $var);
                break;

            default:
                $var = htmlspecialchars($var);
                settype($var, 'string');
        }
    }
}
