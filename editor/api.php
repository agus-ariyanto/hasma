<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR',dirname(dirname(__FILE__)));
define('isApi',true);
require_once (ROOT_DIR.DS.'core'.DS.'auto.php');
include ROOT_DIR.DS.'core'.DS.'config.php';
include ROOT_DIR.DS.'core'.DS.'route.php';
