<?php 

class BloggerForms {
  private $id;    // int, unique id of article
  private $pid;   // int, parent id
  private $func;  // String, requested function
  private $render;  // String, htmlpage

  public function addMetaFields($form) {
    // category
    $field = $form->addSelectField('category');
    $field->setLabel('Kategorie');

    $select = $field->getSelect();
    $select->setSize(1);
  
    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'blogger_categories');
    $sql->select('id, name');
    $sql->execute();

    while ($sql->hasNext()) {
      $select->addOption($sql->getValue('name'), $sql->getValue('id'));
      $sql->next();
    }

    // tags
    $field = $form->addSelectField('entry.tags');
    $field->setLabel('Tags');

    $select = $field->getSelect();
    $select->setMultiple(true);

    $sql = rex_sql::factory();
    $sql->setTable(rex::getTablePrefix().'blogger_tags');
    $sql->select('id, tag');
    $sql->execute();

    while ($sql->hasNext()) {
      $select->addOption($sql->getValue('tag'), $sql->getValue('id'));
      $sql->next();
    }

    // postedAt
    $myTime = new DateTime($form->getSql()->getValue('entry.postedAt'));
    $field = $form->addInputField(
      'datetime-local',
      'postedAt',
      date('Y-m-d', $myTime->getTimestamp()).'T'.date('H:i', $myTime->getTimestamp())
    );
    $field->setLabel('Post Tag');

    // postedBy
    $field = $form->addTextField('entry.postedBy');
    $field->setLabel('Author');

    // status
    // TODO
  }


  public function addContentFields($form) {
    // content-fields, make reusable for add-function
    // - language (disabled -> force field for every lang)

    // title
    $field = $form->addTextField('cont.title');
    $field->setLabel('Title');

    // text
    $field = $form->addTextAreaField('cont.text', null, array("class"=>"redactorEditor2-full"));
    $field->setLabel('Text');

    // preview
    $field = $form->addMediaField('cont.preview');
    $field->setLabel('Preview Bild');

    // gallery
    $field = $form->addMediaListField('cont.gallery');
    $field->setLabel('Gallery');
  }
}

?>