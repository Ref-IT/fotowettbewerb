<h1>Verwaltung des Bereiches {$bereich.name|escape}</h1>

{if isset($projekt)}

{* Projekt bearbeiten *}

<h2>Projekt {$projekt.name|escape}</h2>

<p><a href="index.php?uuid={$projekt.uuid|escape:url}">Link für Teilnehmer: {$PHP_BASE|escape}/index.php?uuid={$projekt.uuid|escape}</a></p>

<form action="bereichadmin.php" method="POST" enctype="multipart/form-data">
<fieldset>
<legend>Projekt bearbeiten</legend>
 <input type="hidden" name="action" value="changeprojekt">
 <input type="hidden" name="bereich_id" value="{$bereich.id|escape:url}">
 <input type="hidden" name="id" value="{$projekt.id|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
<ul class="table">
 <li> <label for="name">Name:</label> <input type="text" name="name" value="{$projekt.name|escape}"> </li>
 <li> <label for="start">Start:</label> <input class="datetimepicker" id="pstart" type="datetime-local" name="start" value="{$projekt.start|date_format:"%Y-%m-%dT%H:%M:%S"}"> </li>
 <li> <label for="end">Ende:</label> <input class="datetimepicker" id="pend" type="datetime-local" name="end" value="{if $projekt.end}{$projekt.end|date_format:"%Y-%m-%dT%H:%M:%S"}{/if}"> </li>
 <li> <label for="tos">AGB:</label> <textarea name="tos" style="vertical-align:text-top; min-width: 600px;">{if $projekt.tos}{$projekt.tos|escape}{/if}</textarea> </li>
 <li> <label for="logo">Logo:</label> <span>
      <input name="logo" type="file" size="50" accept="image/png,image/jpeg,image/gif">
      {if $projekt.logo_data}
        <br/><img src="data:{$projekt.logo_mimetype};base64,{base64_encode($projekt.logo_data)}">
        <br/><input type="checkbox" name="clearlogo" value="1"> aktuelles Logo entfernen
      {/if}
      </span>
 <li> <label for="numSlot">maximale Anzahl der Bilder:</label> <input type="number" name="numSlot" value="{$projekt.numSlot|escape}"> </li>
 <li> <label for="contact">Kontakt:</label> <input type="email" name="contact" value="{if $projekt.contact}{$projekt.contact|escape}{/if}"> </li>
</ul>
 <input type="submit" value="Daten ändern">
</fieldset>
</form>

<form action="bereichadmin.php" method="POST">
<fieldset>
<legend>Projekt löschen</legend>
 <input type="hidden" name="action" value="delprojekt">
 <input type="hidden" name="bereich_id" value="{$bereich.id|escape:url}">
 <input type="hidden" name="id" value="{$projekt.id|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
 <input type="submit" value="Projekt löschen">
 <input type="checkbox" name="deldata" value="1"> <label for="deldata">hochgeladene Bilder mit entfernen</label>
</fieldset>
</form>

<form action="bereichadmin.php" method="POST">
<fieldset>
<legend>Bilder herunterladen</legend>
 <input type="hidden" name="action" value="download">
 <input type="hidden" name="bereich_id" value="{$bereich.id|escape:url}">
 <input type="hidden" name="id" value="{$projekt.id|escape}">
 <input type="hidden" name="csrf" value="{$csrf|escape}">
 <input type="submit" value="Bilder herunterladen">
</fieldset>
</form>

{/if}

<h2>Projekte</h2>

{* Liste der Projekte *}
<ul>
{foreach $projekte as $p}
<li><a href="bereichadmin.php?id={$p.id|escape:url}&amp;bereich_id={$bereich.id|escape:url}">{$p.name|escape}</a>
{foreachelse}
<li>Keine Projekte vorhanden.
{/foreach}
</ul>

{* Neues Projekt anlegen *}
<h2>Neues Projekt</h2>
<form action="bereichadmin.php" method="POST">
<input type="hidden" name="action" value="newprojekt">
<input type="hidden" name="bereich_id" value="{$bereich.id|escape:url}">
<input type="hidden" name="csrf" value="{$csrf|escape}">
<label for="name">Name:</label><input type="text" name="name" placeholder="Name"> <input type="submit" value="anlegen">
</form>

