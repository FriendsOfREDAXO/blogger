<?php

class BloggerEntry {

	// member variables
	private $id;			// int
	private $aid;			// article id //int
	private $translation;	// boolean
	private $clang;			// int
	private $category;		// String
	private $preview;		// String
	private $headline;		// String
	private $content;		// String; ehm. text
	private $gallery;		// String;
	private $tags;			// String[]
	private $offline;		// boolean
	private $postDate;		// datetime String
	private $createdBy;		// int String
	private $createdAt;		// datetime String
	private $updatedBy;		// int String
	private $updatedAt; 	// datetime String

	public function __construct() { }

	public function setId($id) {$this->id = $id;}
	public function getId() {return $this->id;}

	public function setArtId($artId) {$this->aid = $artId;}
	public function getArtId() {return $this->aid;}

	public function setTranslation($translation) {$this->translation = $translation;}
	public function getTranslation() {return $this->translation;}

	public function setClang($clang) {$this->clang = $clang;}
	public function getClang() {return $this->clang;}

	public function setCategory($category) {$this->category = $category;}
	public function getCategory() {return $this->category;}

	public function setPreview($preview) {$this->preview = $preview;}
	public function getPreview() {return $this->preview;}

	public function setHeadline($headline) {$this->headline = $headline;}
	public function getHeadline() {return $this->headline;}

	public function setContent($content) {$this->content = $content;}
	public function getContent() {return $this->content;}

	public function setGallery($gallery) {$this->gallery = $gallery;}
	public function getGallery() {return $this->gallery;}

	public function setTags($tags) {$this->tags = $tags;}
	public function getTags() {return $this->tags;}

	public function setOffline($offline) {$this->offline = $offline;}
	public function isOffline() {return $this->offline;}

	public function setPostDate($date) {$this->postDate = $date;}
	public function getPostDate() {return $this->postDate;}

	public function setCreatedBy($createdBy) {$this->createdBy = $createdBy;}
	public function getCreatedBy() {return $this->createdBy;}

	public function setCreatedAt($createdAt) {$this->createdAt = $createdAt;}
	public function getCreatedAt() {return $this->createdAt;}

	public function setUpdatedBy($updatedBy) {$this->updatedBy = $updatedBy;}
	public function getUpdatedBy() {return $this->updatedBy;}

	public function setUpdatedAt($updatedAt) {$this->updatedAt = $updatedAt;}
	public function getUpdatedAt() {return $this->updatedAt;}


	public function getUrl() {
		$char = (rex_addon::exists('yrewrite')) ? '?' : '&';
		return rex_article::getCurrent()->getUrl().$char.'blogger_entry='.$this->id;
	}

}

?>