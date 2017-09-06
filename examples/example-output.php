<?php 

$myBlogger = new Blogger();

// entries
dump($myBlogger->getEntries());
dump($myBlogger->getEntry(1));

// categories
dump($myBlogger->getCategories());

// tags
dump($myBlogger->getTags());

// time
dump($myBlogger->getMonths());
