<?php if (!defined('SYSPATH')) exit('Access Denied');

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

if (!function_exists('authcode')) {
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    	$ckey_length = 4;
    	$key = md5($key != '' ? $key : getglobal('authkey'));
    	$keya = md5(substr($key, 0, 16));
    	$keyb = md5(substr($key, 16, 16));
    	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    
    	$cryptkey = $keya.md5($keya.$keyc);
    	$key_length = strlen($cryptkey);
    
    	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    	$string_length = strlen($string);
    
    	$result = '';
    	$box = range(0, 255);
    
    	$rndkey = array();
    	for($i = 0; $i <= 255; $i++) {
    		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
    	}
    
    	for($j = $i = 0; $i < 256; $i++) {
    		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
    		$tmp = $box[$i];
    		$box[$i] = $box[$j];
    		$box[$j] = $tmp;
    	}
    
    	for($a = $j = $i = 0; $i < $string_length; $i++) {
    		$a = ($a + 1) % 256;
    		$j = ($j + $box[$a]) % 256;
    		$tmp = $box[$a];
    		$box[$a] = $box[$j];
    		$box[$j] = $tmp;
    		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    	}
    
    	if($operation == 'DECODE') {
    		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
    			return substr($result, 26);
    		} else {
    			return '';
    		}
    	} else {
    		return $keyc.str_replace('=', '', base64_encode($result));
    	}
    
    }
}