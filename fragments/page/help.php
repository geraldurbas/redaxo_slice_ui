<p>FDL Copy fügt jedem Slice bis zu drei neue Icons hinzu. Dabei wird berücksichtigt, welche Aktion von einem Benutzer gewählt wurde. Wählt der Benutzer die Funktion Ausschneiden, kann er den Slice nicht nach sich selbst einfügen.</p>
<p>@todo :)</p>
<h3>Entwickler</h3>
<p>Folgende Einstellungen können Entwickler in den Slices nutzen:</p>
<h4>JSON-Block</h4>
<p>Bekommt der übergeordnete Container - meist das Fieldset - die Klasse <b>class="ui_json_blocks"</b> können die Attribute data-min="[0-9]" und data-max="[0-9]" verwendet werden, um den Benutzer zu zwingen eine genaue Anzahl Blocks zu erstellen. Damit können zum Beispiel Galerien und Grids realisiert werden.</p>
<p>Mit folgendem Beispiel müssen mindestens 2 Blöcke definiert werden. Es dürfen aber maximal 4 definiert werden.</p>
<hr>
<code><?php highlight_file('codes/fieldset_config.php');?></code>
<hr>