<?php

global $smarty, $logoutUrl, $csrf;

$smarty = new Smarty();
$smarty->setTemplateDir(BASE.'/templates')
       ->setCompileDir(BASE.'/templates_c')
       ->setCacheDir(BASE.'/cache');

$smarty->assign('logoutUrl',$logoutUrl);
$smarty->assign('csrf',$csrf);
$smarty->assign('PHP_BASE',"https://".$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"]));

if (isset($_SESSION["message"])) {
  $smarty->assign('message',$_SESSION['message']);
  unset($_SESSION['message']);
}
