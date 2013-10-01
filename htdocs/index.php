<?php

require_once("../lib/inc.all.php");

if (!isset($_REQUEST["uuid"])) die("Missing Projekt-ID (uuid)");

requireAuth();

$projekt = getCurrentProjekt($_REQUEST["uuid"]);
if ($projekt === false) {
  echo ("Kein solches Projekt oder Projekt derzeit nicht aktiv!");
  exit;
}
$id = $projekt["id"];

global $smarty;

$smarty->assign('title','Bilderupload');
$smarty->assign('content','teilnehmer');
$smarty->assign('contact',$projekt["contact"]);
$smarty->assign('uuid',$projekt["uuid"]);
$userTOS = getDataBySlot($id, SLOT_TOS);
if ($userTOS === false || $userTOS["data"] !== $projekt["tos"]) {
  $smarty->assign('needtos',TRUE);
} else {
  $smarty->assign('needtos',FALSE);
}

if (isset($_REQUEST["slotIdx"])) {
  $idx = (int) $_REQUEST["slotIdx"];
  if ($idx < 1 || $idx > (int) $projekt["numSlot"])
    httperror("Invalid slotIdx");
  $slotId = $_REQUEST["slotIdx"] - 1 + SLOT_BASE;
}

if (isset($_REQUEST["action"])) {
switch ($_POST["action"]) { /* use POST here for CSRF */
case "tos":
  if (md5($projekt["tos"]) !== $_REQUEST["tos"]) {
    httperror("Die AGB wurden zwischenzeitlich verändert.");
    exit;
  }
  delSlot($id, SLOT_TOS);
  delSlot($id, SLOT_MAIL);
  addDataToSlot($id, SLOT_TOS, "AGB", "text/plain", $projekt["tos"]);
  addDataToSlot($id, SLOT_MAIL, "eMail", "text/plain", getUserMail());
  $_SESSION["message"] = "Sie haben den Nutzungsbedingungen zugestimmt.";
  header("Location: index.php?uuid={$_REQUEST["uuid"]}");
  exit;
  break;
case "addslot":
  if (!isset($_REQUEST["slotIdx"])) httperror("Missing slotIdx");
  if (!isset($_FILES["bild"]) || $_FILES["bild"]["error"] != 0 || $_FILES["bild"]["size"] <= 0 || !is_uploaded_file($_FILES['bild']['tmp_name'])) {
    $_SESSION["message"] = "Der Upload ist fehlgeschlagen.";
  } else {
    $mime = get_mime($_FILES["bild"]["tmp_name"]);
    if (!in_array($mime, Array("image/png","image/jpeg","image/gif"))) {
      $_SESSION["message"] = "Ungültiger Bild-Typ - nur JPEG, PNG und GIF - Dateien erlaubt.";
    } else {
      $data = file_get_contents($_FILES["bild"]["tmp_name"]);
      $name = trim($_REQUEST["name"]);
      if ($name == "") {
        $name = $_FILES["bild"]["name"];
      } else {
        $name .= " (".$_FILES["bild"]["name"].")";
      }
      addDataToSlot($id, $slotId, $name, $mime, $data);
      $_SESSION["message"] = "Das Bild wurde gespeichert";
    }
  }
  header("Location: index.php?uuid={$_REQUEST["uuid"]}");
  exit;
  break;
case "download":
  if (!isset($_REQUEST["slotIdx"])) httperror("Missing slotIdx");
  $data = getDataBySlot($id, $slotId);
  if ($data === false) httperror("Missing slotIdx");
  $ext = "";
  switch ($data["mimetype"]) {
    case "text/plain": $ext = ".txt"; break;
    case "image/png": $ext = ".png"; break;
    case "image/gif": $ext = ".gif"; break;
    case "image/jpeg": $ext = ".jpeg"; break;
  }
  header("Content-Type: {$data["mimetyp"]}");
  header("Content-Length: ".strlen($data["data"]));
  header("Content-Disposition: inline; filename=\"{$slotId} {$data["name"]}{$ext}\"");
  echo $data["data"];
  exit;
  break;
case "renameslot":
  if (!isset($_REQUEST["slotIdx"])) httperror("Missing slotIdx");
  renameSlot($id, $slotId, $_REQUEST["name"]);
  $_SESSION["message"] = "Das Bild wurde umbenannt.";
  header("Location: index.php?uuid={$_REQUEST["uuid"]}");
  exit;
  break;
case "delslot":
  if (!isset($_REQUEST["slotIdx"])) httperror("Missing slotIdx");
  delSlot($id, $slotId);
  $_SESSION["message"] = "Das Bild wurde gelöscht";
  header("Location: index.php?uuid={$_REQUEST["uuid"]}");
  exit;
  break;
default:
  die("Invalid action");
}
}

$data = getData($id);
$smarty->assignByRef('data',$data);
$smarty->assign('projekt',$projekt);
$smarty->assign('upload_max_filesize', ini_get('upload_max_filesize'));
$smarty->display('layout.tpl');
