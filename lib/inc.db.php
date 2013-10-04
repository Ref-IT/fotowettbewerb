<?php
global $pdo;
global $DB_DSN, $DB_USERNAME, $DB_PASSWORD, $DB_PREFIX;

$pdo = new PDO($DB_DSN, $DB_USERNAME, $DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8; SET lc_time_names = 'de_DE';"));

define("SLOT_TOS", -1);
define("SLOT_MAIL", -2);
define("SLOT_NAME", -3);
define("SLOT_BASE", 0);

$pdo->query("
CREATE TABLE IF NOT EXISTS `{$DB_PREFIX}bereich` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
") or httperror(print_r($pdo->errorInfo(),true));

$pdo->query("
CREATE TABLE IF NOT EXISTS `{$DB_PREFIX}acl` (
  `bereich_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `isgroup` tinyint(1) NOT NULL,
  PRIMARY KEY (`bereich_id`,`name`,`isgroup`),
  KEY `bereich_id` (`bereich_id`),
  FOREIGN KEY (bereich_id) REFERENCES {$DB_PREFIX}bereich(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
") or httperror(print_r($pdo->errorInfo(),true));

$pdo->query("
CREATE TABLE IF NOT EXISTS `{$DB_PREFIX}projekt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bereich_id` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) NULL DEFAULT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end` timestamp NULL DEFAULT NULL,
  `tos` text,
  `numSlot` int(11) DEFAULT 5,
  `logo_mimetype` varchar(255) DEFAULT NULL,
  `logo_data` blob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projekt_name` (`bereich_id`,`name`),
  UNIQUE KEY `uuid_idx` (`uuid`),
  KEY `bereich_id` (`bereich_id`),
  FOREIGN KEY (bereich_id) REFERENCES {$DB_PREFIX}bereich(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
") or httperror(print_r($pdo->errorInfo(),true));

$pdo->query("
CREATE TABLE IF NOT EXISTS `{$DB_PREFIX}dateien` (
  `projekt_id` int(11) NOT NULL,
  `slot` int(11) NOT NULL,
  `nutzer` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mimetype` varchar(255) NOT NULL,
  PRIMARY KEY `unique_slot` (`projekt_id`,`slot`,`nutzer`),
  KEY `search_nutzer` (`projekt_id`,`nutzer`),
  FOREIGN KEY (projekt_id) REFERENCES {$DB_PREFIX}projekt(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
") or httperror(print_r($pdo->errorInfo(),true));

$pdo->query("
CREATE TABLE IF NOT EXISTS `{$DB_PREFIX}dateien_data` (
  `projekt_id` int(11) NOT NULL,
  `slot` int(11) NOT NULL,
  `nutzer` varchar(255) NOT NULL,
  `part` int(11) NOT NULL,
  `data` longblob NOT NULL,
  PRIMARY KEY `unique_part` (`projekt_id`,`slot`,`nutzer`,`part`),
  KEY `search_nutzer` (`projekt_id`,`nutzer`),
  FOREIGN KEY (projekt_id, slot, nutzer) REFERENCES {$DB_PREFIX}dateien(projekt_id, slot, nutzer) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
") or httperror(print_r($pdo->errorInfo(),true));

function getBereiche() {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}bereich");
  $query->execute(Array()) or httperror(print_r($query->errorInfo(),true));
  return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getBereich($id) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}bereich WHERE id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  if ($query->rowCount() == 0) return false;
  return $query->fetch(PDO::FETCH_ASSOC);
}

function delBereich($id) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}projekt WHERE bereich_id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  if ($query->rowCount() > 0) { httperror("Der Bereich hat noch Projekte und kann daher nicht entfernt werden."); return false; }
  $query = $pdo->prepare("DELETE FROM {$DB_PREFIX}bereich WHERE id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
}

function setBereichName($id, $name) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("UPDATE {$DB_PREFIX}bereich SET name = ? WHERE id = ?");
  $query->execute(Array($name, $id)) or httperror(print_r($query->errorInfo(),true));
}

function addBereich($name) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("INSERT INTO {$DB_PREFIX}bereich(name) VALUES (?)");
  $query->execute(Array($name)) or httperror(print_r($query->errorInfo(),true));
  return $pdo->lastInsertId();
}

function getBereichACL($id) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}acl WHERE bereich_id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  return $query->fetchAll(PDO::FETCH_ASSOC);
}

