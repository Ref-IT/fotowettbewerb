<h1>Administration</h1>

{if isset($bereich)}

{* Bereich bearbeiten *}

<h2>Bereich {$bereich.name|escape}</h2>

<form action="admin.php" method="POST">
<fieldset>
<legend>Bereich umbennen</legend>
 <input type="hidden" name="action" value="changename">
 <input type="hidden" name="id" value="{$bereich.id|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
 <label for="name">Name:</label>
 <input type="text" name="name" value="{$bereich.name|escape}">
 <input type="submit" value="Name ändern">
</fieldset>
</form>

<form action="admin.php" method="POST">
<fieldset>
<legend>Bereich löschen</legend>
 <input type="hidden" name="action" value="delbereich">
 <input type="hidden" name="id" value="{$bereich.id|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
 <input type="submit" value="Bereich löschen">
</fieldset>
</form>

<h3>Bereich-Administratoren</h3>

<a href="bereichadmin.php?bereich_id={$bereich.id|escape}">
Bereichsadministration unter {$PHP_BASE|escape}/bereichadmin.php?bereich_id={$bereich.id|escape}
</a>

<ul>
{foreach $acl as $a}
<li>
<form action="admin.php" method="POST">
 <input type="hidden" name="action" value="delacl">
 <input type="hidden" name="id" value="{$bereich.id|escape}">
 <input type="hidden" name="name" value="{$a.name|escape}">
 <input type="hidden" name="isgroup" value="{$a.isgroup|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
{if $a.isgroup}Gruppe{else}Nutzer{/if} {$a.name|escape}
 <input type="submit" value="löschen">
</form>
{foreachelse}
<li>Es gibt keine Administratoren für diesen Bereich
{/foreach}
</ul>

<form action="admin.php" method="POST">
<fieldset>
<legend>Bereich-Administrator hinzufügen</legend>
 <input type="hidden" name="action" value="addacl">
 <input type="hidden" name="id" value="{$bereich.id|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
<label for="isgroup">Typ:</label>
 <select name="isgroup" size="1"><option value="0">Benutzer<option value="1">Gruppe</select>
<label for="name">Name:</label>
 <input type="text" name="name" placeholder="Nutzer- oder Gruppenname">
 <input type="submit" value="anlegen">
</fieldset>
</form>

<p>
Nutzernamenund Gruppen können im <a href="https://helfer.stura.tu-ilmenau.de/sgis">SGIS</a> nachgeschlagen werden.
</p>

{/if}

<h2>Bereiche</h2>

{* Liste der Bereiche *}
<ul>
{foreach $bereiche as $b}
<li><a href="admin.php?id={$b.id|escape:url}">{$b.name|escape}</a>
{foreachelse}
<li>Keine Bereiche vorhanden.
{/foreach}
</ul>

{* Neuen Bereich anlegen *}
<h2>Neuer Bereich</h2>
<form action="admin.php" method="POST">
<input type="hidden" name="action" value="newbereich">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
<label for="name">Name:</label><input type="text" name="name" placeholder="Name"> <input type="submit" value="anlegen">
</form>

