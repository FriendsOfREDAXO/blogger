<?php

$form = rex_config_form::factory('blogger');

$field = $form->addTextField('texteditor');
$field->setLabel(rex_i18n::msg('blogger_config_texteditor_class'));

$field = $form->addCheckboxField('gallery');
$field->setLabel(rex_i18n::msg('blogger_config_show_gallery'));
$field->addOption(rex_i18n::msg('blogger_config_show_gallery_option'), 1);

$form->show();
