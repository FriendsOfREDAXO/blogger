# Blogger - Redaxo 5.x Blog AddOn

Not production ready.

## TODOS

* Restructure database and how it is accessed
* Own class for the Backend Pages

## Usage



### Database structure


#### Tables

* rex_blogger_entries
  * id
  * category
  * tags
  * status
  * content
  * postedAt
  * postedBy
* rex_blogger_content
  * id
  * pid
  * clang
  * title
  * text
  * preview
  * gallery
* rex_blogger_categories
  * id
  * name
* rex_blogger_tags
  * id
  * tag