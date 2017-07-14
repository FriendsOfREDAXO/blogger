<?php 

/**
 * Class will be used in Backend to create the user output
 * for the configuration, editing and creating
 */
class BeBlogger extends BloggerRenderer {
  public function __construct() {
    // create default variables
    $this->id = rex_request('id', 'int');
    $this->pid = rex_request('pid', 'int');
    $this->func = rex_request('func', 'string');
  }


  public function handleRequest() {
    switch($this->func) {
      case 'online':
      case 'offline':
        $this->toggleStatus($this->func);
        break;

      case 'edit':
      case 'add':
        $this->render = $this->renderEditPage();
        break;

      default:
        $this->render = $this->renderDefaultPage();
        break; 
    }
  }


  /**
   * returns the requested page as a string
   *
   * @return String
   */
  public function getPage() {
    $render = $this->render ?: '';
    $this->render = null;

    dump($this);
    dump($_SERVER['REQUEST_METHOD']);
    dump($_POST ?: $_GET);

    return $render;
  }
}

?>