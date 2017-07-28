<?php

/**
 * @author chenxuezhi
 * @copyright 20147
 */

define('C_DEBUG', false);
define('SYSPATH', '../sys/');
define('APPPATH', './');
require (SYSPATH . './core/kernel.php');
Kernel::run();
?>