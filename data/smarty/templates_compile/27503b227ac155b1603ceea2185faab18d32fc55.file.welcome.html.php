<?php /* Smarty version Smarty-3.1.19, created on 2016-03-24 13:10:40
         compiled from ".\app\templates\welcome.html" */ ?>
<?php /*%%SmartyHeaderCode:1790356f3d940089342-96823992%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27503b227ac155b1603ceea2185faab18d32fc55' => 
    array (
      0 => '.\\app\\templates\\welcome.html',
      1 => 1458818562,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1790356f3d940089342-96823992',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56f3d9400c7b56_02725570',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56f3d9400c7b56_02725570')) {function content_56f3d9400c7b56_02725570($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>模板</title>
</head>

<body>
<strong><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</strong></br>
<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

</body>
</html><?php }} ?>
