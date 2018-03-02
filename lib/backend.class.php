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
    $isEntryCreate = (
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && $_POST['blogger']
      && $this->func === 'add'
    );

    $isEntryUpdate = (
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && $_POST['blogger']
    );

    $isStatusUpdate = (
      $this->func === 'online'
      || $this->func === 'offline'
    );

    if ($isEntryCreate) {
      $hash = md5(0);
      $data = $_POST['blogger'][$hash];
      $this->pid = BloggerApi::create($data);
      $this->func = '';
      return;
    }

    if ($isEntryUpdate) {
      // save, update, create table
      $hash = md5($this->pid);  // what if pid is not in GET?
      $data = $_POST['blogger'][$hash];
      $formPid = $data['pid'];

      if ($data['action'] === 'abort') {
        $this->func = '';
        return;
      }

      if ($data['action'] === 'delete') {
        BloggerApi::delete($formPid);
        $this->func = '';
        return;
      }

      if ($data['action'] === 'save') {
        $this->func = '';
      }

      $metaData = $data['meta'];
      $content = $data['content'];

      BloggerApi::updateMeta($formPid, $metaData);
      foreach($content as $clang => $content) {
        BloggerApi::updateEntry($formPid, $clang, $content);
      }
    }

    if ($isStatusUpdate) {
      // TODO
      // update database
    }
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
      JOIN rex_blogger_content AS content
        ON entry.id=content.pid
      JOIN rex_blogger_categories AS category
        ON entry.category=category.id
      WHERE content.clang=".$clang."
    ");

    // TODO
    // what if entry for current lang does not exist

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
    $addon = rex_addon::get('blogger');

    if ($this->pid === 0) {
      return ('
        <hr />
        <div class="btn-toolbar">
          <button name="'.$this->name.'[action]" value="save" class="btn btn-save">'.$addon->i18n('btn_create').'</button>
          <button name="'.$this->name.'[action]" value="abort" class="btn btn-abort">'.$addon->i18n('btn_abort').'</button>
        </div>
        <hr />
      ');
    }

    return ('
      <hr />
      <div class="btn-toolbar">
        <button name="'.$this->name.'[action]" value="save" class="btn btn-save">'.$addon->i18n('btn_save').'</button>
        <button name="'.$this->name.'[action]" value="apply" class="btn btn-apply">'.$addon->i18n('btn_apply').'</button>
        <button name="'.$this->name.'[action]" value="delete" class="btn btn-delete">'.$addon->i18n('btn_delete').'</button>
        <button name="'.$this->name.'[action]" value="abort" class="btn btn-abort">'.$addon->i18n('btn_abort').'</button>
      </div>
      <hr />
    ');
  }

  private function genMetaSection() {
    $addon = rex_addon::get('blogger');

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
    $category->setLabel($addon->i18n('forms_category'));
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
    $tags->setLabel($addon->i18n('forms_tags'));
    $tags->setAttribute('name', $this->name.'[meta][tags]');
    $tags->setAttribute('class', 'form-control');
    $tags->setSelect($tagSelect);

    $postedBy = new rex_form_element('input');
    $postedBy->setLabel($addon->i18n('forms_author'));
    $postedBy->setAttribute('name', $this->name.'[meta][postedBy]');
    $postedBy->setAttribute('class', 'form-control');

    $postedAt = new rex_form_element('input');
    $postedAt->setLabel($addon->i18n('forms_post_day'));
    $postedAt->setAttribute('name', $this->name.'[meta][postedAt]');
    $postedAt->setAttribute('class', 'form-control');
    $postedAt->setAttribute('data-blogger-time-form', true);
    $postedAt->setAttribute('type', 'datetime-local');

    if ($this->pid) {
      $meta = BloggerApi::getMeta($this->pid);

      $category->setValue($meta['category']);
      foreach($meta['tags'] as $selected) {
        $tagSelect->setSelected($selected);
      }
      $postedBy->setValue($meta['postedBy']);
      $postedAt->setValue(str_replace(' ', 'T', $meta['postedAt']));
    } else {
      $date = new DateTime();
      $postedAt->setValue(str_replace(' ', 'T', $date->format('Y-m-d H:i:s')));
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
    $addon = rex_addon::get('blogger');

    $title = new rex_form_element('input');
    $title->setLabel($addon->i18n('forms_title'));
    $title->setAttribute('name', $this->name.'[content]['.$clang.'][title]');
    $title->setAttribute('class', 'form-control');

    $text = new rex_form_element('textarea', null, [], true);
    $text->setLabel($addon->i18n('forms_text'));
    $text->setAttribute('name', $this->name.'[content]['.$clang.'][text]');

    $textClass = 'form-control';
    $configClass = rex_config::get('blogger', 'texteditor');

    if ($configClass) {
      $textClass .= ' '.$configClass;
    }

    $text->setAttribute('class', $textClass);


    $preview = new rex_form_widget_media_element('input');
    $preview->setAttribute('name', $this->name.'[content]['.$clang.'][preview]');
    $preview->setLabel($addon->i18n('forms_preview'));

    $gallery = new rex_form_widget_medialist_element('select');
    $gallery->setAttribute('name', $this->name.'[content]['.$clang.'][gallery]');
    $gallery->setLabel($addon->i18n('forms_gallery'));

    if ($this->pid) {
      $entry = BloggerApi::getEntry($this->pid, $clang);

      $title->setValue($entry['title']);
      $preview->setValue($entry['preview']);
      $text->setValue($entry['text']);
      $gallery->setValue($entry['gallery']);
    }

    $content = '';
    $content .= $title->get();
    $content .= $preview->get();
    $content .= $text->get();

    if (rex_config::get('blogger', 'gallery') === 'on') {
      $content .= $gallery->get();
    }

    $className = $clang == rex_clang::getCurrentId()
      ? ' class="active" data-clang="'.$clang.'"'
      : ' class="hidden" data-clang="'.$clang.'"';

    $langCount = count(rex_clang::getAllIds(true));
    $headline = '';

    if ($langCount > 1) {
      $tempClang = rex_clang::get($clang);
      $tempClang = $tempClang->getName();
      $headline = '<h2>'.$tempClang.'</h2>';
    }

    return '<section'.$className.'>'.$headline.$content.'</section>';
  }

  static public function genList($query) {
    $blogger = rex_addon::get('blogger');
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped');
    $list->setNoRowsMessage('Es wurden keine Einträge gefunden');

    $thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

    $list->addColumn($thIcon, $tdIcon, 0, [
      '<th class="rex-table-icon">###VALUE###</th>',
      '<td class="rex-table-icon">###VALUE###</td>'
    ]);

    $list->setColumnParams($thIcon, [
      'func' => 'edit',
      'pid' => '###id###'
    ]);

    $list->setColumnLabel('id', $blogger->i18n('col_nr'));
    $list->setColumnLabel('title', $blogger->i18n('col_titel'));
    $list->setColumnLabel('name', $blogger->i18n('col_cat'));
    $list->setColumnLabel('postedAt', $blogger->i18n('col_post_day'));

    
    $list->setColumnLabel()

    $content = $list->get();

    $fragment = new rex_fragment();
    $fragment->setVar('content', $content, false);
    $content = $fragment->parse('core/page/section.php');

    return $content;
  }
}

?>