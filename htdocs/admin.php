<?php

require_once("../lib/inc.all.php");

requireGroupOrUser($ADMINGROUP);

global $smarty;

$smarty->assign('title','Administration');
$smarty->assign('content','admin');

if (isset($_REQUEST["action"])) {
switch ($_POST["action"]) { /* use POST here for CSRF */
case "newbereich":
  $id = addBereich($_REQUEST["name"]);
  $_SESSION["message"] = "Bereich wurde angelegt.";
  header("Location: admin.php?id=$id");
  exit;
  break;
case "delbereich":
  delBereich($_REQUEST["id"]);
  $_SESSION["message"] = "Bereich wurde entfernt";
  header("Location: admin.php");
  exit;
  break;
case "changename":
  assert(getBereich($_REQUEST["id"]) !== false);
  $id = (int) $_REQUEST["id"];
  setBereichName($id, $_REQUEST["name"]);
  $_SESSION["message"] = "Bereich wurde umbenannt.";
  header("Location: admin.php?id=$id");
  exit;
  break;
case "addacl":
  assert(getBereich($_REQUEST["id"]) !== false);
  $id = (int) $_REQUEST["id"];
  addBereichACL($id, $_REQUEST["name"], $_REQUEST["isgroup"]);
  $_SESSION["message"] = "ACL wurde angelegt";
  header("Location: admin.php?id=$id");
  exit;
  break;
case "delacl":
  assert(getBereich($_REQUEST["id"]) !== false);
  $id = (int) $_REQUEST["id"];
  delBereichACL($id, $_REQUEST["name"], $_REQUEST["isgroup"]);
  $_SESSION["message"] = "ACL wurde angelegt";
  header("Location: admin.php?id=$id");
  exit;
  break;
default:
  die("Invalid action");
}
}

$bereiche = getBereiche();
$smarty->assign('bereiche',$bereiche);

if (isset($_REQUEST["id"])) {
  $bereich = getBereich($_REQUEST["id"]);
  $smarty->assign('bereich',$bereich);
  $acl = getBereichACL($_REQUEST["id"]);
  $smarty->assign('acl',$acl);
}

$smarty->display('layout.tpl');
