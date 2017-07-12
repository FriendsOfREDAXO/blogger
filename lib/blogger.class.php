<?php

class rex_blogger extends rex_blogger_page {

	/**
	 * Creates and returns a new rex_blogger object
	 *
	 * @param int $art_per_page
	 */
	public function __construct($art_per_page=5) {
		parent::__construct($art_per_page);
	}

}

?>