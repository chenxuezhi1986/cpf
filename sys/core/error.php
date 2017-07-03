<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Error_Core extends Exception {

    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code); 
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n"; 
    }
    
    private static function set_status_header($code = 200, $text = '')
    {
        $stati = array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',

            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');

        if ($code == '' or !is_numeric($code)) {
            show_error('Status codes must be numeric', 500);
        }

        if (isset($stati[$code]) and $text == '') {
            $text = $stati[$code];
        }

        if ($text == '') {
            show_error('No status text available.  Please check your status code number or supply your own message text.',
                500);
        }

        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : false;

        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            header("Status: {$code} {$text}", true);
        } elseif ($server_protocol == 'HTTP/1.1' or $server_protocol == 'HTTP/1.0') {
            header($server_protocol . " {$code} {$text}", true, $code);
        } else {
            header("HTTP/1.1 {$code} {$text}", true, $code);
        }
    }

    public static function error_404()
    {
        self::set_status_header(404);
        ob_start();
        include (APPPATH . 'errors/404.html');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {
            case E_USER_ERROR:
                echo "<b>Fatal error:</b> $errstr<br />\n";
                //echo "  Fatal error on line $errline in file <b>$errfile</b>";
                exit;
                break;

            case E_USER_WARNING:
                echo "<b>WARNING</b> $errstr<br />\n";
                break;

            case E_USER_NOTICE:
                echo "<b>NOTICE</b> $errstr<br />\n";
                break;

            default:
                echo "Unknown error type: [$errno] $errstr<br />\n";
                break;
        }
        
        return true;
    }
}
