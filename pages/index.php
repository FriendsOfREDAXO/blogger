<?php


// set the title and include subpages
echo rex_view::title(rex_i18n::msg('blogger_title'));
$subpage = rex_be_controller::getCurrentPagePart(1);
include rex_be_controller::getCurrentPageObject()->getSubPath();
