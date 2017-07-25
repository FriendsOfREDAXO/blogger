<?php
	$beBlogger = new BeBlogger();

  dump($_SERVER['REQUEST_METHOD']);
  dump($_GET ?: $_POST);

	echo $beBlogger->getPage();
?>