<?php

$id = rex_request('id', 'int');
$func = rex_request('func', 'string');


$fragment = new rex_fragment();
$fragment->setVar('id', $id);
$fragment->setVar('func', $func);


switch ($func) {
  case 'add':
  case 'edit':
    echo $fragment->parse('tag-form.php');
    break;
  default:
    echo $fragment->parse('tag-list.php');
    break;
}
