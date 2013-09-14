<?php

global $SESSIONNAME;

session_name($SESSIONNAME);
session_set_cookie_params ( 0, dirname($_SERVER["SCRIPT_NAME"]), $_SERVER["HTTP_HOST"], true, true);
session_start();