function addBereichACL($id, $name, $isgroup) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("INSERT INTO {$DB_PREFIX}acl(bereich_id, name, isgroup) VALUES (?, ?, ?)");
  $query->execute(Array($id, $name, $isgroup)) or httperror(print_r($query->errorInfo(),true));
}

function delBereichACL($id, $name, $isgroup) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("DELETE FROM {$DB_PREFIX}acl WHERE bereich_id = ? AND name = ? AND isgroup = ?");
  $query->execute(Array($id, $name, $isgroup)) or httperror(print_r($query->errorInfo(),true));
}

function getProjekte($id) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT id, bereich_id, uuid, name, contact, UNIX_TIMESTAMP(start) as start, UNIX_TIMESTAMP(end) as end, tos, numSlot, logo_mimetype, logo_data FROM {$DB_PREFIX}projekt WHERE bereich_id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  return $query->fetchAll(PDO::FETCH_ASSOC);
}

function addProjekt($id, $name) {
  global $pdo, $DB_PREFIX, $UUIDPREFIX;
  $query = $pdo->prepare("INSERT INTO {$DB_PREFIX}projekt(bereich_id, name, uuid) VALUES (?, ?, ?)");
  $query->execute(Array($id, $name, uniqid($UUIDPREFIX,true))) or httperror(print_r($query->errorInfo(),true));
  return $pdo->lastInsertId();
}

function getProjekt($id) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT id, bereich_id, uuid, name, contact, UNIX_TIMESTAMP(start) as start, UNIX_TIMESTAMP(end) as end, tos, numSlot, logo_mimetype, logo_data FROM {$DB_PREFIX}projekt WHERE id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  if ($query->rowCount() == 0) return false;
  return $query->fetch(PDO::FETCH_ASSOC);
}

function getCurrentProjekt($uuid) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT id, bereich_id, uuid, name, contact, UNIX_TIMESTAMP(start) as start, UNIX_TIMESTAMP(end) as end, tos, numSlot, logo_mimetype, logo_data FROM {$DB_PREFIX}projekt WHERE uuid = ? AND start <= CURRENT_TIMESTAMP AND (end IS NULL or end >= CURRENT_TIMESTAMP)");
  $query->execute(Array($uuid)) or httperror(print_r($query->errorInfo(),true));
  if ($query->rowCount() == 0) return false;
  return $query->fetch(PDO::FETCH_ASSOC);
}

function updateProjekt($id, $start, $end, $tos, $numSlot, $contact, $mime = NULL, $data = NULL) {
  global $pdo, $DB_PREFIX;
  if (empty($start)) $start = NULL;
  if (empty($end)) $end = NULL;
  if (empty($tos)) $tos = NULL;
  if (empty($numSlot)) $numSlot = NULL;
  if (empty($contact)) $contact = NULL;
  $query = $pdo->prepare("UPDATE {$DB_PREFIX}projekt SET start = ?, end = ?, tos = ?, numSlot = ?, contact = ? WHERE id = ?");
  $query->execute(Array($start, $end, $tos, $numSlot, $contact, $id)) or httperror(print_r($query->errorInfo(),true));
  if ($mime !== NULL && $data !== NULL) {
    if (empty($mime)) $mime = NULL;
    if (empty($data)) $data = NULL;
    $query = $pdo->prepare("UPDATE {$DB_PREFIX}projekt SET logo_mimetype = ?, logo_data = ? WHERE id = ?");
    $query->execute(Array($mime, $data, $id)) or httperror(print_r($query->errorInfo(),true));
  }
}

function delProjekt($id, $deldata = FALSE) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}dateien WHERE projekt_id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  if ($query->rowCount() > 0 && !$deldata) { httperror("Das Projekt hat Dateien und kann daher nicht entfernt werden."); return false; }
  $query = $pdo->prepare("DELETE FROM {$DB_PREFIX}projekt WHERE id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
}

function getAllData($id) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}dateien WHERE projekt_id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  return $query->fetchAll(PDO::FETCH_ASSOC);
}

