# Blogger - Redaxo 5.x Blog AddOn

Not production ready.


## TODOS

* Restructure database and how it is accessed
* Own class for the Backend Pages
* After create PID is 0


## Usage

Coming soon...


## How it Works

In work...

### Database Tables

`rex_blogger_entries` contains the meta-data for any entry

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