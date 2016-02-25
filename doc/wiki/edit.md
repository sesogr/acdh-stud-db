# Datensätze bearbeiten

Die Datensätze werden in einer MySQL-Datenbank und können mit einem generischen Administrationstool wie z. B. PhpMyAdmin
gepflegt werden. Da mit einem solchen Tool auch Änderungen durchgeführt werden können, welche die Integrität der Daten
verletzen und Datenbestände gar unwiederbringlich löschen könnten, wird dringend empfohlen, dass diese Änderungen von
jemandem mit entsprechender Erfahrung durchgeführt werden.

## Allgemeiner Aufbau

Die Datensätze werden in mehreren Datenbanktabellen verwaltet, die an einem gemeinsamen Präfix erkennbar sind. Es wird
im Folgenden angenommen, dass dieses Präfix »student_« lautet.

»student_identity«
:	bildet die Basis der Personendatensätze und verwaltet Personen-IDs und die zugehörigen Zeiträume, aus denen die
	erfassten Daten stammen.

»student_..._value«-Tabellen
:	verwalten alle aufgezeichneten Daten zu einer bestimmten persönlichen oder biografischen Eigenschaft und ordnen sie
	der entsprechenden Person über deren ID zu.
	
	Jede dieser Tabellen hat eine fortlaufende »id«-Spalte und eine »person_id« zum Verweis auf die betreffende Person.
	Daneben gibt es schließlich eine weitere Spalte für den Aspekt-bezogenen Wert. Einige dieser Tabellen haben mehrere
	Wert-Spalten, in diesem Falle werden Sie in der nachfolgenden Übersicht erläutert.

	»student_biography_value«
	:	»Biographie«
	
	»student_birth_date_value«
	:	»Geburtsdatum« als menschenlesbarer Freitext in »birth_date« mit maschinenverwertbarer Eingrenzung auf den
		Datumsbereich zwischen »born_on_or_after« und »born_on_or_before«.
	
	»student_birth_place_value«
	:	»Geburtsort« in »birth_place«, Geburtsland zum Zeitpunkt der Geburt in »birth_country_historic« und dessen heutiges
		Staatsgebiet in »birth_country_today«
	
	»student_ethnicity_value«
	:	»Volkszugehörigkeit«
	
	»student_father_value«
	:	»Vater«
	
	»student_gender_value«
	:	»Geschlecht«
	
	»student_given_names_value«
	:	»Vorname«
	
	»student_graduation_value«
	:	»Promotion«
	
	»student_guardian_value«
	:	»Vormund«
	
	»student_language_value«
	:	»Muttersprache«
	
	»student_last_name_value«
	:	»Name« (Familienname)
	
	»student_last_school_value«
	:	»Letzte Schule«
	
	»student_literature_value«
	:	»Literaturhinweise«
	
	»student_nationality_value«
	:	»Staatsbürgerschaft«
	
	»student_religion_value«
	:	»Religion«
	
	»student_remarks_value«
	:	»Bemerkungen«
	
	»student_studying_address_value«
	:	»Wohnadr. (Studium)«

»student_..._time«-Tabellen
:	geben den Zeitraum der Gültigkeit einer persönlichen oder biografischen Eigenschaft aus der zugehörigen
	»student_..._value«-Tabelle an.

»student_attendance«
:	verzeichnet alle Informationen zur Teilnahme an Lehrveranstaltungen und ordnet sie der entsprechenden Person über
	deren ID zu.

## Typische Anwendungsfälle

### Ermitteln der Personen-ID

Als zentrales Identifikationsmerkmal ist für die Bearbeitung der Datensätze immer die ID der betreffenden Person
vonnöten. Um diese zu ermitteln, wird eine reguläre Suche mit der browserbasierten Suchfunktion durchgeführt. In der
Ergebnisliste wird die Person ausgewählt und es erscheint deren Detailansicht mit allen persönlichen Daten und der Liste
der Lehrveranstaltungen. In der Adressleiste steht nun ganz am Ende der URL »?id=«, gefolgt von einer Nummer. Diese
Nummer ist die Personen-ID, die für alle weiteren Aktionen benötigt wird.

