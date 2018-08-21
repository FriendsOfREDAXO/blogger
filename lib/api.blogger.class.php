<?php 

/**
 * This class provides some functions to
 * create, read, update and delete blog entries. 
 */
class BloggerApi {

  /**
   * Deletes an entry.
   * $pid is the ID of the entry.
   */
  public static function delete(int $pid) {
    $tableEntries = rex::getTable('blogger_entries');
    $tableContent = rex::getTable('blogger_content');

    $query = sprintf("DELETE FROM `%s` WHERE `id` = %d;", $tableEntries, $pid);
    $query .= sprintf("DELETE FROM `%s` WHERE `pid` = %d;", $tableContent, $pid);

    $sql = rex_sql::factory();
    $sql->setQuery($query);
  }


  /**
   * Updates the contents of an entry with the id of $pid.
   * $data will overwrite every value in the database for that entry.
   */
  public static function updateEntry(int $pid, int $clang, array $data) {
    $set = [];

    $set['title'] = $data['title'];
    $set['preview'] = $data['preview'];
    $set['gallery'] = $data['gallery'];
    $set['text'] = $data['text'];

    $table = rex::getTable('blogger_content');

    $sql = rex_sql::factory();
    $sql->setTable($table);
    $sql->setValues($set);
    $sql->setWhere([ 'pid' => $pid, 'clang' => $clang ]);
    $sql->update();
  }


  /**
   * Updates the metadata for the entry with the ID of $pid.
   * $data will overwrite each field in the database.
   * $data['tags'] is splitted at each "|" character
   */
  public static function updateMeta(int $pid, array $data) {
    $set = [];

    $set['category'] = $data['category'];
    $set['tags'] = $data['tags'] ? implode('|', $data['tags']) : '';
    $set['postedBy'] = $data['postedby'];
    $set['postedAt'] = str_replace('T', ' ', $data['postedat']);

    $table = rex::getTable('blogger_entris');

    $sql = rex_sql::factory();
    $sql->setTable($table);
    $sql->setValues($set);
    $sql->setWhere([ 'id' => $pid ]);
    $sql->update();
  }


  /**
   * Returns an array with the metadata
   * of the entry with the ID $pid.
   */
  public static function getMeta(int $pid) {
    $meta = [];

    $table = rex::getTable('blogger_entris');

    $sql = rex_sql::factory();
    $sql->setTable($table);
    $sql->setWhere(sprintf('id = %d LIMIT 1', $pid));
    $sql->select();

    $meta['category'] = $sql->getValue('category');
    $meta['tags'] = explode('|', $sql->getValue('tags'));
    $meta['postedBy'] = $sql->getValue('postedBy');
    $meta['postedAt'] = $sql->getValue('postedAt');

    return $meta;
  }


  /**
   * Returns the content for an entry with the ID $pid and
   * the Clang $clang.
   */
  public static function getEntry(int $pid, int $clang) {
    $entry = [];

    $table = rex::getTable('blogger_content');

    $sql = rex_sql::factory();
    $sql->setTable($table);
    $sql->setWhere('pid='.$pid.' AND clang='.$clang.' LIMIT 1');
    $sql->select();

    if ($sql->getRows() === 0) {
      // lang doesn't exist, create new row for clang
      $csql = rex_sql::factory();
      $csql->setTable($table);
      $csql->setValues(array(
        'pid' => $pid,
        'clang' => $clang
      ));
      $csql->insert();

      $entry['title'] = "";
      $entry['text'] = "";
      $entry['preview'] = "";
      $entry['gallery'] = "";
    } else {
      $entry['title'] = $sql->getValue('title');
      $entry['text'] = $sql->getValue('text');
      $entry['preview'] = $sql->getValue('preview');
      $entry['gallery'] = $sql->getValue('gallery');
    }

    return $entry;
  }


  /**
   * Creates a new entry and returns the new ID.
   * $data must look like this [ 'meta' => [], 'content' => []]
   */
  public static function create(array $data) {
    $metaSet['category'] = $data['meta']['category'];

    if ($data['meta']['tags']) {
      $metaSet['tags'] = implode('|', $data['meta']['tags']);
    }

    if ($data['meta']['postedby']) {
      $metaSet['postedBy'] = $data['meta']['postedby'];
    }

    if ($data['meta']['postedat']) {
      $metaSet['postedAt'] = $data['meta']['postedat'];
    }

    $table = rex::getTable('blogger_entris');

    $sql = rex_sql::factory();
    $sql->setTable($table);
    $sql->setValues($metaSet);
    $sql->insert();

    $pid = $sql->getLastId();

    foreach ($data['content'] as $clang => $content) {
      self::createContent($pid, $clang, $content);
    }

    return $pid;
  }


  /**
   * Creates the content for an entry with the ID of $pid and the Clang $clang.
   */
  public static function createContent(int $pid, int $clang, array $content) {
    $contentSet = [];

    $contentSet['pid'] = $pid;
    $contentSet['clang'] = $clang;

    if ($content['title']) {
      $contentSet['title'] = $content['title'];
    }

    if ($content['text']) {
      $contentSet['text'] = $content['text'];
    }

    if ($content['preview']) {
      $contentSet['preview'] = $content['preview'];
    }

    if ($content['gallery']) {
      $contentSet['gallery'] = $content['gallery'];
    }

    $table = rex::getTable('blogger_content');

    $sql = rex_sql::factory();
    $sql->setTable($table);
    $sql->setValues($contentSet);
    $sql->insert();
  }
}

?>