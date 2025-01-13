# Blogger - Redaxo 5.x Blog

## Usage

Create a new Blogger instance like this:

```php
$myBlogger = new Blogger();
```

With this new blogger instance different data can be accessed.
For example you can get all Entries with a function or just a single one.
For now blogger only gives you access to the data it created, this means that you're responsible for your urls and how they are handled. See [examples](https://github.com/FriendsOfREDAXO/blogger/tree/main/examples).

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

### Friends Of REDAXO

<https://www.redaxo.org>
<https://github.com/FriendsOfREDAXO>

Umsetzung:
[AndyBitz](https://github.com/AndyBitz/)

## Projekt-Lead  

[Alexander Walther](https://github.com/alexplusde)
