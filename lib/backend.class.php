<?php 

/**
 * Class will be used in Backend to create the user output
 * for the configuration, editing and creating
 */
class BeBlogger {
  private $id;
  private $pid;
  private $func;

  public function __construct() {
    $this->id = rex_request('id', 'int');
    $this->pid = rex_request('pid', 'int');
    $this->func = rex_request('func', 'string');

    $this->preHandle();
  }

  public function getPage() {
    $isForm = ($this->func === 'add' || $this->func === 'edit');

    if ($isForm) {
      return $this->getForm();
    } else {
      return $this->getList();
    }
  }

  private function preHandle() {
    $isStatusUpdate = ($this->func === 'online' || $this->func === 'offline');
    $isEntryUpdate = ($_SERVER['REQUEST_METHOD'] === 'POST');
  }

  private function getList() {
    $clang = rex_clang::getCurrentId();

    $query = ("
      SELECT
        entry.id,
        content.title,
        category.name,
        entry.postedAt
      FROM rex_blogger_entries AS entry
      LEFT JOIN rex_blogger_content AS content
        ON entry.id=content.pid
      LEFT JOIN rex_blogger_categories AS category
        ON entry.category=category.id
      WHERE content.clang=".$clang."
    ");

    // TODO
    // show all blog entries for the current lang
    // if there is a blog entry only for another lang show it as well,
    // but mark it

    return BeForms::genList($query);
  }

  private function getForm() {
    $query = ("

    ");

    return BeForms::genForm();
  }
}

class BeForms {
  static public function genForm() {
    $content = '';

    $content .= Self::genMetaSection();
    $content .= Self::genLangListSection();
    $content .= Self::genContentSection();

    $content = '<form>'.$content.'</form>';
    return $content;
  }

  static protected function genMetaSection() {
    $catSelect = new rex_select();
    $tagSql = rex_sql::factory();
    $tagSql->setQuery("SELECT * FROM rex_blogger_categories");
    while($tagSql->hasNext()) {
      $name = $tagSql->getValue('name');
      $id = $tagSql->getValue('id');
      $catSelect->addOption($name, $id);
      $tagSql->next();
    }

    $category = new rex_form_select_element('select');
    $category->setLabel('Category');
    $category->setAttribute('class', 'form-control');
    $category->setSelect($catSelect);

    $tagSelect = new rex_select();
    $tagSql = rex_sql::factory();
    $tagSql->setQuery("SELECT * FROM rex_blogger_tags");
    while($tagSql->hasNext()) {
      $name = $tagSql->getValue('tag');
      $id = $tagSql->getValue('id');
      $tagSelect->addOption($name, $id);
      $tagSql->next();
    }

    $tags = new rex_form_select_element('select');
    $tags->setLabel('Tags');
    $tags->setAttribute('class', 'form-control');
    $tags->setSelect($tagSelect);

    $postedBy = new rex_form_element('input');
    $postedBy->setLabel('Author');
    $postedBy->setAttribute('class', 'form-control');

    $postedAt = new rex_form_element('input');
    $postedAt->setLabel('Publish Day');
    $postedAt->setAttribute('class', 'form-control');


    $content = '';
    $content .= $category->get();
    $content .= $tags->get();
    $content .= $postedBy->get();
    $content .= $postedAt->get();

    return '<section>'.$content.'</section><hr />';
  }

  static protected function genLangListSection() {
    $list = rex_clang::getAll(true);
    $clang = rex_clang::getCurrentId();

    if (sizeof($list) == 1)
      return '';

    foreach($list as $item) {
      $content .= ($item->getId() == $clang)
        ? '<button class="btn btn-primary">'
        : '<button class="btn">';

      $content .= $item->getName().'</button>';
    }

    return '<section>'.$content.'</section><hr />';
  }

  static protected function genContentSection() {
    $title = new rex_form_element('input');
    $title->setLabel('Title');
    $title->setAttribute('class', 'form-control');

    $text = new rex_form_element('textarea', null, [], true);
    $text->setLabel('Text');
    $text->setAttribute('class', 'form-control');

    $preview = new rex_form_widget_media_element('input');
    $preview->setLabel('Preview');

    $gallery = new rex_form_widget_medialist_element('select');
    $gallery->setLabel('Gallery');

    $content = '';
    $content .= $title->get();
    $content .= $text->get();
    $content .= $preview->get();
    $content .= $gallery->get();

    return '<section>'.$content.'</section><hr />';
  }

  static public function genList($query) {
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped');
    $list->setNoRowsMessage('Es wurden keine Einträge gefunden');

    $thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'pid' => '###id###']);

    $list->setColumnLabel('id', 'Nr.');
    $list->setColumnLabel('title', 'Titel');
    $list->setColumnLabel('name', 'Kategorie');
    $list->setColumnLabel('postedAt', 'Post Tag');

    $content = $list->get();

    $fragment = new rex_fragment();
    $fragment->setVar('content', $content, false);
    $content = $fragment->parse('core/page/section.php');

    return $content;
  }
}

?>