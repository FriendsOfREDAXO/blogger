<?php 

$myBlogger = new Blogger();

// // entries
// dump($myBlogger->getEntries());
// dump($myBlogger->getEntry(1));

// // categories
// dump($myBlogger->getCategories());

// // tags
// dump($myBlogger->getTags());

// // time
// dump($myBlogger->getMonths());

// // get entries by category, tags, month, year, author

// // get entries which were posted by 'admin' in the year 1337
// dump($myBlogger->getEntriesBy(null, [], null, 1337, 'admin'));

// // get entries which have the category 2 and the tags 1 and 3
// dump($myBlogger->getEntriesBy(2, [1, 3]));

