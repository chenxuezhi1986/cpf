<?php

/**
 * @author chenxuezhi
 * @copyright 20147
 */

define('C_DEBUG', true);
define('BASEPATH', './sys/');
define('APPPATH', './app/');
require (BASEPATH . './core/kernel.php');
Kernel::run();
?>