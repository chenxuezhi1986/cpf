<?php

/**
 * @author chenxuezhi
 * @copyright 20147
 */

define('C_DEBUG', true);
define('SYSPATH', './sys/');
define('APPPATH', './app/');
require (SYSPATH . './core/kernel.php');
Kernel::run();
?>