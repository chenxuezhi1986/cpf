<?php /* Smarty version Smarty-3.1.19, created on 2014-08-16 17:52:34
         compiled from ".\app\templates\test.html" */ ?>
<?php /*%%SmartyHeaderCode:2344453ee3501e91ad0-69834258%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1b70432fb48a9033598a26e3657316ba594205ff' => 
    array (
      0 => '.\\app\\templates\\test.html',
      1 => 1408182751,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2344453ee3501e91ad0-69834258',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_53ee3501eff0f3_15611944',
  'variables' => 
  array (
    'title' => 0,
    'user' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ee3501eff0f3_15611944')) {function content_53ee3501eff0f3_15611944($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
</head>

<body>
<?php echo print_r($_smarty_tpl->tpl_vars['user']->value);?>

</body>
</html><?php }} ?>
