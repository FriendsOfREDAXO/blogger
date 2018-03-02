<?php
  $blogger = rex_addon::get('blogger');
  $config = rex_config::get('blogger');

  if ($_POST) {
    $newConfig = $_POST['blogger']['config'];

    if ($newConfig['gallery'] != 'on') {
      $newConfig['gallery'] = 'off';
    }

    rex_config::set('blogger', $newConfig);

    $config = $newConfig;
  }
?>

<form method="POST" action="index.php?page=blogger/config" enctype="multipart/form-data">

  <dl class="rex-form-group form-group">
    <dt>
      <label class="control-label" for="">
        <?= $blogger->i18n('config_texteditor_class') ?>
        <code>class</code>
      </label>
    </dt>
    <dd>
      <input
        type="text"
        name="blogger[config][texteditor]"
        value="<?= $config['texteditor'] ?>"
        placeholder="e.g. redactorEditor2-full"
        class="form-control">
    </dd>
  </dl>

  <dl class="rex-form-group form-group">
    <dt>
      <label class="control-label" for="">
        <?= $blogger->i18n('config_show_gallery') ?>
      </label>
    </dt>
    <dd>
      <?php 
        $attr = $config['gallery'] == 'on' ? ' checked' : '';
      ?>
      <input type="checkbox" name="blogger[config][gallery]"<?= $attr ?>>
    </dd>
  </dl>

  <div class="btn-toolbar">
    <button name="blogger[config][action]" value="save" class="btn btn-save">
      <?= $blogger->i18n('btn_save') ?>
    </button>
  </div>
</form>
