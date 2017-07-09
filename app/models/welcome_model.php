<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

class Welcome_Model {
    
    public $db_config_id = 1; //数据库配置号

    public function say()
    {
        $opt['cached'] = true;
        $opt['select'] = '*';
        $opt['from'] = 'search_items';
        //$opt['where'] = 'id in(1,2,3) and userid = 123';
        $opt['order_by'] = 'sid desc';
        //$opt['limit'] = '1,2';
        $data = $this->get($opt);
        return $data;
    }
    
    public function hi()
    {
$txt = <<<EOF
1.单入口，默认控制器设定，基类使用单列模式</br>
2.MVC模式部署</br>
3.Lib类库扩展、延伸</br>
4.自定义路由器规则</br>
5.支持多个数据库配置可在Model设定</br>
6.嵌入Smarty模板引擎</br>
7.Debug模式可打印出当前页面引用的文件
EOF;
return $txt;
    } 
    
}