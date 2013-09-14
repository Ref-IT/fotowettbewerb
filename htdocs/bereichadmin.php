<?php

require_once("../lib/inc.all.php");

if (!isset($_REQUEST["bereich_id"])) die("Missing Bereich-ID (bereich_id)");

$acl = getBereichACL($_REQUEST["bereich_id"]);

if (!is_array($acl) || count($acl) == 0) die("Bereich ist gesperrt.");
$group = explode(",", $ADMINGROUP);
$user = Array();
foreach ($acl as $a) {
  if ($a["isgroup"]) $group[] = $a["name"]; 
   else $userp[] = $a["name"];
}
requireGroupOrUser($group, $user);

if (isset($_REQUEST["id"])) {
  $projekt = getProjekt($_REQUEST["id"]);
  if ($projekt === false || $projekt["bereich_id"] !== $_REQUEST["bereich_id"]) {
    httperror("Projekt gehört zu anderem Bereich!");
    exit;
  }
}

global $smarty;

$smarty->assign('title','Verwaltung');
$smarty->assign('content','bereichadmin');

if (isset($_REQUEST["action"])) {
switch ($_POST["action"]) { /* use POST here for CSRF */
case "newprojekt":
  $id = addProjekt($_REQUEST["bereich_id"], $_REQUEST["name"]);
  $_SESSION["message"] = "Projekt wurde angelegt.";
  header("Location: bereichadmin.php?id=$id&bereich_id={$_REQUEST["bereich_id"]}");
  exit;
  break;
case "changeprojekt":
  $id = (int) $_REQUEST["id"];
  $mime = NULL; $data = NULL;
  $_SESSION["message"] = "";
  if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0 && $_FILES["logo"]["size"] > 0 && is_uploaded_file($_FILES['logo']['tmp_name'])) {
    $tmpmime = get_mime($_FILES["logo"]["tmp_name"]);
    if (!in_array($tmpmime, Array("image/png","image/jpeg","image/gif"))) {
      $_SESSION["message"] .= "Ungültiger Bild-Typ - nur JPEG, PNG und GIF - Dateien erlaubt.\n";
    } else {
      $mime = $tmpmime;
      $data = file_get_contents($_FILES["logo"]["tmp_name"]);
    }
  } elseif (isset($_REQUEST["clearlogo"])) {
    $mime = false; $data = false;
  }
  updateProjekt($projekt["id"], $_REQUEST["start"], $_REQUEST["end"], $_REQUEST["tos"], $_REQUEST["numSlot"], $_REQUEST["contact"], $mime, $data);
  $_SESSION["message"] .= "Projekt wurde geändert.";
  header("Location: bereichadmin.php?id=$id&bereich_id={$_REQUEST["bereich_id"]}");
  exit;
  break;
case "delprojekt":
  delProjekt($_REQUEST["id"], isset($_REQUEST["deldata"]));
  $_SESSION["message"] = "Projekt wurde entfernt";
  header("Location: bereichadmin.php?bereich_id={$_REQUEST["bereich_id"]}");
  exit;
  break;
case "download":
  set_time_limit(0);
  $zip = new ZipStream("Projekt {$_REQUEST["id"]}.zip");
  iterateAllData($_REQUEST["id"], $zip, function ($d, $zip) {
    $ext = "";
    switch ($d["mimetype"]) {
      case "text/plain": $ext = ".txt"; break;
      case "image/png": $ext = ".png"; break;
      case "image/gif": $ext = ".gif"; break;
      case "image/jpeg": $ext = ".jpeg"; break;
    }
    $zip->addFile($d["data"], $d["nutzer"]."/".$d["slot"]." ".$d["name"].$ext);
  });
  $zip->finalize();
  exit;
  break;
default:
  die("Invalid action");
}
}

$bereich = getBereich($_REQUEST["bereich_id"]);
$smarty->assign('bereich',$bereich);

$projekte = getProjekte($bereich["id"]);
$smarty->assign('projekte',$projekte);

if (isset($_REQUEST["id"])) {
  $smarty->assign('projekt',$projekt);
}

$smarty->display('layout.tpl');