### Korrigieren von Angaben zur Teilnahme an einer Lehrveranstaltung

Suchen Sie in der Tabelle »student_attendance« alle Datensätze, bei denen die Spalte »person_id« mit der Personen-ID
übereinstimmt. Korrigieren Sie die entsprechende Lehrveranstaltung, indem Sie die Werte der nachfolgend aufgelisteten
Felder bearbeiten.

»semester_abs«
:	Semesterangabe, bestehend aus Saisonkennung und Jahreszahl(en), wie in der Spalte »Semester« in der Liste der 
	Lehrveranstaltungen

»semester_rel«
:	Die Nummer des Semesters aus Sicht der Studenten, wie in der Spalte »ordinal«

»lecturer«
:	Der »Dozent« der Lehrveranstaltungs

»class«
:	Der Titel der Lehrveranstaltung, wie in der Spalte »Vorlesung«

»class_extra«
:	Zusätzliche Angaben zur Lehrveranstaltung, wie in der Spalte »Zusatz«

»remarks«
:	»Bemerkungen« zur Teilnahme

### Erfassen der Teilnahme an einer weiteren Lehrveranstaltung

Fügen Sie in die Tabelle »student_attendance« einen weiteren Datensatz ein. Lassen Sie die »id« automatisch vergeben,
geben Sie als »person_id« die Personen-ID an und tragen Sie in die übrigen Spalten die entsprechenden Angaben zur
Lehrveranstaltung ein. Eine Erklärung zu diesen Spalten finden Sie im vorhergehenden Abschnitt »Korrigieren von Angaben
zur Teilnahme an einer Lehrveranstaltung«.

### Korrigieren einer persönlichen oder biografischen Angabe

Die persönlichen Daten zu einer Person (abgesehen von der Teilnahme an Lehrveranstaltungen) sind auf mehrere Tabellen
verteilt. Zu jedem Aspekt gibt es eine Tabelle mit Eigenschaftswerten (»student_..._value«). Die Ellipsen stehen dabei
als Platzhalter für den jeweiligen Aspekt. Eine Übersicht über die Aspekte finden Sie weiter oben im Abschnitt
»Allgemeiner Aufbau«.

In einer solchen Tabelle können zu jeder Person mehrere Einträge mit unterschiedlichen Werten erfasst werden. Daher hat
jeder Eintrag eine eigene ID. In der zugehörigen »student_..._time«-Tabelle wird dann mit der Spalte »value_id« auf
diese ID verwiesen und der Zeitraum der Gültigkeit dieses Wertes angegeben. Auf diese Weise können auch Angaben
festgehalten werden, die sich im Laufe des Studiums verändert haben und in welchem Semester sie jeweils galten. Dazu
wird in der Spalte »time« ein menschenlesbarer Zeitraum angegeben, der zur maschinellen Auswertung außerdem in
Jahreszahlen bei »year_min« und »year_max« einzutragen ist.

### Erfassen einer weiteren persönlichen Angabe

Erstellen Sie in der jeweiligen »student_..._value«-Tabelle einen neuen Datensatz. Lassen Sie dabei die »id« automatisch
vergeben, tragen Sie bei »person_id« die Personen_ID ein und füllen Sie die entsprechenden Spalten zum gewählten Aspekt
aus. Erläuterungen zu den jeweiligen Wert-Tabellen finden Sie weiter oben im Abschnitt »Allgemeiner Aufbau«. Die dabei
automatisch vergebene Wert-ID wird im nächsten Schritt benötigt.

Erstellen Sie dann in der zugehörigen »student_..._time«-Tabelle ebenfalls einen neuen Datensatz. Die »id« lassen Sie
automatisch vergeben, als »value_id« tragen Sie die zuvor erzeugte Wert-ID ein und mit den übrigen Spalten geben Sie,
wie im vorherigen Abschnitt »Korrigieren einer persönlichen oder biografischen Angabe« erläutert, den
Gültigkeitszeitraum des hinzugefügten Wertes an.