function iterateAllData($id, $ctx, $callback) {
  global $pdo, $DB_PREFIX;
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}dateien WHERE projekt_id = ?");
  $query->execute(Array($id)) or httperror(print_r($query->errorInfo(),true));
  $dateien = $query->fetchall(PDO::FETCH_ASSOC);
  foreach ($dateien as $row) {
    $row["data"] = getContentBySlot($row["projekt_id"], $row["nutzer"], $row["slot"]);
    $callback($row, $ctx);
  }
}

function getData($id) {
  global $pdo, $DB_PREFIX;
  $username = getUsername();
  $query = $pdo->prepare("SELECT `slot`, `name`, `mimetype` FROM {$DB_PREFIX}dateien WHERE projekt_id = ? AND nutzer = ?");
  $query->execute(Array($id, $username)) or httperror(print_r($query->errorInfo(),true));
  $rows = $query->fetchAll(PDO::FETCH_ASSOC);
  $ret = Array();
  foreach ($rows as $r)
   $ret[$r["slot"] + 1 - SLOT_BASE] = $r;
  return $ret;
}

function getDataBySlot($id, $slot) {
  global $pdo, $DB_PREFIX;
  $username = getUsername();
  $query = $pdo->prepare("SELECT * FROM {$DB_PREFIX}dateien WHERE projekt_id = ? AND nutzer = ? AND slot = ?");
  $query->execute(Array($id, $username, $slot)) or httperror(print_r($query->errorInfo(),true));
  if ($query->rowCount() == 0) return false;
  $ret = $query->fetch(PDO::FETCH_ASSOC);
  $ret["data"] = getContentBySlot($id, $username, $slot);
  return $ret;
}

function getContentBySlot($id, $username, $slot) {
  global $pdo, $DB_PREFIX;

  $query = $pdo->prepare("SELECT data FROM `{$DB_PREFIX}dateien_data` WHERE projekt_id = ? AND nutzer = ? AND slot = ? ORDER BY part");
  $query->execute(Array($id, $username, $slot)) or httperror(print_r($query->errorInfo(),true));
  if ($query->rowCount() == 0) return $ret;
  $ret = "";
  while ($row = $query->fetch(PDO::FETCH_ASSOC))
    $ret .= $row["data"];
  return $ret;
}

function addDataToSlot($id, $slot, $name, $mime, $data) {
  global $pdo, $DB_PREFIX, $UUIDPREFIX;
  $username = getUsername();
  $pdo->beginTransaction();
  $query = $pdo->prepare("INSERT INTO {$DB_PREFIX}dateien(projekt_id, nutzer, slot, name, mimetype) VALUES (?, ?, ?, ?, ?)");
  $query->execute(Array($id, $username, $slot, $name, $mime)) or httperror(print_r($query->errorInfo(),true));
  if ($data !== NULL) {
    $query = $pdo->prepare("INSERT INTO {$DB_PREFIX}dateien_data(projekt_id, nutzer, slot, part, data) VALUES (?, ?, ?, ?, ?)");
    $datas = str_split($data, 1024 * 10);
    foreach ($datas as $i => $data) {
      $query->execute(Array($id, $username, $slot, $i, $data)) or httperror(print_r($query->errorInfo(),true));
    }
  }
  $pdo->commit();
}

function renameSlot($id, $slot, $name) {
  global $pdo, $DB_PREFIX;
  $username = getUsername();
  $query = $pdo->prepare("UPDATE {$DB_PREFIX}dateien SET name = ? WHERE projekt_id = ? AND nutzer = ? AND slot = ?");
  $query->execute(Array($name, $id, $username, $slot)) or httperror(print_r($query->errorInfo(),true));
}

function delSlot($id, $slot) {
  global $pdo, $DB_PREFIX;
  $username = getUsername();
  $query = $pdo->prepare("DELETE FROM {$DB_PREFIX}dateien_data WHERE projekt_id = ? AND nutzer = ? AND slot = ?");
  $query->execute(Array($id, $username, $slot)) or httperror(print_r($query->errorInfo(),true));
  $query = $pdo->prepare("DELETE FROM {$DB_PREFIX}dateien WHERE projekt_id = ? AND nutzer = ? AND slot = ?");
  $query->execute(Array($id, $username, $slot)) or httperror(print_r($query->errorInfo(),true));
}

# vim: set expandtab tabstop=8 shiftwidth=8 :

