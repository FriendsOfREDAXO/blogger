<?php 

/**
 * Class will be used in Backend to create the user output
 * for the configuration, editing and creating
 */
class BeBlogger {
  private $pid;
  private $func;

  public function __construct() {
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
    $isEntryUpdate = ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['blogger']);
    $isStatusUpdate = ($this->func === 'online' || $this->func === 'offline');

    if ($isEntryUpdate) {
      // save, update, create table
      $hash = md5($this->pid);  // what if pid is not in GET?

      $data = $_POST['blogger'][$hash];
      $formPid = $data['pid'];
      $metaData = $data['meta'];
      $content = $data['content'];

      $this->updateMeta($formPid, $metaData);
      foreach($content as $clang => $content) {
        $this->updateEntry($formPid, $clang, $content);
      }
    }

    if ($isStatusUpdate) {
      // update database
    }
  }

  private function updateMeta($id, $data) {
    $set = [];

    if ($data['category'])
      $set['category'] = $data['category'];

    if ($data['tags'])
      $set['tags'] = implode('|', $data['tags']);

    if ($data['postedby'])
      $set['postedBy'] = $data['postedby'];

    if ($data['postedat'])
      $set['postedAt'] = $data['postedat'];

    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_entries');
    $sql->setValues($set);
    $sql->setWhere('id='.$id);
    $sql->update();
  }

  private function updateEntry($id, $clang, $data) {
    $set = [];

    if ($data['title'])
      $set['title'] = $data['title'];

    if ($data['preview'])
      $set['preview'] = $data['preview'];

    if ($data['gallery'])
      $set['gallery'] = $data['gallery'];

    if ($data['text'])
      $set['text'] = $data['text'];

    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_content');
    $sql->setValues($set);
    $sql->setWhere('pid='.$id.' AND clang='.$clang);
    $sql->update();
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
    $beforms = new BeForms($this->func, $this->pid);
    return $beforms->genForm();
  }
}

class BeForms {
  private $func;
  private $pid;
  private $hash;
  private $name;

  public function __construct($func, $pid) {
    $this->func = $func;
    $this->pid = $pid;
    $this->hash = md5($this->pid);
    $this->name = 'blogger['.$this->hash.']';
  }

