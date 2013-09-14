<?php

global $csrf;

if (isset($_SESSION["csrf"]) && !empty($_SESSION["csrf"])) {
  $csrf = $_SESSION["csrf"];
} else {
  $csrf = randomstring();
  $_SESSION["csrf"] = $csrf;
}

if (!isset($_REQUEST["csrf"]) || $_REQUEST["csrf"] !== $csrf) {
  $_POST = Array();
}

/* return a random ascii string */
function randomstring($length = 32) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
  srand((double)microtime()*1000000);
  $pass = "";
  for ($i = 0; $i < $length; $i++) {
    $num = rand(0, strlen($chars)-1);
    $pass .= substr($chars, $num, 1);
  }
  return $pass;
}

