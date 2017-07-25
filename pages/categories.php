<?php

  $func = rex_request('func', 'string');

  if ($func == '') {
    $list = rex_list::factory("SELECT `id`, `name` FROM `".rex::getTablePrefix()."blogger_categories` ORDER BY `id`");
    $list->addTableAttribute('class', 'table-striped');
    $list->setNoRowsMessage('Es wurden keine Einträge gefunden');

    $thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

    $list->setColumnLabel('id', 'Id');
    $list->setColumnLabel('name', 'Name');

    $content = $list->get();

    $fragment = new rex_fragment();
    $fragment->setVar('content', $content, false);
    $content = $fragment->parse('core/page/section.php');

    echo $content;
  
  } else if ($func == 'edit' || $func == 'add') {
    $id = rex_request('id', 'int');

    if ($func == 'edit') {
      $formLabel = 'Edit';
    } elseif ($func == 'add') {
      $formLabel = 'Hinzufügen';
    }

    $form = rex_form::factory(rex::getTablePrefix().'blogger_categories', '', 'id='.$id);

    $field = $form->addTextField('name');
    $field->setLabel('Name');

    if ($func == 'edit') {
      $form->addParam('id', $id);
    }

    $content = $form->get();

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $formLabel, false);
    $fragment->setVar('body', $content, false);
    $content = $fragment->parse('core/page/section.php');

    echo $content;

  }


?>