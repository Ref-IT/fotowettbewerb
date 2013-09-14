<?php

define('BASE', dirname(dirname(__FILE__)));
require_once(BASE.'/config/config.php');

if (!defined("SMARTY_DIR")) {
  define('SMARTY_DIR', BASE.'/lib/smarty/libs/');
}
require_once(SMARTY_DIR.'Smarty.class.php');

require_once(BASE.'/lib/inc.error.php');
require_once(BASE.'/lib/inc.simplesaml.php');
require_once(BASE.'/lib/inc.db.php');
require_once(BASE.'/lib/inc.session.php');
require_once(BASE.'/lib/inc.csrf.php');
require_once(BASE.'/lib/inc.smarty.php');
require_once(BASE.'/lib/inc.mime.php');
require_once(BASE.'/lib/inc.zip.php');
require_once(BASE.'/lib/inc.cache.php');
