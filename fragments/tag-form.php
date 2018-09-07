<?php

$fragment = $this;
$id = $fragment->getVar('id');
$func = $fragment->getVar('func');
$clangs = rex_clang::getAll();


// make sure all clang columns exist
$table = rex_sql_table::get(rex::getTable('blogger_tags'));

foreach ($clangs as $clang) {
  $name = 'tag_' . $clang->getId();
  $table->ensureColumn(new rex_sql_column($name, 'varchar(255)', false, ''));
}

$table->ensure();


$table = rex::getTable('blogger_tags');
$where = sprintf('`id` = %d', $id);


$form = rex_form::factory($table, '', $where);

// inputs for all clangs
foreach ($clangs as $clang) {
  $name = 'tag_' . $clang->getId();
  $langName = $clang->getName();
  $field = $form->addTextField($name);
  $field->setLabel(rex_i18n::msg('blogger_forms_tag') . ' (' . $langName . ')');
  $field->setAttribute('placeholder', $langName);
}

if ($func == 'edit') {
  $form->addParam('id', $id);
}

$form->show();
