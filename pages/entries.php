<?php

$pid = rex_request('pid', 'int');
$func = rex_request('func', 'string');

$fragment = new rex_fragment();
$fragment->setVar('pid', $pid);
$fragment->setVar('func', $func);

switch ($func) {
  case 'add':
  case 'edit':
    echo $fragment->parse('entries-form.php');
    break;
  default:
    echo $fragment->parse('entries-list.php');
    break;
}
