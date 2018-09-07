<?php

$clang = rex_clang::getCurrentId();

$query = sprintf("
  SELECT
    entry.id,
    content.title,
    category.name_%d,
    entry.postedAt
  FROM rex_blogger_entries AS entry
  JOIN rex_blogger_content AS content
    ON entry.id=content.pid
  JOIN rex_blogger_categories AS category
    ON entry.category=category.id
  WHERE content.clang=%d
  ORDER BY entry.postedAt
", $clang, $clang);

// TODO
// what if entry for current lang does not exist

$rowsPerPage = PHP_INT_MAX;
$list = rex_list::factory($query, $rowsPerPage);
$list->addTableAttribute('class', 'table-striped');

$addUrl = $list->getUrl(['func' => 'add']);
$addIcon = '<a href="'.$addUrl.'"><i class="rex-icon rex-icon-add-action"></i></a>';
$editIcon = '<i class="rex-icon fa-file-text-o"></i>';

$list->addColumn($addIcon, $editIcon, 0, [
  '<th class="rex-table-icon">###VALUE###</th>',
  '<td class="rex-table-icon">###VALUE###</td>'
]);

$list->setColumnParams($addIcon, [
  'func' => 'edit',
  'pid' => '###id###'
]);

// add formats
$list->setColumnFormat('postedAt', 'date', 'd.m.Y');

// add labels
$list->setColumnLabel('id', rex_i18n::msg('blogger_col_nr'));
$list->setColumnLabel('title', rex_i18n::msg('blogger_col_titel'));
$list->setColumnLabel('name', rex_i18n::msg('blogger_col_cat'));
$list->setColumnLabel('postedAt', rex_i18n::msg('blogger_col_post_day'));
$list->setColumnLabel('name_' . $clang, rex_i18n::msg('blogger_col_cat'));

// add sorting
$list->setColumnSortable('id', 'desc');
$list->setColumnSortable('name', 'desc');
$list->setColumnSortable('title', 'desc');
$list->setColumnSortable('postedAt', 'desc');
$list->setColumnSortable('name_' . $clang, 'desc');

$list->show();
