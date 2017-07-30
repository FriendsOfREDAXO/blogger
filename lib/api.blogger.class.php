<?php 

class BloggerApi {
  public static function delete($pid) {
    $query = 'DELETE FROM rex_blogger_entries WHERE id="'.$pid.'"; ';
    $query .= 'DELETE FROM rex_blogger_content WHERE pid="'.$pid.'"';
    $sql = rex_sql::factory();
    $sql->setQuery($query);
  }

  public static function updateEntry($pid, $clang, $data) {
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
    $sql->setWhere('pid='.$pid.' AND clang='.$clang);
    $sql->update();
  }

  public static function updateMeta($pid, $data) {
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
    $sql->setWhere('id='.$pid);
    $sql->update();
  }

  public static function getMeta($pid) {
    $meta = [];

    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_entries');
    $sql->setWhere('id='.$pid.' LIMIT 1');
    $sql->select();

    $meta['category'] = $sql->getValue('category');
    $meta['tags'] = explode('|', $sql->getValue('tags'));
    $meta['postedBy'] = $sql->getValue('postedBy');
    $meta['postedAt'] = $sql->getValue('postedAt');

    return $meta;
  }

  public static function getEntry($pid, $clang) {
    $entry = [];

    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_content');
    $sql->setWhere('pid='.$pid.' AND clang='.$clang.' LIMIT 1');
    $sql->select();

    $entry['title'] = $sql->getValue('title');
    $entry['text'] = $sql->getValue('text');
    $entry['preview'] = $sql->getValue('preview');
    $entery['gallery'] = $sql->getValue('gallery');

    return $entry;
  }

  public static function create($data) {
    $metaSet['category'] = $data['meta']['category'];
    if ($data['meta']['tags'])
      $metaSet['tags'] = implode('|', $data['meta']['tags']);

    if ($data['meta']['postedby'])
      $metaSet['postedBy'] = $data['meta']['postedby'];

    if ($data['meta']['postedat'])
      $metaSet['postedAt'] = $data['meta']['postedat'];

    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_entries');
    $sql->setValues($metaSet);
    $sql->insert();

    $pid = $sql->getLastId();

    foreach ($data['content'] as $clang => $content) {
      self::createContent($pid, $clang, $content);
    }

    return $pid;
  }

  public static function createContent($pid, $clang, $content) {
    $contentSet = [];

    $contentSet['pid'] = $pid;
    $contentSet['clang'] = $clang;

    if ($content['title'])
      $contentSet['title'] = $content['title'];

    if ($content['text'])
      $contentSet['text'] = $content['text'];

    if ($content['preview'])
      $contentSet['preview'] = $content['preview'];

    if ($content['gallery'])
      $contentSet['gallery'] = $content['gallery'];

    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_content');
    $sql->setValues($contentSet);
    $sql->insert();
  }

}

?>