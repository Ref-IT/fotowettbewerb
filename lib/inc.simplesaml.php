<?php

global $SIMPLESAML, $SIMPLESAMLAUTHSOURCE, $attributes, $logoutUrl;

requireAuth();

function getUserMail() {
  global $attributes;
  requireAuth();
  return $attributes["mail"][0];
}

function requireAuth() {
  global $SIMPLESAML, $SIMPLESAMLAUTHSOURCE;
  global $attributes, $logoutUrl;

  require_once($SIMPLESAML.'/lib/_autoload.php');
  $as = new SimpleSAML_Auth_Simple($SIMPLESAMLAUTHSOURCE);
  $as->requireAuth();

  $attributes = $as->getAttributes();
  $logoutUrl = $as->getLogoutURL();
}

function requireGroupOrUser($group,$user="") {
  global $attributes;

  requireAuth();
  if (!is_array($group)) $group = explode(",",$group);
  if (!is_array($user)) $user = explode(",",$user);

  if ((count(array_intersect($group, $attributes["groups"])) == 0) &&
      !in_array(getUsername(), $user)) {
    header('HTTP/1.0 401 Unauthorized');
    include SGISBASE."/template/permission-denied.tpl";
    die();
  }
}

function getUsername() {
  global $attributes;
  if (isset($attributes["eduPersonPrincipalName"]) && isset($attributes["eduPersonPrincipalName"][0])) 
    return $attributes["eduPersonPrincipalName"][0];
  if (isset($attributes["mail"]) && isset($attributes["mail"][0])) 
    return $attributes["mail"][0];
  return NULL;
}
