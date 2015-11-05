#Slice UI

Slice UI erweitert Slices um weitere Funktionen wie kopieren, ausschneiden und aktivieren bzw. deaktivieren.

##Features

- Move Up/Down ohne reload
- Online/Offline ohne reload
- Slices sortieren mit Drag&Drop
- Online von / bis
- Alle Slices löschen / Gewählte Slices löschen
- Slice-Buttons scrollen mit
- Slices kopieren
- Slices ausschneiden
- Fast alles optional
- Auf bestimmte Module beschränken
- Einige neue Extension Points für Entwickler

##Funktionen

###Kopieren, Ausschneiden

![Neue Butons](/../assets/slice_ui.png?raw=true)

Ein Slice kann kopiert oder ausgeschnitten werden. Dabei ist zu beachten dass Ausschneiden den Slice an einer neuen Stelle hinzufügt und den Ausgangsslice löscht. Der Slice bekommt somit eine neue ID.

Nach dem ein Slice kopiert wurde, kann er direkt an den Anfang eingefügt werden. Die Buttons dafür erscheinen direkt nachdem der Kopier-Button eines Slices aktiviert wurde. Außerdem wird der Kopier-Button durch einen Einfüge-Button ersetzt. Aktiviert man diesen Button, wird der kopierte Slice direkt im Anschluss eingefügt.

![Neue Butons](/../assets/slice_ui_copied.png?raw=true)

###Aktivieren/Deaktivieren

Slice UI bietet die Funktion an, Slices zu aktivieren bzw. zu deaktivieren. Diese Funktion ist bereits aus dem Addon slice_status für Redaxo4 bekannt. 

###Clipboard löschen

Ein Slice kann unendlich oft an beliebiger Stelle eingefügt werden. Nachdem der Slice nicht mehr gebraucht wird, kann er über den Button `Clipboard löschen` aus dem Kopier-Cache entfernt werden.

##Konfiguration

Die Funktionen lassen sich Teilweise konfigurieren. So wurde in der ersten Version entschieden, dass es nicht möglich sein muss, einen Slice zu kopieren, aber es sollte möglich sein ihn an beliebiger Stelle einzufügen, falls dass System es zulässt.

Die Kopierfunktion kann auf einzelne Module und auch auf einzelne CTypes beschränkt werden. Mit jedem Template wird das Konfigurations-Formular erweitert um dessen CTypes.

![Konfiguration](/../assets/slice_ui_settings.png?raw=true)

###Allgemeine Einstellungen

####Kopieren/Ausschneiden aktivieren

Diese Option aktiviert alle Kopier-, Ausschneide- und Einfügeoptionen.

####Slices sortieren mit Drag&Drop

Slices sortieren via Drag&Drop. Diese Option fügt ein Icon hinzu, mit dem man Slices sortieren kann. Das Icon wurde gewählt, damit Redakteure nicht versehentlich Slices verschieben.

####Online/Offline-Icon hinzufügen

Nach aktivieren dieser Option, kann der Redakteur Slices online bzw. offline schalten.

####Slice hoch/runter bewegen beibehalten

Das Addon entfernt die Buttons `Slice hoch bewegen` und `Slice runter bewegen`. Diese Option verhindert das, da Slices mit viel Output umständlicher zu bewegen sind.

####Sticky Header

Die Option `Slice-Buttons sollen mit-scrollen` lädt ein Javascript welches die Buttons über den Slices mir-scrollen lässt. Diese Option ist in Artikeln mit sehr vielen Slices hilfreich, wenn das Clipboard geleert werden, oder das Frontend angezeigt werden soll. 

###Online von/bis

Slices können mit dieser Option für einen bestimmten Zeitrahmen aktiviert werden.

![Neue Butons](/../assets/online_offline.png?raw=true)

##Extension points

Diese Extension points sind eingebaut, aber noch nicht getestet.

####SLICE_PASTED

Wird ausgeführt nachdem der Slice eingefügt wurde. Übergibt alle Parameter die SLICE_ADDED übergibt.

####SLICE_COPIED

Wird ausgeführt nachdem ein Slice kopiert oder ausgeschnitten wurde. Mit dem Parameter cut=1 kann geprüft werden, ob kopiert oder ausgeschnitten wurde.

####SLICE_TOGGLED

Wird ausgeführt, wenn ein Slice aktiviert bzw. deaktiviert wird.

####ADD_SLICE_FORMS

Kann dazu verwendet werden, weitere Felder über einen Slice zu schreiben.

####EXTEND_SLICE_FORMS

Erweitert das Formular um weitere Felder.

####ADD_AFTER_SLICE_FORMS

Fügt Inhalte an das Ende des Online/Offline Formulars ein.

####ADD_AFTER_SLICE 

Fügt einen Footer nach einem Slice ein.

##Plugins

###Slice Footer

Slice Footer unterstützt in der Initialversion lediglich die "Alle Slices löschen" und "Alle ausgewählten Slices löschen". Jeder Slice erhält einen Footer mit erstmals einem Icon für die "Alle ausgewählten Slices löschen"-Funktion.

##Features

- Slice-Groups damit eine Gruppe Slices kopiert und verschoben werden kann.
- Viele Buttons ggf. in ein Dropdown stecken
- STRG/CMD + Klick editiert den Slice
- STRG/CMD + C kopiert einen Slice
- STRG/CMD + V fügt den Slice am Anfang eines Artikels ein


###Article UI

Vermutlich wäre es sinnvoll nach Slice UI die usability für Artikel zu erhöhen. Features könnten ebenfalls via Drag & Drop sortiert werden. In der Slice-Ansicht könnten zusätzlich die Artikel-Einstellungen im Head-Bereich erreichbar sein.

Die Javascript-Funktionen sollten standartisiert werden, damit weitere Addons auf diese Zurückgreifen können. 
