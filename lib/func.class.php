<?php

class rex_blogger_func {

	/**
	 * Returns an array of rex_blogger_entries as a rex_blogger array from an rex_sql object
	 *
	 * @param rex_sql $sql
	 *
	 * @return rex_blogger_entry[]
	 */
	protected static function get_by_sql($sql)
	{
		$entries = array();
		while ($sql->hasNext()) {
			$entry = new rex_blogger_entry();

			$entry->setId( $sql->getValue('e.id') );
			$entry->setArtId( $sql->getValue('e.art_id') );
			$entry->setTranslation( ($sql->getValue('e.translation') == 0) ? false : true );
			$entry->setClang( $sql->getValue('e.clang') );
			$entry->setCategory( $sql->getValue('c.name') );
			$entry->setPreview( $sql->getValue('e.preview') );
			$entry->setHeadline( $sql->getValue('e.headline') );
			$entry->setContent( $sql->getValue('e.content') );
			$entry->setGallery( $sql->getValue('e.gallery') );

			$entry->setTags( self::get_tags_from_value($sql->getValue('e.tags')) );
			$entry->setOffline( ($sql->getValue('e.offline') == 0) ? false : true );

			$entry->setPostDate( $sql->getValue('e.post_date') );

			$entry->setCreatedBy( $sql->getValue('e.createdBy') );
			$entry->setCreatedAt( $sql->getValue('e.createdAt') );
			$entry->setUpdatedBy( $sql->getValue('e.updatedBy') );
			$entry->setUpdatedAt( $sql->getValue('e.updatedAt') );

			$entries[] = $entry;

			$sql->next();
		}

		return $entries;

	}

	/**
	 * Returns all categories used in a multiple select field as a string array
	 *
	 * @param String $string
	 * @param char $delimiter
	 *
	 * @return String[]
	 */
	protected static function get_tags_from_value($string, $delimiter='|')
	{

		$tags = array();
		$tmp = array_filter(explode($delimiter, $string));

		$query = 'SELECT `tag` FROM `'.rex::getTablePrefix().'blogger_tags` ';
		$query .= 'WHERE';
		foreach ($tmp as $key=>$value) {
			$query .= ' `id`=' . $value . ' OR';
		}
		$query .= ' `id`=-1';

		$sql = rex_sql::factory();
		$sql->setQuery($query);
		$sql->execute();

		while ($sql->hasNext()) {
			array_push($tags, $sql->getValue('tag'));			
			$sql->next();
		}

		return $tags;

	}


	/**
	 * Returns the entry where the id is equal to $id
	 *
	 * @param int $id
	 *
	 * @return rex_blogger_entry
	 */
	public static function get_by_id($id, $ignoreOfflines=true)
	{

		$query = 'SELECT e.*, c.`name` FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
		$query .= 'ON e.`category`=c.`id` ';
		$query .= sprintf('WHERE e.`id`=%u', $id);
		if ($ignoreOfflines) {
			$query .= ' AND e.`offline`=0';
		}

		$sql = rex_sql::factory();
		$sql->setQuery($query);
		$sql->execute();

		return rex_blogger::get_by_sql($sql)[0];

	}


	/**
	 * Returns the latest entries as an array with the size of $limit
	 *
	 * @param int $limit
	 *
	 * @return rex_blogger_entry[]
	 */
	public static function get_latest_entries($limit=1, $ignoreOfflines=true)
	{
		// TODO
		return null;
	}


	/**
	 * Returns all tags as an string array
	 *
	 * @return String[]
	 */
	public static function get_tags()
	{

		$tags = array();

		$sql = rex_sql::factory();
		$sql->setTable(rex::getTablePrefix().'blogger_tags');
		$sql->select();
		$sql->execute();

		while ($sql->hasNext()) {
			$tags[] = $sql->getValue('tag');
			$sql->next();
		}		

		return $tags;

	}


	/**
	 * Returns all categories as an string array
	 *
	 * @return String[]
	 */
	public static function get_categories()
	{

		$categories = array();

		$sql = rex_sql::factory();
		$sql->setTable(rex::getTablePrefix().'blogger_categories');
		$sql->select();
		$sql->execute();

		while ($sql->hasNext()) {
			$categories[] = $sql->getValue('name');
			$sql->next();
		}		

		return $categories;

	}


	/**
	 * Returns all months and years used by the entries as a datetime string array
	 *
	 * @param bool $ignoreOfflines
	 *
	 * @return array('year'=>STRING, 'month'=>STRING)
	 */
	public static function get_all_months($reverse=false, $ignoreOfflines=true)
	{

		$dates = array();

		$whereStatement = '';
		if ($ignoreOfflines) {
			$whereStatement = 'WHERE offline=0 AND translation=0';
		}

		$orderStatement = 'ORDER BY year ASC, month ASC';
		if ($reverse) {
			$orderStatement = 'ORDER BY year DESC, month DESC';
		}

		$query = ('
			SELECT DISTINCT
				month(post_date) AS month,
				year(post_date) AS year
			FROM rex_blogger_entries
				'.$whereStatement.'
				'.$orderStatement.'
		');

		$sql = rex_sql::factory();
		$sql->setQuery($query);
		$sql->execute();

		while ($sql->hasNext()) {
			$temp = [];
			$temp['month'] = $sql->getValue('month');
			$temp['year'] = $sql->getValue('year');
			$dates[] = $temp;
			$sql->next();
		}

		return $dates;
	}

}