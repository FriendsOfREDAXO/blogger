<?php

class BloggerPage extends BloggerFunc {
	private $currentPage;			// int; number of current page
	private $articlesPerPage;		// int; number of articles that are shown per page
	private $tags;					// int[]; searched tags
	private $categoryId;			// int; searched category
	private $maxPageNumber;		// int; basically the number of all entries divided by the number of articles per page
	private $singleEntry;			// int; when the user is requesting a single entry this will be the id of said entry
	private $clang;					// int; holds the clang value
	private $year;
	private $month;

	/**
	 * The constructor sets the limit for entries on each page
	 *
	 * @param int $articles
	 */
	protected function __construct($articles) {
		$this->currentPage = rex_request('bloggerPage', 'int');
		$this->articlesPerPage = $articles;

		$this->setTags(rex_request('bloggerTags', 'string'));
		$this->categoryId = rex_request('bloggerCategory', 'int');
		$this->setMaxPageNumber();

		// check if date is given
		$this->year = rex_request('bloggerYear', 'int');
		$this->month = rex_request('bloggerMonth', 'int');

		$this->singleEntry = rex_request('bloggerEntry', 'int');
		$this->clang = rex_clang::getCurrent()->getId() ?: 1;
	}


	/**
	 * Returns true if the user is requesting a single entry
	 *
	 * @return bool
	 */
	public function isSingleEntry() {
		return ($this->singleEntry) ? true : false;
	}


	/**
	 * Returns the entry with the id from $this->singleEntry
	 * if no entry is found the result will be null
	 *
	 * @return BloggerEntry
	 */
	public function getSingleEntry() {
		if (!$this->isSingleEntry())
			return null;

		$query = 'SELECT e.*, c.`name` FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
		$query .= 'ON e.`category`=c.`id` ';
		$query .= 'WHERE e.`offline`=0 ';
		$query .= sprintf('AND (e.`art_id`=%u)', $this->singleEntry);

		$sql = rex_sql::factory();
		$sql->setQuery($query);
		$sql->execute();

		$tmp = parent::getBySql($sql);

		if (empty($tmp))
			return null;

		foreach ($tmp as $key=>$value) {
			if ($value->getClang() == $this->clang) {
				return $value;
			}
		}

		return $tmp[0];
	}


	/**
	 * Returns a BloggerEntry array with the entries for the current page
	 * 
	 * @return BloggerEntry[]
	 */
	public function getEntriesBlogPage() {
		$query = 'SELECT
			e.*,
			c.`name`,
			UNIX_TIMESTAMP(e.`post_date`) AS postTimestamp
			FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
		$query .= 'ON e.`category`=c.`id` ';
		$query .= 'WHERE e.`offline`=0 AND e.`clang`='.rex_clang::getCurrent()->getId();

		// year without month
		if ($this->year && !$this->month) {
			$query .= ' AND (year(e.post_date)='.$this->year.')';
		}

		// year and month
		if ($this->year && $this->month) {
			$query .= ' AND (year(e.post_date)='.$this->year.')';
			$query .= ' AND (month(e.post_date)='.$this->month.')';
		}

		// tags
		if (!empty($this->tags)) {
			foreach ($this->tags as $key=>$tag) {
				if ($tag == '') break;
				$query .= ' AND (e.`tags` LIKE "%'.$tag.'%")';
			}
		}

		// category
		if ($this->categoryId) {
			$query .= sprintf(' AND e.`category`=%u', $this->categoryId);
		}

		// group distinct
		$query .= ' GROUP BY(e.`art_id`)';

		// order
		$query .= ' ORDER BY postTimestamp DESC';

		// limit
		$query .= sprintf(' LIMIT %u, %u',
			$this->currentPage*$this->articlesPerPage,
			$this->articlesPerPage
		);

		$sql = rex_sql::factory();
		$sql->setQuery($query);

		$tmp = parent::getBySql($sql);

		return $tmp;
	}


	/**
	 * Sets the max page number for this page instance
	 * 
	 *
	 */
	private function setMaxPageNumber() {

		$query = 'SELECT e.*, c.`name` FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
		$query .= 'ON e.`category`=c.`id` ';
		$query .= 'WHERE e.`offline`=0 AND e.`clang`='.rex_clang::getCurrent()->getId();

		// tags
		if (!empty($this->tags)) {
			foreach ($this->tags as $key=>$tag) {
				if ($tag == '') break;
				$query .= ' AND (e.`tags` LIKE "%'.$tag.'%")';
			}
		}

		// category
		if ($this->categoryId) {
			$query .= sprintf(' AND e.`category`=%u', $this->categoryId);
		}

		// group distinct
		$query .= ' GROUP BY(e.`art_id`)';

		$sql = rex_sql::factory();
		$sql->setQuery($query);
		$sql->execute();

		$this->maxPageNumber = (int)ceil($sql->getRows()/$this->articlesPerPage);
	}


	/**
	 * Returns the max page number of this page instance
	 *
	 * @return int
	 */
	public function getMaxPageNumber() {
		return $this->maxPageNumber;
	}


	/**
	 * sets the tags as a String array
	 *
	 * @param String $query
	 * @param char $delimiter
	 */
	public function setTags($query, $delimiter=',') {
		$this->tags = explode($delimiter, $query);
	}


	/**
	 * sets the category as an id
	 *
	 * @param int $categoryId
	 */
	public function setCategoryId($categoryId) {
		$this->categoryId = $categoryId;
	}


	/**
	 * Returns the url for the n-th page
	 *
	 * @param int $counter
	 *
	 * @return String
	 */
	public function getUrlPage($page) {
		$nextPageNr = $page;
		$currentArticleUrl = rex_article::getCurrent()->getUrl();

		// will stay on 0 if the page is going negative
		if ($nextPageNr < 0)
			$nextPageNr = 0;

		// will return null when the last page is reached
		if ($nextPageNr > $this->maxPageNumber)
			return null;

		// default values
		$currentTags = '';
		$currentCategory = '';
		$currentYear = '';
		$currentMonth = '';

		// tags
		if ($this->tags[0] != '')
			$currentTags = '&bloggerTags='.implode(',', $this->tags);

		// category
		if ($this->categoryId)
			$currentCategory = '&bloggerCategory='.$this->categoryId;

		// year
		if ($this->year)
			$currentYear = '&bloggerYear='.$this->year;

		// month
		if ($this->month)
			$currentMonth = '&bloggerMonth='.$this->month;

		$char = (rex_addon::exists('yrewrite')) ? '?' : '&';

		$nextPage = $char.'bloggerPage='.$nextPageNr;

		return $currentArticleUrl.$nextPage.$currentTags.$currentCategory.$currentYear.$currentMonth;
	}


	/**
	 * Returns the url for the next page
	 *
	 * @return String
	 */
	public function getUrlNext() {
		return $this->getUrlPage($this->currentPage+1);
	}


	/**
	 * Returns the url for the previous page
	 *
	 * @return String
	 */
	public function getUrlPrev() {
		return $this->getUrlPage($this->currentPage-1);
	}
}

?>