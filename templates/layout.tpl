<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Fotowettbewerb {$title|escape}</title>
  <link rel="stylesheet" href="css/jquery-ui-1.10.3.custom.min.css" />
  <link rel="stylesheet" href="css/jquery-ui-timepicker-addon.css" />
  <link rel="stylesheet" href="css/table.css" />
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-i18n.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon-i18n.js"></script>
  <script type="text/javascript" src="js/jquery-ui-i18n-select.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-install.js"></script>
  <script type="text/javascript" src="js/jquery.autosize.js"></script>
  <script type="text/javascript" src="js/jquery.autosize-install.js"></script>
 </head>
 <body>

 {if isset($message)}
 <b>{$message|escape}</b>
 {/if}

 {include file="$content.tpl"}

<hr/>
<a href="{$logoutUrl|escape}">Abmelden</a>
{if isset($contact)}
&bull; Kontakt: <a href="mailto:{$contact|escape}">{$contact|escape}</a>
{/if}
&bull; <a href="https://stura.tu-ilmenau.de/" target="_blank">Impressum</a>
<br/>
Diese Webseite wurde optimiert für Internet Explorer Version 8 oder neuer, Mozilla Firefox oder Google Chrome.
<br/>
Diese Anwendung unterliegt der <a href="http://www.gnu.org/licenses/agpl-3.0.html" target="_blank">GNU Affero General Public License</a>. <a href="https://github.com/michael-dev/fotowettbewerb" target="_blank">Quellcode</a>
 </body>
</html>
