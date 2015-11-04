#Slice UI

Slice UI erweitert Slices um weitere Funktionen wie kopieren, ausschneiden und aktivieren bzw. deaktivieren.

##Kopieren, Ausschneiden

![Neue Butons](/../assets/slice_ui.png?raw=true)

Ein Slice kann kopiert oder ausgeschnitten werden. Dabei ist zu beachten dass Ausschneiden den Slice an einer neuen Stelle hinzufügt und den Ausgangsslice löscht. Der Slice bekommt somit eine neue ID.

Nach dem ein Slice kopiert wurde, kann er direkt an den Anfang eingefügt werden. Die Buttons dafür erscheinen direkt nachdem der Kopier-Button eines Slices aktiviert wurde. Außerdem wird der Kopier-Button durch einen Einfüge-Button ersetzt. Aktiviert man diesen Button, wird der kopierte Slice direkt im Anschluss eingefügt.

![Neue Butons](/../assets/slice_ui_copied.png?raw=true)

##Aktivieren/Deaktivieren

Slice UI bietet die Funktion an, Slices zu aktivieren bzw. zu deaktivieren. Diese Funktion ist bereits aus dem Addon slice_status für Redaxo4 bekannt. 

##Clipboard löschen

Ein Slice kann unendlich oft an beliebiger Stelle eingefügt werden. Nachdem der Slice nicht mehr gebraucht wird, kann er über den Button `Clipboard löschen` aus dem Kopier-Cache entfernt werden.

##Features

- Slice-Groups damit eine Gruppe Slices kopiert und verschoben werden kann.
- Drag and Drop Sortierung
- MoveUp/Down in dieses Addon integrieren
- Viele Buttons ggf. in ein Dropdown stecken
- STRG/CMD + Klick editiert den Slice
- STRG/CMD + C kopiert einen Slice
- STRG/CMD + V fügt den Slice am Anfang eines Artikels ein

###Article UI

Vermutlich wäre es sinnvoll nach Slice UI die usability für Artikel zu erhöhen. Features könnten ebenfalls via Drag & Drop sortiert werden. In der Slice-Ansicht könnten zusätzlich die Artikel-Einstellungen im Head-Bereich erreichbar sein.

Die Javascript-Funktionen sollten standartisiert werden, damit weitere Addons auf diese Zurückgreifen können. 