  public function genForm() {
    $pid = $this->pid;
    $func = $this->func;

    $content = '';
    $contentAreas = '';

    $content .= $this->genHiddenFields();
    $content .= $this->genMetaSection();
    $content .= $this->genLangListSection();

    foreach (rex_clang::getAllIds() as $clang) {
      $contentAreas .= $this->genContentSection($clang);
    }

    $content .= '<section class="blogger-content-areas">'.$contentAreas.'</section>';
    $content .= $this->genButtons();

    $action = 'index.php?page=blogger/entries&func='.$func.'&pid='.$pid;
    $content = ('
      <form class="blogger-form" method="POST" action="'.$action.'" enctype="multipart/form-data">
        '.$content.'
      </form>
    ');
    return $content;
  }

  private function genHiddenFields() {
    return '<input type="hidden" name="'.$this->name.'[pid]" value="'.$this->pid.'">';
  }

  private function genButtons() {
    return ('
      <hr />
      <div class="btn-toolbar">
        <button name="'.$this->name.'[action]" value="save" class="btn btn-save">Save</button>
        <button name="'.$this->name.'[action]" value="apply" class="btn btn-apply">Apply</button>
        <button name="'.$this->name.'[action]" value="delete" class="btn btn-delete">Delete</button>
        <button name="'.$this->name.'[action]" value="abort" class="btn btn-abort">Abort</button>
      </div>
      <hr />
    ');
  }

  private function genMetaSection() {
    $catSelect = new rex_select();
    $catSql = rex_sql::factory();
    $catSql->setQuery("SELECT * FROM rex_blogger_categories");
    while($catSql->hasNext()) {
      $name = $catSql->getValue('name');
      $id = $catSql->getValue('id');
      $catSelect->addOption($name, $id);
      $catSql->next();
    }

    $category = new rex_form_select_element('select');
    $category->setLabel('Category');
    $category->setAttribute('name', $this->name.'[meta][category]');
    $category->setAttribute('class', 'form-control');
    $category->setSelect($catSelect);

    $tagSelect = new rex_select();
    $tagSelect->setMultiple();
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
    $tags->setAttribute('name', $this->name.'[meta][tags]');
    $tags->setAttribute('class', 'form-control');
    $tags->setSelect($tagSelect);

    $postedBy = new rex_form_element('input');
    $postedBy->setLabel('Author');
    $postedBy->setAttribute('name', $this->name.'[meta][postedBy]');
    $postedBy->setAttribute('class', 'form-control');

    $postedAt = new rex_form_element('input');
    $postedAt->setLabel('Publish Day');
    $postedAt->setAttribute('name', $this->name.'[meta][postedAt]');
    $postedAt->setAttribute('class', 'form-control');

    if ($this->pid) {
      $sql = rex_sql::factory();
      $sql->setTable('rex_blogger_entries');
      $sql->setWhere('id='.$this->pid.' LIMIT 1');
      $sql->select();

      $category->setValue($sql->getValue('category'));
      foreach(explode('|', $sql->getValue('tags')) as $selected) {
        $tagSelect->setSelected($selected);
      }
      $postedBy->setValue($sql->getValue('postedBy'));
      $postedAt->setValue($sql->getValue('postedAt'));
    }

    $content = '';
    $content .= $category->get();
    $content .= $tags->get();
    $content .= $postedBy->get();
    $content .= $postedAt->get();

    return '<section>'.$content.'</section><hr />';
  }

  private function genLangListSection() {
    $list = rex_clang::getAll(true);
    $clang = rex_clang::getCurrentId();

    if (sizeof($list) == 1)
      return '';

    foreach($list as $item) {
      $content .= ($item->getId() == $clang)
        ? '<button class="btn btn-primary"'
        : '<button class="btn"';
      
      $content .= ' data-clang="'.$item->getId().'">';
      $content .= $item->getName().'</button>';
    }

    return '<section class="lang-list">'.$content.'</section><hr />';
  }

  private function genContentSection($clang) {
    $title = new rex_form_element('input');
    $title->setLabel('Title');
    $title->setAttribute('name', $this->name.'[content]['.$clang.'][title]');
    $title->setAttribute('class', 'form-control');

    $text = new rex_form_element('textarea', null, [], true);
    $text->setLabel('Text');
    $text->setAttribute('name', $this->name.'[content]['.$clang.'][text]');
    $text->setAttribute('class', 'form-control');

    $preview = new rex_form_widget_media_element('input');
    $preview->setAttribute('name', $this->name.'[content]['.$clang.'][preview]');
    $preview->setLabel('Preview');

    $gallery = new rex_form_widget_medialist_element('select');
    $gallery->setAttribute('name', $this->name.'[content]['.$clang.'][gallery]');
    $gallery->setLabel('Gallery');

    if ($this->pid) {
      $sql = rex_sql::factory();
      $sql->setTable('rex_blogger_content');
      $sql->setWhere('pid='.$this->pid.' AND clang='.$clang.' LIMIT 1');
      $sql->select();

      $title->setValue($sql->getValue('title'));
      $text->setValue($sql->getValue('text'));
      $preview->setValue($sql->getValue('preview'));
      $gallery->setValue($sql->getValue('gallery'));
    }

    $content = '';
    $content .= $title->get();
    $content .= $text->get();
    $content .= $preview->get();
    $content .= $gallery->get();

    $className = $clang == rex_clang::getCurrentId()
      ? ' class="active" data-clang="'.$clang.'"'
      : ' class="hidden" data-clang="'.$clang.'"';

    return '<section'.$className.'>'.$content.'</section>';
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