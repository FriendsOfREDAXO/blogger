<?php 

/**
 * Class will be used in Backend to create the user output
 * for the configuration, editing and creating
 */
class BeBlogger {
  private $id;    // int, unique id of article
  private $pid;   // int, parent id
  private $func;  // string, requested function

  /**
   *
   *
   */
  public function __construct() {
    $this->id = rex_request('id', 'int');
    $this->pid = rex_request('pid', 'int');
    $this->func = rex_request('func', 'string');
  }

  /**
   *
   *
   */
  public function handleRequest() {
    $this->preRequestHandler();

    switch ($this->func) {
      case 'offline':
      case 'online':
        $this->toggleStatus($this->func);
        break;

      // TODO
      // - edit and tedit can be the same
      // - every post has some values that are the equal for any lang
        // - maybe an extra "list" for this meta
        // - and an extra "form" for the diffrent langs

      case 'add':
      case 'edit':
      case 'tadd':
      case 'tedit':
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
    dump($_POST ?: $_GET);
    return '';
  }

  /**
   * 
   *
   */
  private function preRequestHandler() {

  }

  /**
   * Toggle online/offline status of blog entry
   *
   * @param $status String
   */
  private function toggleStatus($status) {
    $status = ($status === 'offline') ? 1 : 0;

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix() . 'blogger_entries');
    $sql->setWhere(sprintf('id=%u OR aid=%u', $type_id, $type_id));
    $sql->setValues(array("offline"=>$status));

    try {
      $sql->update();
    } catch (rex_sql_exception $e) {
      echo $e;
    }
  }

}

?>