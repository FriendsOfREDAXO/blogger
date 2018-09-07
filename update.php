<?php

// main clang
$mainClang = rex_clang::getStartId();


// rename `rex_blogger_tags`.`tag` to `rex_blogger_tags`.`tag_1`
$table = rex_sql_table::get(rex::getTable('blogger_tags'));

if ($table->exists() && $table->hasColumn('tag')) {
  $table->renameColumn('tag', 'tag_' . $mainClang);
  $table->alter();
}


include './install.php';
