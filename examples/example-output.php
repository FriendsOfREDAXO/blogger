<?php 

$myBlogger = new Blogger();

// entries
dump($myBlogger->getEntries());

// limit the entries, e.g for pages
dump($myBlogger->getEntries("0, 2"));
dump($myBlogger->getEntries("2, 2"));
dump($myBlogger->getEntries("4, 2"));

// get a single entry
dump($myBlogger->getEntry(1));

// categories
dump($myBlogger->getCategories());

// tags
dump($myBlogger->getTags());

// time
dump($myBlogger->getMonths());

// get entries by category, tags, month, year and author

// get entries which were posted by 'admin' in the year 1337
dump($myBlogger->getEntriesBy(array(
  'year' => 1337,
  'author' => 'admin'
)));

// get entries which have the category 2 and the tags 1 and 3
// and show only 10 results from 1 on
dump($myBlogger->getEntriesBy(array(
  'category' => 2,
  'tags' => [1, 2],
  'limit' => '0, 10'
)));
