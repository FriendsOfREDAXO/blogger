# Blogger - Redaxo 5.x Blog

## Migration von `Blogger` zu `FriendsOfREEAXO\Neues` 4.1

### Warum der Wechsel?

Das FOR-Addon News-Manager und das FOR-Addon Blogger befinden sich nicht mehr in aktiver Entwicklung. Sie wurden nur noch bis Ende 2022 bzw. Anfang 2024 gewartet. Potentielle Sicherheitslücken werden nicht mehr geschlossen.

Um die Lücke zu schließen, wurde das Addon `Neues` von @alexplus_de zu FriendsOfREDAXO übertragen. Die Weiterentwicklung des Addons "Neues" ist gesichert. Es wird ständig an die neuesten REDAXO-Versionen angepasst und erweitert.

Ein wesentlicher Vorteil gegenüber News Manager oder Blogger ist die Unterstützung von YForm. Damit lassen sich die News-Einträge und Kategorien komfortabel verwalten und erweitern, viele Funktionen von YForm und YOrm können genutzt werden.

Wir danken Alex für die Bereitschaft, das Addon in die Hände von FriendsOfREDAXO zu geben, Alex bleibt Projekt-Lead des Addons. Sowie @schorschy @skerbis und @eace für die Unterstützung bei der Entwicklung.

### Funktions-Parität und Unterschiede

| Was                                  | News Manager `3.0.3`                        | Blogger `1.3.2` | Neues `^4.1`                                               |
| ------------------------------------ | ------------------------------------------- | --------------- | ---------------------------------------------------------- |
| Letzte Weiterentwicklung und Wartung | ❌ 28. Dez. 2022                             | ❌ 31. März 2024 | ✅ aktuell                                                  |
| REDAXO Core-Version                  | ab `^5.4`                                   | ❌ n/a             | ab `^5.15`                                                 |
| PHP-Version                          | ab `^5.6`                                   | ❌ n/a             | ab `^7.2`                                                  |
| Addon-Abhängigkeiten                 | URL ab `^2`                                 | Keine           | URL ab `^2`, YForm ab `^4`, YForm Field ab `^2`            |
| Position im Backend                  | `Addons > News Manager`                     | `Addons > Blogger` | `Aktuelles` (oben)                                         |
| News-Übersicht                       | ✅ `News Manager > "News anlegen"`           | `Blogger > Einträge` | ✅ `Aktuelles > Einträge`                                   |
| Kategorien                           | ✅ `News Manager > "Kategorien"`             | `Blogger > Kategorien` | ✅ `Aktuelles > Kategorien`                                 |
| Kommentare                           | ✅ als Plugin: `News Manager > "Kommentare"` | ❌ nein         | ❌ nein                                                     |
| Autoren                              | ❌ nein                                      | ❌ nein         | ✅ `Aktuelles > Autoren`                                      |
| Mehrsprachigkeit                     | ✅ `News Manager > (Sprache auswählen)`      | ❌ nein         | ✅ `Aktuelles > Sprachen`                                   |
| Dokumentation                        | ✅ als Plugin                                | ❌ nein         | ✅ `Aktuelles > Hilfe`                                      |
| Einstellungen                        | ❌ nein                                      | `Blogger > Einstellungen` | ✅ `Aktuelles > Einstellungen`                              |
| WYSIWYG-Editor                       | ✅ ausschließlich `redactor2`                | ✅ frei wählbar | ✅ frei wählbar (`cke5`, `redactor`, `markitup`, `tinymce`) |
| Backend-Sprachen                     | ✅ ja `de,en,es,se`                          | ✅ ja `de, en`  | ✅ ja `de,en,es,fr,it,se`                                   |
| RSS                                  | ✅ ja                                        | ❌ nein         | ✅ ja                                                       |
| Fertige Fragmente                    | ✅ ja                                        | ✅ ja           | ✅ ja                                                       |
| Multi-Domain-Unterstützung           | ❌ über Umwege                               | ❌ n/a             | ✅ ja                                                       |
| Frei erweiterbare Felder             | ❌ nein                                      | ❌ nein         | ✅ ja (via YForm)                                           |
| YOrm-Model                           | ❌ nein                                      | ❌ nein         | ✅ ja (News-Einträge, Kategorien, Autoren, Sprachen)        |
| CSV-Import                           | ❌ nein                                      | ❌ nein         | ✅ ja (via YForm)                                           |
| CSV-Export                           | ❌ nein                                      | ❌ nein         | ✅ ja (via YForm)                                           |
| RESTful API                          | ❌ nein                                      | ❌ nein         | ✅ ja (via YForm)                                           |

