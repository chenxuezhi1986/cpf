<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

if (!function_exists('getgpc')) {
    function getgpc($k, $t = 'GP')
    {
        $t = strtoupper($t);
        switch ($t) {
            case 'GP':
                isset($_POST[$k]) ? $var = &$_POST : $var = &$_GET;
                break;
            case 'G':
                $var = &$_GET;
                break;
            case 'P':
                $var = &$_POST;
                break;
            case 'C':
                $var = &$_COOKIE;
                break;
            case 'R':
                $var = &$_REQUEST;
                break;
        }
        return isset($var[$k]) ? $var[$k] : null;
    }
}

if (!function_exists('error_404')) {
    function error_404()
    {
        echo Error_Core::error_404();
        exit;
    }
}

if (!function_exists('error')) {
    function error($content)
    {
        trigger_error($content, E_USER_ERROR);
        exit;
    }
}

if (!function_exists('microtime_float')) {
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}

////purge("192.168.0.4", "/index.php");
if (!function_exists('purge')) {
    function purge($ip, $url)
    {
        $errstr = '';
        $errno = '';
        $fp = fsockopen($ip, 80, $errno, $errstr, 2);
        if (!$fp) {
            return false;
        } else {
            $out = "PURGE $url HTTP/1.1\r\n";
            $out .= "Host:blog.zyan.cc\r\n";
            $out .= "Connection: close\r\n\r\n";
            fputs($fp, $out);
            $out = fgets($fp, 4096);
            fclose($fp);
            return true;
        }
    }
}
