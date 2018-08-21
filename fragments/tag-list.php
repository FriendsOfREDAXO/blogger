<?php

$table = rex::getTable('blogger_tags');
$query = sprintf("SELECT * FROM `%s`", $table);
$rowsPerPage = PHP_INT_MAX;


$list = rex_list::factory($query, $rowsPerPage);

$list->addTableAttribute('class', 'table-striped');

$addUrl = $list->getUrl(['func' => 'add']);
$addIcon = '<a href="'.$addUrl.'"><i class="rex-icon rex-icon-add-action"></i></a>';
$editIcon = '<i class="rex-icon fa-file-text-o"></i>';

$list->addColumn(
  $addIcon,
  $editIcon,
  0,
  ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']
);

$list->setColumnParams($addIcon, ['func' => 'edit', 'id' => '###id###']);

$list->setColumnLabel('id', rex_i18n::msg('blogger_col_id'));
$list->setColumnLabel('tag', rex_i18n::msg('blogger_col_tag'));

$list->show();
