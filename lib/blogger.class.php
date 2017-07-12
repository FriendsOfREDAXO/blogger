<?php

class Blogger extends BloggerPage {

	/**
	 * Creates and returns a new Blogger object
	 *
	 * @param int $articlesPerPage
	 */
	public function __construct($articlesPerPage=5) {
		parent::__construct($articlesPerPage);
	}

}

?>