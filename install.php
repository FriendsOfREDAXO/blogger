<?php

// clangs
$clangs = rex_clang::getAll();

// all tables
$bloggerEntriesTable = rex::getTable('blogger_entries');
$bloggerContentTable = rex::getTable('blogger_content');
$bloggerCategoriesTable = rex::getTable('blogger_categories');
$bloggerTagsTable = rex::getTable('blogger_tags');


// ensure tables
$table = rex_sql_table::get($bloggerEntriesTable);
$table->ensurePrimaryIdColumn();
$table->ensureColumn(new rex_sql_column('category', 'int(11)', false, 1));
$table->ensureColumn(new rex_sql_column('tags', 'text', false, ''));
$table->ensureColumn(new rex_sql_column('status', 'int(11)', false, 0));
$table->ensureColumn(new rex_sql_column('postedBy', 'varchar(255)', false, ''));
$table->ensureColumn(new rex_sql_column('postedAt', 'datetime'));
$table->ensure();


$table = rex_sql_table::get($bloggerContentTable);
$table->ensurePrimaryIdColumn();
$table->ensureColumn(new rex_sql_column('pid', 'int(10) unsigned'));
$table->ensureColumn(new rex_sql_column('clang', 'int(11)', false, 1));
$table->ensureColumn(new rex_sql_column('title', 'varchar(255)', false, ''));
$table->ensureColumn(new rex_sql_column('text', 'text', false, ''));
$table->ensureColumn(new rex_sql_column('preview', 'varchar(1024)', false, ''));
$table->ensureColumn(new rex_sql_column('gallery', 'text', false, ''));
$table->ensure();


$table = rex_sql_table::get($bloggerCategoriesTable);
$table->ensurePrimaryIdColumn();

foreach ($clangs as $clang) {
  $name = 'name_' . $clang->getId();
  $table->ensureColumn(new rex_sql_column($name, 'varchar(256)', false, ''));
}

$table->ensure();


$table = rex_sql_table::get($bloggerTagsTable);
$table->ensurePrimaryIdColumn();

foreach ($clangs as $clang) {
  $name = 'tag_' . $clang->getId();
  $table->ensureColumn(new rex_sql_column($name, 'varchar(256)', false, ''));
}

$table->ensure();


// default categories
$sql = rex_sql::factory();
$sql->setTable($bloggerCategoriesTable);
$sql->select();

if ($sql->getRows() <= 0) {
  $sql = rex_sql::factory();
  $sql->setTable($bloggerCategoriesTable);
  $sql->setValue('id', 1);

  foreach ($clangs as $clang) {
    $name = 'name_' . $clang->getId();
    $sql->setValue($name, 'Default');
  }

  $sql->insert();
}


// default tag
$sql = rex_sql::factory();
$sql->setTable($bloggerTagsTable);
$sql->select();

if ($sql->getRows() <= 0) {
  $sql = rex_sql::factory();
  $sql->setTable($bloggerTagsTable);
  $sql->setValue('id', 1);

  foreach ($clangs as $clang) {
    $name = 'tag_' . $clang->getId();
    $sql->setValue($name, 'Default');
  }

  $sql->insert();
}
