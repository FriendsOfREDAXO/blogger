<?php 

/**
 * Class will be used in Backend to create the user output
 * for the configuration, editing and creating
 */
class BeBlogger extends BloggerForms {
  private $id;    // int, unique id of article
  private $pid;   // int, parent id
  private $func;  // String, requested function

  private $list;  // rex_list, will be rendered

  /**
   *
   *
   */
  public function __construct() {
    // create default variables
    $this->id = rex_request('id', 'int');
    $this->pid = rex_request('pid', 'int');
    $this->func = rex_request('func', 'string');
  }

  /**
   *
   *
   */
  public function handleRequest() {
    switch($this->func) {
      case 'online':
      case 'offline':
        $this->toggleStatus($this->func);
        break;

      default:
        break; 
    }
  }

  /**
   * returns the requested page as a string
   *
   * @return String
   */
  public function getPage() {

    dump($this);
    dump($_SERVER['REQUEST_METHOD']);
    dump($_POST ?: $_GET);
    
    $render = '';

    switch($this->func) {
      case 'edit':
        $render = $this->renderEditPage();
        break;

      case 'add':
        $render = $this->renderEditPage(true);
        break;

      default:
        $render = $this->renderDefaultPage();
    }

    return $render;
  }

  /**
   *
   *
   */
  private function renderDefaultPage() {
    // default query to show entries for current lang /w category
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

    // create list with query
    $this->list = rex_list::factory($query);
    $this->addFuncToList();

    return $this->createListFragment();
  }

  /**
   * adds the default edit and add function 
   * to the list items and head
   */
  private function addFuncToList() {
    $url = $this->list->getUrl(['func' => 'add']);

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

    $this->list->addColumn($head, $data, 0, $columnLayout);
    $this->list->setColumnParams($head, $columnParams);
  }

  /**
   * create fragment from list and return it
   * set default list variables etc.
   */
  private function createListFragment() {
    // default list variables
    $this->list->addTableAttribute('class', 'table-striped');
    $this->list->setNoRowsMessage('Es wurden keine Einträge gefunden');

    $content = $this->list->get();

    $fragment = new rex_fragment();
    $fragment->setVar('content', $content, false);

    return $fragment->parse('core/page/section.php');
  }

  /**
   *
   *
   */
  private function renderEditPage($addPage=false) {
    // show edit page for selected entry
    $langData = $this->sectionEditLang();

    $form = rex_form::factory(
      'rex_blogger_content',
      '',
      sprintf('id=%u AND pid=%u', $this->id, $this->pid)
    );

    // $this->addContentFields($form);

    $metaData = $this->sectionEditMeta();
    $form->addFieldset($metaData);

    if (!$addPage) {
      $form->addParam('id', $this->id);
      $form->addParam('pid', $this->pid);
    }

    $content = $form->get();

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', "Content", false);
    $fragment->setVar('body', $content, false);

    $content = $fragment->parse('core/page/section.php');

    return $content;
  }

  /**
   * show a list of all languages and the current selected one
   * each will have it's own blog content
   */
  private function sectionEditLang() {
    // TODO
    // - select all entries with the same pid
    // - get clang for currently selected entry
    // - mark current clang, show others with pid and id params
    // - don't show if there is only one language
    return '';
  }

  /**
   * meta section which will be available for
   * every entry and every lang
   */
  private function sectionEditMeta() {
    $form = rex_form::factory(
      'rex_blogger_entries',
      'Meta',
      'id='.$this->pid
    );

    $this->addMetaFields($form);
    $form->addParam('pid', $this->pid);

    $content = $form->get();

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', "Metadata", false);
    $fragment->setVar('body', $content, false);

    $fragment->parse('core/page/section.php');

    return $form;
  }

  /**
   * Toggle online/offline status of blog entry
   *
   * @param $status String
   */
  private function toggleStatus($status) {
    $status = ($status === 'offline') ? 1 : 0;
  }
}

?>