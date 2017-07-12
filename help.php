<style type="text/css">
	.field {
		margin-bottom: 60px;
	}
	.inner-field {
		padding-left: 25px;
	}
</style>

<div class="field">
<p>Mit "blogger" können <strong>Blog-Einträge</strong>, <strong>Kategorien</strong> und <strong>Tags</strong> angelegt werden.<br>
Vorgefertigte Funktionen erlauben das benutzen dieser.</p>
</div>

<div class="field">
<h4>Bestandteile</h4>
<p>Ein <strong>Eintrag</strong> besteht aus:
<ul>
	<li>einer ID</li>
	<li>einer Artikel ID</li>
	<li>einen Übersetzungs-Status</li>
	<li>einer clang ID (Sprache)</li>
	<li>einer Kategorie</li>
	<li>einem Preview-Bild</li>
	<li>einer Headline</li>
	<li>dem Content</li>
	<li>einer Galerie</li>
	<li>ein oder mehreren Tags</li>
	<li>einem Offline-Status</li>
	<li>einem Post-Tag (Datum der Veröffentlichung)</li>
	<li>einem createdAt Wert</li>
	<li>einem createdBy Wert</li>
	<li>einem updatedAt Wert</li>
	<li>einem updatedBy Wert</li>
</ul>
</p>

<p>Eine <strong>Kategorie</strong> besteht aus:
<ul>
	<li>einer ID</li>
	<li>ihrem Namen</li>
</ul>
</p>

<p>Ein <strong>Tag</strong> besteht aus:
<ul>
	<li>einer ID</li>
	<li>seinem Namen</li>
</ul>
</p>
</div>

<div class="field">
<h4>Bestandteile Eintrag</h4>
<div class="inner-field">
<p>
<strong>ID</strong><br>
Standard MySQL ID Auto-Increment.
</p>
<p>
<strong>Artikel ID</strong><br>
Grundsätzlich der selbe Wert wie in ID.<br>
Sollte der Eintrag allerdings eine Übersetzung für einen bereits bestehenden Eintrag sein,<br>
so wird die ID des Eintrags übernommen, der übersetzt worden ist.
</p>
<p>
<strong>Übersetzungs-Status</strong><br>
Gibt an ob der Eintrag eine Übersetzung (1) oder ein Normaler (0) Artikel ist.
</p>
<p>
<strong>clang ID</strong><br>
Die Sprach ID des jeweiligen Artikels.
</p>
<p>
<strong>Kategorie</strong><br>
Die ID der ausgewählten Kategorie. Standardmäßig 1.
</p>
<p>
<strong>Preview-Bild</strong><br>
Name einer Datei im Medienpool.
</p>
<p>
<strong>Headline</strong><br>
Text der als Headline (Überschrift) benutzt werden kann.
</p>
<p>
<strong>Content</strong><br>
Redactor2 Textarea.
</p>
<p>
<strong>Galerie</strong><br>
Redaxo Medienliste.
</p>
<p>
<strong>Tags</strong><br>
ID's der ausgewählten Tags.
</p>
<p>
<strong>Offline-Status</strong><br>
Ist der Eintrag Online (0) oder Offline (1).
</p>
<p>
<strong>Post-Tag</strong><br>
MySql Datetime.
</p>
<p>
<strong>createdAt</strong><br>
MySql Datetime bei erstellen.
</p>
<p>
<strong>createdBy</strong><br>
Redaxo User ID bei erstellen.
</p>
<p>
<strong>updatedAt</strong><br>
MySql Datetime bei updated.
</p>
<p>
<strong>updatedBy</strong><br>
Redaxo User ID bei updated.
</p>
</div>
<p><br>Dies sind die Werte die in der Datenbank gespeichert werden.<br>
Kategorien und Tags besitzen keine Übersetzungen.</p>
</div>

<div class="field">
<h4>Verwendung</h4>
<p>blogger kommt mit vorgefertigen Klassen die benutzt werden können um zugriff auf die Einträge zu haben.</p>
<p>Mit der Klasse "rex_blogger" kann man eine "Seite" erstellen.<br>
Wie viele Einträge pro Seite angezeigt werden sollen, wird als Argument an den Constructor übergeben.<br>
</p>
<p>
<strong>Beachte:</strong> Es werden nur Einträge ausgegeben, die der aktuellen clang entsprechen.<br>
</p>
<p>
Nachdem die Seite erstellt wurde, kann man überprüfen, ob der Nutzer einen Einzelnen Eintrag ansehen will<br>
oder eine bestimmte Seite angezeigt haben möchte.<br>
<br>
Bei einer Seite, wird die Funktion "get_entries_blog_page()" ein Array zurückgeben, dass Alle Einträge dieser Seite erhält.<br>
Die Einträge bestehen aus Objekten der Klasse "rex_blogger_entry".<br>
Mit den getter-Funktionen dieser Klasse können die Einzelnen bestandteile eines Eintrags ausgegeben werden.<br>
<p>
<strong>Beachte:</strong> Die Kategorie wird als deren Namen zurückgegeben nicht als ID.<br>
Ebenso wie Tags als Array derer Namen zurückgegeben wird.
</p>
<br>
<p><strong>Die getter-Funktionen von "rex_blogger_entry":</strong></p>
<pre>
getId();
getArtId();
getTranslation();
getClang();
getCategory();
getPreview();
getHeadline();
getContent();
getGallery();
getTags();
isOffline();
getPostDate();
getCreatedBy();
getCreatedAt();
getUpdatedBy();
getUpdatedAt();
getUrl();		// returns the url for current entry
</pre>

</div>


<div class="field">
<h4>Weitere Funktionen</h4>
<pre>
/**
 * gibt true zurück, wenn ein bestimmter Eintrag angefordert wird
 * ansonsten false
 */
is_single_entry();

/**
 * gibt eine Einheit der Klasse rex_blogger_entry
 * mit den Daten des angeforderten Eintrags zurück
 */
get_single_entry();

/**
 * gibt die Maximale Anzahl der Seiten zurück
 */
get_max_page_number();

/**
 * gibt die URL als String für die nächste/vorherige Seite zurück
 */
get_url_next_page();
get_url_previous_page();

/**
 * gibt die URL für eine bestimmte Seite zurück ($page ist ein integer)
 */
get_url_page($page);
</pre>

<p>Static Functions</p>
<pre>
/**
 * Eintrag mit bestimmter ID als rex_blogger_entry ($id ist ein integer)
 */
get_by_id($id);

/**
 * Array aller Tags
 */
get_tags();

 /**
  * Array aller Kategorien
  */
get_categories();
</pre>
</div>


<div class="field">
<h4>Beispiel</h4>
<p>Ein Beispiel für die Ausgabe eines Moduls befindet sich in "output.php".</p>
</div>