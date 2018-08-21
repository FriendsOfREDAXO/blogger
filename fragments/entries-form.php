<?php

$fragment = $this;
$pid = $fragment->getVar('pid');
$func = $fragment->getVar('func');

$data = rex_post('blogger', 'array');
$method = rex_request::server('REQUEST_METHOD', 'string');

$isPost = $method === 'POST';
$hasData = empty($data) === false;

// funcs
$isAdd = $func === 'add';
$isEdit = $func === 'edit';
$isOnline = $func === 'online';
$isOffline = $func === 'offline';

// saving, updating etc.
$isEntryCreate = $isPost && $hasData && $isAdd;
$isEntryUpdate = $isPost && $hasData && $isEdit;
$isStatusUpdate = $isOnline || $isOffline;

if ($isEntryCreate) {
  $hash = md5(0);
  $data = $_POST['blogger'][$hash];
  $pid = BloggerApi::create($data);

  // redirect to list
  $listPageUrl = rex_url::backendPage('blogger/entries');
  header('Location: ' . $listPageUrl);
  die();
} else if ($isEntryUpdate) {
  // save, update, create table
  $hash = md5($pid);
  $data = $_POST['blogger'][$hash];
  $formPid = $data['pid'];

  if ($data['action'] === 'abort') {
    $func = '';
  } else if ($data['action'] === 'delete') {
    BloggerApi::delete($formPid);

    // redirect to list
    $listPageUrl = rex_url::backendPage('blogger/entries');
    header('Location: ' . $listPageUrl);
    die();
  } else {
    $metaData = $data['meta'];
    $content = $data['content'];

    BloggerApi::updateMeta($formPid, $metaData);

    foreach($content as $clang => $content) {
      BloggerApi::updateEntry($formPid, $clang, $content);
    }

    if ($data['action'] === 'save') {
      // redirect to list
      $listPageUrl = rex_url::backendPage('blogger/entries');
      header('Location: ' . $listPageUrl);
      die();
    }
  }
} else if ($isStatusUpdate) {
  // TODO
}

// create and output form
$form = new BloggerBackendForm($func, $pid);
$form->show();