### Migration von Blogger zu Neues

#### Automatische Daten-Migration von Blogger zu Neues 4.1

Es wird eine automatische Migration von Blogger-Einträgen zu Neues 4.1.

Diese liegt der finalen Version des News Managers bei. Alternativ müssen folgenden Schritte erfolgen.

#### Manuelle Daten-Migration von Blogger zu Neues 4

1. Backup der Datenbank und des Dateisystems
2. `Neues` installieren (`YForm`, `YForm Field`, `URL` müssen bereits installiert und aktiviert sein)
3. Bestehende News-Einträge und Kategorien in Neues importieren
4. Module, Templates und URL-Profile anpassen
5. `News Manager` deinstallieren.

#### SQL-Befehle zur Migration der Daten von Blogger zu Neues 4

> Hinweis: Die Tags müssen manuell oder mit eigenen Anpassungen übertragen werden, da es hierfür eine eigene Tabelle gibt.

folgt...

```SQL
```

## Usage

Create a new Blogger instance like this:
```php
$myBlogger = new Blogger();
```

With this new blogger instance different data can be accessed.
For example you can get all Entries with a function or just a single one.
For now blogger only gives you access to the data it created, this means that you're responsible for your urls and how they are handled. See [examples](https://github.com/AndyBitz/rex_blogger/tree/master/examples). 

```php
// get all entries
$allEntries = $myBlogger->getEntries();

// $allEntries looks like this
array(
  0 => array(
    "id" => 1,
    "categoryId" => 1,
    "categoryName" => "DefaultCategory",
    "tags" => array(
      0 => array(
        "id" => 1,
        "tag" => "DefaultTag"
      ),
      1 => array(
        "id" => 2,
        "tag" => "ExtraTag"
      )
    ),
    "status" => 0,
    "postedBy" => "admin",
    "postedAt" => "2017-07-07 12:30:00",
    "content" => array(
      1 => array(
        "clang" => 1,
        "title" => "Die andere Welt",
        "text" => "<p>Von einer anderen Welt.</p>",
        "preview" => "",
        "gallery" => ""
      ),
      2 => array(
        "clang" => 2,
        "title" => "The other World",
        "text" => "<p>From another World</p>",
        "preview" => "",
        "gallery" => ""
      )
    )
  ),
  ...
)

// or give a limit to if you only want to show 10 entries per page
$pageEntries = $myBlogger->getEntries("0, 10");
$nextPageEntries = $myBlogger->getEntries("10, 10");

// you can use getLastEntries() with the same use but the output is orderd
// with the latest entries coming first

// similiar, this would look like $allEntries[0]
$singleEntry = $myBlogger->getEntry(1);
```

If you need the tags and categories, e.g. so the user can select all entries from a specific category, you can get the data through these functions.
```php
$myBlogger->getCategories();
// returns array( array( 'id' => 1, 'name' => 'myCategory' ), ... )

$myBlogger->getTags();
// returns array( array( 'id' => 1, 'tag' => 'myTag' ), ... )
```

The same works for the year and/or month of the post date.
```php
$myBlogger->getMonths();
// returns array( array( 'year' => 2017, 'month' => 7 ), ... )
```

You can use the `Blogger::getEntriesBy` function to query for specific entries with an associative array.
```php
$entriesFrom = $myBlogger->getEntriesBy(array(
  'category' => 1,
  'tags' => [1, 2, 4]
  'year' => 2017,
  'month' => 3,
  'author' => 'admin',
  'limit' => '0, 10',
  'latest' => true, // default false
  'includeOfflines' => true // default false
));

// You don't have to use everything.
// If you call getEntriesBy without any parameters, you'll just get all entries.
```


### Database Tables

`rex_blogger_entries` contains the meta-data for all entries

* rex_blogger_entries
  * id
  * category
  * tags
  * status
  * postedBy
  * postedAt

`rex_blogger_content` contains the content for each entry in diffrent languages

* rex_blogger_content
  * id
  * pid
  * clang
  * title
  * text
  * preview
  * gallery

categories every entry can have

* rex_blogger_categories
  * id
  * name

tags every entry can have

* rex_blogger_tags
  * id
  * tag

## Lizenz

MIT Lizenz, siehe [LICENSE.md](https://github.com/friendsofredaxo/rex_blogger/blob/main/LICENSE)  

## Autor

Umsetzung:
[AndyBitz](https://github.com/AndyBitz/)

**Projekt-Lead**  
[Alexander Walther](https://github.com/alexplusde)
