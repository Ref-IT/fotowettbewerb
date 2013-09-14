{if $projekt.logo_data}
<img align="right" src="data:{$projekt.logo_mimetype|escape};base64,{base64_encode($projekt.logo_data)}">
{/if}

<h1>{$projekt.name|escape}</h1>

{if $needtos}

<form action="index.php" method="POST">
<input type="hidden" name="action" value="tos">
<input type="hidden" name="uuid" value="{$uuid|escape}">
<input type="hidden" name="csrf" value="{$csrf|escape}">
<input type="hidden" name="tos" value="{md5($projekt.tos)}">
<fieldset><legend>Nutzungsbedingungen</legend>
<pre>
{$projekt.tos}
</pre>
<input type="submit" value="Zustimmen">
</fieldset>
</form>

{else}

{* Slots anzeigen *}
{* freier Slot: Upload; belegter Slot: Download + Entfernen + Name ändern*}

<h2>Eingereichte Bilder</h2>

Es können nur Dateien vom Typ "image/png", "image/jpeg" und "image/gif" hochgeladen werden.

<ol>
{for $i = 1 to $projekt.numSlot}
<li>
 {if isset($data[$i])}
<form action="index.php" method="POST" enctype="multipart/form-data" style="display: inline-block;">
 <input type="hidden" name="action" value="renameslot">
 <input type="hidden" name="slotIdx" value="{$i}">
 <input type="hidden" name="uuid" value="{$uuid|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
<label for="name">Name:</label> <input type="text" name="name" value="{$data[$i].name|escape}">
<input type="submit" value="Bild umbennen">
</form>
<form action="index.php" method="POST" enctype="multipart/form-data" style="display: inline-block;">
 <input type="hidden" name="action" value="delslot">
 <input type="hidden" name="slotIdx" value="{$i}">
 <input type="hidden" name="uuid" value="{$uuid|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
<input type="submit" value="Bild löschen">
</form>
<form action="index.php" method="POST" enctype="multipart/form-data" style="display: inline-block;">
 <input type="hidden" name="action" value="download">
 <input type="hidden" name="slotIdx" value="{$i}">
 <input type="hidden" name="uuid" value="{$uuid|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
<input type="submit" value="Bild herunterladen">
</form>
 {else}
<form action="index.php" method="POST" enctype="multipart/form-data">
 <input type="hidden" name="action" value="addslot">
 <input type="hidden" name="slotIdx" value="{$i}">
 <input type="hidden" name="uuid" value="{$uuid|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
<label for="name">Name:</label> <input type="text" name="name" value="">
<label for="bild">Bild:</label> <input name="bild" type="file" size="50" accept="image/png,image/jpeg,image/gif">
<input type="submit" value="Bild hochladen">
</form>
 {/if}
</li>
{/for}
</ol>

<h2>Nutzungsbedingungen</h2>
Du hast bereits folgenden Nutzungsbedingungen zugestimmt:
<pre>
{$projekt.tos}
</pre>

{/if}
