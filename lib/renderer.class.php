<?php 

class BloggerRenderer extends BloggerForms {
  /**
   * renders list with entries
   * 
   */
  public function renderDefaultPage() {
    $query = ('
      SELECT
        con.id,
        con.pid,
        con.title,
        cat.name AS category,
        ent.postedAt
      FROM
        rex_blogger_content AS con
      LEFT JOIN
        rex_blogger_entries AS ent
        ON
          con.pid=ent.id
      LEFT JOIN
        rex_blogger_categories AS cat
        ON
          ent.category=cat.id
      WHERE con.clang=1
    ');

    // TODO
    // - con.clang=1 needs to be any lang, but every entry should be displayed

    $list = rex_list::factory($query);

    $url = $list->getUrl(['func' => 'add']);
    $head = '<a href="'.$url.'"><i class="rex-icon rex-icon-add-action"></i></a>';
    $data = '<i class="rex-icon fa-file-text-o"></i>';

    $columnLayout = [
      '<th class="rex-table-icon">###VALUE###</th>',
      '<td class="rex-table-icon">###VALUE###</td>'
    ];

    $columnParams = [
      'func' => 'edit',
      'id' => '###id###',
      'pid' => '###pid###'
    ];

    $list->addColumn($head, $data, 0, $columnLayout);
    $list->setColumnParams($head, $columnParams);

    // default list variables
    $list->addTableAttribute('class', 'table-striped');
    $list->setNoRowsMessage('Es wurden keine Einträge gefunden');

    $content = $list->get();
    $fragment = new rex_fragment();
    $fragment->setVar('content', $content, false);

    return $fragment->parse('core/page/section.php');
  }


  /**
   * renders add & edit forms
   *
   */
  public function renderEditPageDep() {
    // $query = ('
    //   rex_blogger_content AS cont

    //   LEFT JOIN rex_blogger_entries AS entry
    //     ON cont.pid=entry.id
    // ');
    $query = ('
      rex_blogger_content AS cont,
      rex_blogger_entries AS entry
    ');

    $where = ('
          cont.id='.$this->id.'
      AND cont.pid='.$this->pid.'
      AND entry.id='.$this->pid.'
    ');

    $form = rex_form::factory($query, '', $where, 'post', true);

    dump($form->getSql());

    $this->addMetaFields($form);
    $this->addContentFields($form);

    if ($this->func === "edit") {
      $form->addParam('id', $this->id);
      $form->addParam('pid', $this->pid);
    }

    $content = $form->get();

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $formLabel, false);
    $fragment->setVar('body', $content, false);

    $content = $fragment->parse('core/page/section.php');
    return $content;
  }

  public function renderEditPage() {

    $form1 = rex_form::factory('rex_blogger_entries', '', 'id='.$this->pid, 'post', true);
    $form2 = rex_form::factory('rex_blogger_content', '', 'id='.$this->id, 'post', true);

    if ($this->func === "edit") {
      $form1->addParam('id', $this->id);
      $form1->addParam('pid', $this->pid);
      $form2->addParam('id', $this->id);
      $form2->addParam('pid', $this->pid);
    }

    $this->addMetaFields($form1);
    $this->addContentFields($form2);

    $content = $form1->get();
    $content .= $form2->get();

    // TODO
    // - get names of both forms
    // - create own form with names
    // - make own buttons with save, apply and delete
    // - create post save, apply, delete var server sided to check what button was clicked

    return $content;
  }
}

?>