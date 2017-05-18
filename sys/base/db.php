<?php if (!defined('BASEPATH')) exit('Access Denied');

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Db_Base
{
    private $config;
    private $curlink;

    public function connect($dbhost, $dbuser, $dbpwd, $dbcharset, $dbname, $pconnect)
    {
        if ($pconnect) {
            $link = @mysql_pconnect($dbhost, $dbuser, $dbpwd, MYSQL_CLIENT_COMPRESS);
        } else {
            $link = @mysql_connect($dbhost, $dbuser, $dbpwd, 1, MYSQL_CLIENT_COMPRESS);
        }
        if (!$link) {
            error('Database could not connect code : ' . $this->errno());
        } else {
            $this->config = array(
                'dbhost' => $dbhost,
                'dbuser' => $dbuser,
                'dbpwd' => $dbpwd,
                'dbcharset' => $dbcharset,
                'dbname' => $dbname,
                'pconnect' => $pconnect);

            $this->curlink = $link;
            if ($this->version() > '4.1') {
                $serverset = $dbcharset ? 'character_set_connection=' . $dbcharset .
                    ', character_set_results=' . $dbcharset . ', character_set_client=binary' : '';
                $serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',') .
                    'sql_mode=\'\'') : '';
                $serverset && mysql_query("SET $serverset", $link);
            }
            $dbname && @mysql_select_db($dbname, $link);
        }
        return $link;
    }

    public function select_db($dbname)
    {
        return mysql_select_db($dbname, $this->curlink);
    }

    public function fetch_array($query, $result_type = MYSQL_ASSOC)
    {
        if ($result_type == 'MYSQL_ASSOC')
            $result_type = MYSQL_ASSOC;
        return mysql_fetch_array($query, $result_type);
    }

    public function fetch_first($sql)
    {
        return $this->fetch_array($this->query($sql));
    }

    public function result_first($sql)
    {
        return $this->result($this->query($sql), 0);
    }

    public function query($sql, $silent = false, $unbuffered = false)
    {
        if (defined('C_DEBUG') && C_DEBUG) {
            $starttime = microtime(true);
        }

        if ('UNBUFFERED' === $silent) {
            $silent = false;
            $unbuffered = true;
        } elseif ('SILENT' === $silent) {
            $silent = true;
            $unbuffered = false;
        }

        $func = $unbuffered ? 'mysql_unbuffered_query' : 'mysql_query';

        if (!($query = $func($sql, $this->curlink))) {
            if (in_array($this->errno(), array(2006, 2013)) && substr($silent, 0, 5) != 'RETRY') {
                $this->curlink = $this->connect($this->config['dbhost'], $this->config['dbuser'],
                    $this->config['dbpwd'], $this->config['dbcharset'], $this->config['dbname'], $this->
                    config['pconnect']);
                return $this->query($sql, 'RETRY' . $silent);
            }

            if (!$silent) {
                error($this->error().' '.$this->errno().' '.$sql);
            }
        }

        //记录SQL执行时间
        if (defined('C_DEBUG') && C_DEBUG) {
            Kernel::$debug_info['sql'][] = $sql . ' <' . number_format((microtime(true) - $starttime), 6) . '>';
        }

        return $query;
    }

    public function affected_rows()
    {
        return mysql_affected_rows($this->curlink);
    }

    public function error()
    {
        return (($this->curlink) ? mysql_error($this->curlink) : mysql_error());
    }

    public function errno()
    {
        return intval(($this->curlink) ? mysql_errno($this->curlink) : mysql_errno());
    }

    public function result($query, $row = 0)
    {
        $query = @mysql_result($query, $row);
        return $query;
    }

    public function num_rows($query)
    {
        $query = mysql_num_rows($query);
        return $query;
    }

    public function num_fields($query)
    {
        return mysql_num_fields($query);
    }

    public function free_result($query)
    {
        return mysql_free_result($query);
    }

    public function insert_id()
    {
        return ($id = mysql_insert_id($this->curlink)) >= 0 ? $id : $this->result($this->
            query("SELECT last_insert_id()"), 0);
    }

    public function fetch_row($query)
    {
        $query = mysql_fetch_row($query);
        return $query;
    }

    public function fetch_fields($query)
    {
        return mysql_fetch_field($query);
    }

    public function version()
    {
        return mysql_get_server_info($this->curlink);
    }

    public function escape_string($str)
    {
        return mysql_escape_string($str);
    }

    public function close()
    {
        return mysql_close($this->curlink);
    }
}
