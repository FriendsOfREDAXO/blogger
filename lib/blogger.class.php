<?php

class Blogger {
  /**
   * Creates and returns a new Blogger object
   *
   * @param int $articlesPerPage
   */
  public function __construct() {
    
  }

  /**
   * Returns an array of all entries
   *
   * @return array
   */
  public function getEntries($includeOffline=false) {
    $result = [];

    $where = $includeOffline
      ? '1'
      : 'status != 1';

    $sql = rex_sql::factory();
    $sql->setQuery('
      SELECT * FROM
        rex_blogger_entries AS e
      LEFT JOIN
        rex_blogger_categories AS c
        ON e.category=c.id
      WHERE '.$where.'
    ');
    $sql->execute();

    while ($sql->hasNext()) {
      $result[] = $this->fromSql($sql);
      $sql->next();
    }

    return $result;
  }

  /**
   * Returns one entry
   *
   * @return array
   */
  public function getEntry($id) {
    $sql = rex_sql::factory();
    $sql->setQuery('
      SELECT * FROM
        rex_blogger_entries AS e
      LEFT JOIN
        rex_blogger_categories AS c
        ON e.category=c.id
      WHERE e.id='.$id.'
    ');
    $sql->execute();

    return $this->fromSql($sql);
  }

  /**
   * Returns array for given category or tags
   *
   * @return array 
   */
  public function getEntriesBy($category=null, array $tags=[], $year=null, $month=null, $author=null) {
    $result = [];

    $where = [];

    // check category
    if ($category !== null) {
      $cat .= 'e.category='.$category;
      $cat = '('.$cat.')';
      $where[] = $cat;
    }

    // check tags
    if (empty($tags) === false) {
      $tag = 'e.tags="'.implode('|', $tags).'"';
      $tag = '('.$tag.')';
      $where[] = $tag;
    }

    // check year
    if ($year !== null) {
      $where[] = '(MONTH(e.postedAt)='.$year.')';
    }

    // check month
    if ($month !== null) {
      $where[] = '(YEAR(e.postedAt)='.$month.')';
    }

    // check author
    if ($author !== null) {
      $where[] = '(e.postedBy="'.$author.'")';
    }

    if (empty($where)) {
      $where = "1";
    } else {
      $where = implode(' AND ', $where);
    }

    $query = ('
      SELECT 
        *
       FROM
        rex_blogger_entries AS e
      LEFT JOIN
        rex_blogger_categories AS c
        ON e.category=c.id
      WHERE '.$where.'
    ');

    $sql = rex_sql::factory();
    $sql->setQuery($query);
    $sql->execute();

    while ($sql->hasNext()) {
      $result[] = $this->fromSql($sql);      
      $sql->next();
    }

    return $result;
  }

  /**
   * Each clang as own array with content
   *
   * @return array
   */
  private function getContent($id) {
    $sql = rex_sql::factory();
    $sql->setQuery('
      SELECT * FROM
        rex_blogger_content
      WHERE pid='.$id.'
    ');
    $sql->execute();

    $result = [];

    while ($sql->hasNext()) {
      $temp = [];
      $clang = $sql->getValue('clang');

      $temp['clang'] = (int) $clang;
      $temp['title'] = $sql->getValue('title');
      $temp['text'] = $sql->getValue('text');
      $temp['preview'] = $sql->getValue('preview');
      $temp['gallery'] = $sql->getValue('gallery');

      $result[$clang] = $temp;

      $sql->next();
    }

    return $result;
  }

  /**
   * Gets all important values from an sql object
   *
   * @return array
   */
  private function fromSql($sql) {
    $temp = [];

    $temp['id'] = (int) $sql->getValue('e.id');
    $temp['categoryId'] = (int) $sql->getValue('c.id');
    $temp['categoryName'] = $sql->getValue('c.name');
    $temp['tags'] = $this->getTagNames(explode('|', $sql->getValue('e.tags')));
    $temp['status'] = (int) $sql->getValue('e.status');
    $temp['postedBy'] = $sql->getValue('e.postedBy');
    $temp['postedAt'] = $sql->getValue('e.postedAt');
    $temp['content'] = $this->getContent($temp['id']);

    return $temp;
  }

  /**
   * Return array of tags
   *
   * @return array
   */
  private function getTagNames(array $tagIds) {
    $tagIds = array_filter($tagIds, function($item) {
      if ($item === '') {
        return false;
      }

      return true;
    });


    if (isset($tagIds[0]) === false) {
      return;
    }

    $result = [];
    $where = 'id='.implode(' OR id=', $tagIds);

    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_tags');
    $sql->setWhere($where);
    $sql->select();
    $sql->execute();

    while ($sql->hasNext()) {
      $temp = [];

      $temp['id'] = $sql->getValue('id');
      $temp['tag'] = $sql->getValue('tag');

      $result[] = $temp;

      $sql->next();
    }

    return $result;
  }


  /**
   * Returns array of all categories
   *
   * @return array
   */
  public function getCategories() {
    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_categories');
    $sql->select();
    $sql->execute();

    $result = [];

    while ($sql->hasNext()) {
      $temp = [];

      $temp['id'] = (int) $sql->getValue('id');
      $temp['name'] = $sql->getValue('name');

      $result[] = $temp;

      $sql->next();
    }

    return $result;
  }

  /**
   * Returns array of all tags
   *
   * @return array
   */
  public function getTags() {
    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_tags');
    $sql->select();
    $sql->execute();

    $result = [];

    while ($sql->hasNext()) {
      $temp = [];

      $temp['id'] = (int) $sql->getValue('id');
      $temp['tag'] = $sql->getValue('tag');

      $result[] = $temp;

      $sql->next();
    }

    return $result;
  }


  /**
   * Returns all months in their year
   *
   * @return array
   */
  public function getMonths($includeOffline=false) {

    $where = $includeOffline
      ? '1'
      : 'status != 1';

    $sql = rex_sql::factory();
    $sql->setQuery('
      SELECT DISTINCT
        MONTH(postedAt) AS month,
        YEAR(postedAt) AS year
      FROM rex_blogger_entries
      WHERE '.$where.'
    ');
    $sql->execute();

    $result = [];

    while ($sql->hasNext()) {
      $temp = [];

      $temp['year'] = $sql->getValue('year');
      $temp['month'] = $sql->getValue('month');

      $result[] = $temp;
      $sql->next();
    }

    return $result;
  }
}

?>