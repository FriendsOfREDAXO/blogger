<?php

if (rex::isBackend()) {
  rex_view::addCssFile($this->getAssetsUrl('css/blogger.css'));
  rex_view::addJsFile($this->getAssetsUrl('js/blogger.js'));
}

?>