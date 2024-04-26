<?php

/**
 * This class allows to query all entries,
 * categories and tags with different functions.
 */
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
  public function getEntries($limit = '', $includeOffline = false) {
    $result = [];

    $where = $includeOffline
      ? '1'
      : 'status != 1';

    if ($limit !== '') {
      $limit = 'LIMIT '.$limit;
    }

    $sql = rex_sql::factory();
    $sql->setQuery('
      SELECT * FROM
        rex_blogger_entries AS e
      LEFT JOIN
        rex_blogger_categories AS c
        ON e.category=c.id
      WHERE '.$where.'
      '.$limit.'
    ');
    $sql->execute();

    while ($sql->hasNext()) {
      $result[] = $this->fromSql($sql);
      $sql->next();
    }

    return $result;
  }


  /**
   * Returns an array of all entries orderd descending by post day
   *
   * @return array
   */
  public function getLastEntries($limit = '', $includeOffline = false) {
    $result = [];

    $where = $includeOffline
      ? '1'
      : 'status != 1';

    if ($limit !== '') {
      $limit = 'LIMIT '.$limit;
    }

    $sql = rex_sql::factory();
    $sql->setQuery('
      SELECT * FROM
        rex_blogger_entries AS e
      LEFT JOIN
        rex_blogger_categories AS c
        ON e.category=c.id
      WHERE '.$where.'
      ORDER BY e.postedAt DESC
      '.$limit.'
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

    $id = $sql->escape($id);

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
  public function getEntriesBy($query = []) {
    $result = [];
    $sql = rex_sql::factory();

    $category = $query['category'];

    $tags = isset($query['tags']) ? $query['tags'] : null;
    $year = isset($query['year']) ? $query['year'] : null;
    $month = isset($query['month']) ? $query['month'] : null;
    $author = isset($query['author']) ? $query['author'] : null;
    $limit = isset($query['limit']) ? $query['limit'] : null;
    $latest = isset($query['latest']) ? $query['latest'] : null;
    $includeOfflines = isset($query['includeOfflines']) ? $query['includeOfflines'] : null;

    $where = [];

    // check category
    if ($category !== null) {
      $cat = '(e.category='.$sql->escape($category).')';
      $where[] = $cat;
    }

    // check tags
    if ($tags !== null && empty($tags) === false) {
      foreach ($tags as $tag) {
        $tag = (
          'e.tags='.$tag.
          ' OR e.tags LIKE '.$sql->escape('%|'.$tag).
          ' OR e.tags LIKE '.$sql->escape($tag.'|%').
          ' OR e.tags LIKE '.$sql->escape('%|'.$tag.'|%')
        );

        $tag = '('.$tag.')';
        $where[] = $tag;
      }
    }

    // check year
    if ($year !== null) {
      $where[] = '(YEAR(e.postedAt)='.$sql->escape($year).')';
    }

    // check month
    if ($month !== null) {
      $where[] = '(MONTH(e.postedAt)='.$sql->escape($month).')';
    }

    // check author
    if ($author !== null) {
      $where[] = '(e.postedBy='.$sql->escape($author).')';
    }

    // don't include the offlines
    if ($includeOfflines === null || $includeOfflines === false) {
      $where[] = '(e.status=0)';
    }

    if (empty($where)) {
      $where = "1";
    } else {
      $where = implode(' AND ', $where);
    }

    // order by post date descending
    if ($latest === null || $latest === false) {
      $order = '';
    } else {
      $order = 'ORDER BY e.postedAt DESC';
    }


    if ($limit === null) {
      $limit = '';
    } else {
      $limit = explode(',', $limit);
      $limit = array_map(function($limit) use ($sql) {
        return (int) $limit;
      }, $limit);
      $limit = implode(', ', $limit);
      $limit = 'LIMIT '.$limit;
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
      '.$order.'
      '.$limit.'
    ');

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
  private function fromSql($sql, int $clang = 0) {
    $temp = [];

    $clang = $clang ? $clang : rex_clang::getCurrentId();

    $temp['id'] = (int) $sql->getValue('e.id');
    $temp['categoryId'] = (int) $sql->getValue('c.id');
    $temp['categoryName'] = $sql->getValue('c.name_' . $clang);
    $temp['tags'] = $this->getTagNames(explode('|', $sql->getValue('e.tags')));
    $temp['status'] = (int) $sql->getValue('e.status');
    $temp['postedBy'] = $sql->getValue('e.postedBy');
    $temp['postedAt'] = $sql->getValue('e.postedAt');
    $temp['content'] = $this->getContent($temp['id']);

    if ($temp['categoryName'] === '') {
      $clangs = rex_clang::getAll();

      foreach ($clangs as $clang) {
        $temp['categoryName'] = $sql->getValue('c.name_' . $clang->getId());

        if ($temp['categoryName'] != '') {
          break;
        }
      }
    }

    return $temp;
  }


  /**
   * Return array of tags
   *
   * @return array
   */
  private function getTagNames(array $tagIds, $clangId = null) {
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

    $clang = $clangId ?: rex_clang::getCurrentId();

    while ($sql->hasNext()) {
      $temp = [];

      $temp['id'] = $sql->getValue('id');
      $temp['tag'] = self::getFirstTagNameFromSql($sql, $clang);

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
  public function getCategories($clang = null) {
    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_categories');
    $sql->select();
    $sql->execute();

    $result = [];
    $clang = $clang ? $clang : rex_clang::getCurrentId();

    while ($sql->hasNext()) {
      $temp = [];

      $temp['id'] = (int) $sql->getValue('id');
      $temp['name'] = self::getFirstCategoryNameFromSql($sql, $clang);

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
  public function getTags($clangId = null) {
    $sql = rex_sql::factory();
    $sql->setTable('rex_blogger_tags');
    $sql->select();
    $sql->execute();

    $result = [];

    $clang = $clangId ?: rex_clang::getCurrentId();

    while ($sql->hasNext()) {
      $temp = [];

      $temp['id'] = (int) $sql->getValue('id');
      $temp['tag'] = self::getFirstTagNameFromSql($sql, $clang);

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
  public function getMonths($includeOffline = false) {

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


  /**
   * Gets the first valid tag from an sql object from the table `rex_blogger_tags`.
   * If the clangid is zero it will use the current one.
   */
  protected static function getFirstTagNameFromSql(rex_sql $sql, int $clangId = 0) {
    $clangs = rex_clang::getAll();
    $cid = $clangId ? $clangId : rex_clang::getCurrentId();

    $tag = $sql->getValue('tag_' . $cid);

    if ($tag == '') {
      foreach ($clangs as $clang) {
        $name = 'tag_' . $clang->getId();
        $tag = $sql->getValue($name);

        if ($tag) {
          break;
        }
      }
    }

    return $tag;
  }


  /**
   * Gets the first valid name from an sql object frm the table `rex_blogger_categories`.
   * If the clangid is zero it will use the current one.
   */
  protected static function getFirstCategoryNameFromSql(rex_sql $sql, int $clangId = 0) {
    $clangs = rex_clang::getAll();
    $cid = $clangId ? $clangId : rex_clang::getCurrentId();

    $name = $sql->getValue('name_' . $cid);

    if ($name == '') {
      foreach ($clangs as $clang) {
        $name = $sql->getValue('name_' . $clang->getId());

        if ($name) {
          break;
        }
      }
    }

    return $name;
  }
}

?>
