<?php 

class BloggerForms {

  public function addMetaFields($form) {
    // category
    $field = $form->addSelectField('category');
    $field->setLabel('Kategorie');

    $select = $field->getSelect();
    $select->setSize(1);
  
    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'blogger_categories');
    $sql->select('`id`, `name`');
    $sql->execute();

    while ($sql->hasNext()) {
      $select->addOption($sql->getValue('name'), $sql->getValue('id'));
      $sql->next();
    }

    // tags
    $field = $form->addSelectField('tags');
    $field->setLabel('Tags');

    $select = $field->getSelect();
    $select->setMultiple(true);

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'blogger_tags');
    $sql->select('`id`, `tag`');
    $sql->execute();

    while ($sql->hasNext()) {
      $select->addOption($sql->getValue('tag'), $sql->getValue('id'));
      $sql->next();
    }

    // postedAt
    $myTime = new DateTime($form->getSql()->getValue('postedAt'));
    $field = $form->addInputField(
      'datetime-local',
      'postedAt',
      date('Y-m-d', $myTime->getTimestamp()).'T'.date('H:i', $myTime->getTimestamp())
    );
    $field->setLabel('postedAt');

    // postedBy
    $field = $form->addTextField('postedBy');
    $field->setLabel('postedBy');

    // status
    // TODO
  }

  public function addContentFields($form) {
    // content-fields, make reusable for add-function
    // - language (disabled -> force field for every lang)

    // title
    $field = $form->addTextField('title');
    $field->setLabel('Title');

    // text
    $field = $form->addTextAreaField('text', null, array("class"=>"redactorEditor2-full"));
    $field->setLabel('Text');

    // preview
    $field = $form->addMediaField('preview');
    $field->setLabel('Preview Bild');

    // gallery
    $field = $form->addMediaListField('gallery');
    $field->setLabel('Gallery');
  }
}

?>