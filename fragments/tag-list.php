<?php

$table = rex::getTable('blogger_tags');
$query = sprintf("SELECT * FROM `%s` WHERE 1", $table);
$rowsPerPage = PHP_INT_MAX;

$list = rex_list::factory($query, $rowsPerPage);

$list->addTableAttribute('class', 'table-striped');

$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

$list->setColumnLabel('id', rex_i18n::msg('blogger_col_id'));
$list->setColumnLabel('tag', rex_i18n::msg('blogger_col_tag'));

$list->show();
