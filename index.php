<?php

/**
 * @author chenxuezhi
 * @copyright 2014
 */

define('C_DEBUG', true);
define('BASEPATH', './sys/');
define('APPPATH', './app/');
require (BASEPATH . './base/kernel.php');
Kernel::run();
?>