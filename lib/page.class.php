<?php

class rex_blogger_page extends rex_blogger_func {

	private $current_page;			// int; number of current page
	private $articles_per_page;		// int; number of articles that are shown per page
	private $tags;					// int[]; searched tags
	private $category_id;			// int; searched category
	private $max_page_number;		// int; basically the number of all entries divided by the number of articles per page
	private $single_entry;			// int; when the user is requesting a single entry this will be the id of said entry
	private $clang;					// int; holds the clang value

	/**
	 * The constructor sets the limit for entries on each page
	 *
	 * @param int $articles
	 */
	protected function __construct($articles)
	{
		$this->current_page = rex_request('blogger_page', 'int');
		$this->articles_per_page = $articles;

		$this->set_tags(rex_request('blogger_tags', 'string'));
		$this->category_id = rex_request('blogger_category', 'int');
		$this->set_max_page_number();

		$this->single_entry = rex_request('blogger_entry', 'int');
		$this->clang = rex_clang::getCurrent()->getId() ?: 1;
	}


	/**
	 * Returns true if the user is requesting a single entry
	 *
	 * @return bool
	 */
	public function is_single_entry()
	{
		return ($this->single_entry) ? true : false;
	}


	/**
	 * Returns the entry with the id from $this->single_entry
	 * if no entry is found the result will be null
	 *
	 * @return rex_blogger_entry
	 */
	public function get_single_entry()
	{
		if (!$this->is_single_entry())
			return null;

		$query = 'SELECT e.*, c.`name` FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
		$query .= 'ON e.`category`=c.`id` ';
		$query .= 'WHERE e.`offline`=0 ';
		$query .= sprintf('AND (e.`art_id`=%u)', $this->single_entry);

		$sql = rex_sql::factory();
		$sql->setQuery($query);
		$sql->execute();

		$tmp = parent::get_by_sql($sql);

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
	 * Returns a rex_blogger_entry array with the entries for the current page
	 * 
	 * @return rex_blogger_entry[]
	 */
	public function get_entries_blog_page()
	{
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
		if ($this->category_id) {
			$query .= sprintf(' AND e.`category`=%u', $this->category_id);
		}

		// group distinct
		$query .= ' GROUP BY(e.`art_id`)';

		// limit
		$query .= sprintf(' LIMIT %u, %u', $this->current_page*$this->articles_per_page, $this->articles_per_page);

		$sql = rex_sql::factory();
		$sql->setQuery($query);

		$tmp = parent::get_by_sql($sql);

		return $tmp;

	}


	/**
	 * Sets the max page number for this page instance
	 * 
	 *
	 */
	private function set_max_page_number()
	{

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
		if ($this->category_id) {
			$query .= sprintf(' AND e.`category`=%u', $this->category_id);
		}

		// group distinct
		$query .= ' GROUP BY(e.`art_id`)';

		$sql = rex_sql::factory();
		$sql->setQuery($query);
		$sql->execute();

		$this->max_page_number = (int)ceil($sql->getRows()/$this->articles_per_page);

	}


	/**
	 * Returns the max page number of this page instance
	 *
	 * @return int
	 */
	public function get_max_page_number()
	{
		return $this->max_page_number;
	}


	/**
	 * sets the tags as a String array
	 *
	 * @param String $query
	 * @param char $delimiter
	 */
	public function set_tags($query, $delimiter=',')
	{
		$this->tags = explode($delimiter, $query);
	}


	/**
	 * sets the category as an id
	 *
	 * @param int $category_id
	 */
	public function set_category_id($category_id)
	{
		$this->category_id = $category_id;
	}


	/**
	 * Returns the url for the n-th page
	 *
	 * @param int $counter
	 *
	 * @return String
	 */
	public function get_url_page($page)
	{
		$nextPageNr = $page;
		$currentArticleUrl = rex_article::getCurrent()->getUrl();

		// will stay on 0 if the page is going negative
		if ($nextPageNr < 0)
			$nextPageNr = 0;

		// will return null when the last page is reached
		if ($nextPageNr > $this->max_page_number)
			return null;

		if ($this->tags[0] != '')
			$currentTags = '&blogger_tags='.implode(',', $this->tags);
		else
			$currentTags = '';

		if ($this->category_id)
			$currentCategory = '&blogger_category='.$this->category_id;
		else
			$currentCategory = '';

		$char = (rex_addon::exists('yrewrite')) ? '?' : '&';

		$nextPage = $char.'blogger_page='.$nextPageNr;

		return $currentArticleUrl.$nextPage.$currentTags.$currentCategory;
	}


	/**
	 * Returns the url for the next page
	 *
	 * @return String
	 */
	public function get_url_next_page()
	{
		return $this->get_url_page($this->current_page+1);
	}


	/**
	 * Returns the url for the previous page
	 *
	 * @return String
	 */
	public function get_url_previous_page()
	{
		return $this->get_url_page($this->current_page-1);
	}

}

?>