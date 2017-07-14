<?php
	$beBlogger = new BeBlogger();
	$beBlogger->handleRequest();

	echo $beBlogger->getPage();
?>