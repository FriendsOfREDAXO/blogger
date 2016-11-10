<?php

	$func = rex_request('func', 'string');
	$type_id = rex_request('type_id', 'int') ?: rex_request('id', 'int');
	$translation = rex_request('translation_main', 'int');

	if ($func == 'offline') {

		$sql = rex_sql::factory();
		//  $sql->setDebug();
		$sql->setTable(rex::getTablePrefix() . 'blogger_entries');
		$sql->setWhere(sprintf('`id`=%u OR `translation`=%u', $type_id, $type_id));
		$sql->setValues(array("offline"=>"1"));

		try {
			$sql->update();
		} catch (rex_sql_exception $e) {
			echo $e;
		}

		$func = '';

	}

	if ($func == 'online') {

		$sql = rex_sql::factory();
		//  $sql->setDebug();
		$sql->setTable(rex::getTablePrefix() . 'blogger_entries');
		$sql->setWhere(['id' => $type_id]);
		$sql->setValues( array("offline"=>"0") );

		try {
			$sql->update();
		} catch (rex_sql_exception $e) {
			echo $e;
		}

		$func = '';
	}

	if ($func == '') {
		/**
		 * STANDARD
		 */

		$list = rex_list::factory("SELECT e.`id`, e.`artId`, e.`headline`, c.`name`, e.`createdAt`, e.`offline` FROM `".rex::getTablePrefix()."blogger_entries` AS e LEFT JOIN `".rex::getTablePrefix()."blogger_categories` AS c ON e.`category`=c.`id` WHERE e.`translation`=0 ORDER BY e.`id`");

		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage('Es wurden keine Einträge gefunden');

		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

		$list->setColumnLabel('id', 'Id');
		$list->setColumnLabel('artId', 'Artikel ID');
		$list->setColumnLabel('headline', 'Headline');
		$list->setColumnLabel('name', 'Kategorie');

		$list->setColumnLabel('createdAt', 'Erstellt am');
		$list->setColumnFormat('createdAt', 'custom', function ($params) {
			$list = $params['list'];

			if ($list->getValue('e.createdAt') != "0000-00-00 00:00:00") {
				return date('d.m.Y', strtotime($list->getValue('e.createdAt')));
			} else {
				return 'Ungültige Zeitangabe';
			}

		});

		$list->setColumnLabel('offline', '');
	    $list->setColumnFormat('offline', 'custom', function ($params) {
	        $list = $params['list'];

	        if ($list->getValue('e.offline') == "1") {
	        	$list->addLinkAttribute('offline', 'class', 'rex-offline');
	            return $list->getColumnLink('offline', '<i class="rex-icon rex-icon-offline"></i>&nbsp;offline', ['type_id' => '###id###', 'func' => 'online']);
	        } else {
	        	$list->addLinkAttribute('offline', 'class', 'rex-online');
	        	return $list->getColumnLink('offline', '<i class="rex-icon rex-icon-online"></i>&nbsp;online', ['type_id' => '###id###', 'func' => 'offline']);
	        }

	    });


		$content = $list->get();

		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');

		echo $content;
	
	} else if ($func == 'edit' || $func == 'add') {
		/**
		 * EDIT & ADD
		 */

		if ($func == 'edit') {
			$formLabel = 'Bearbeiten';
		} else if ($func == 'add') {
			$formLabel = 'Neuen Eintrag erstellen';
		}

		$form = rex_form::factory(rex::getTablePrefix().'blogger_entries', '', 'id='.$type_id);

		// generic
		if ($translation === 0) {
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
		} else {
			$field = $form->addHiddenField('category', $translation);
		}

		$field = ($func == 'add' && $translation) ? $form->addTextField('headline', $sql->getValue('headline')) : $form->addTextField('headline');
		$field->setLabel('Headline');

		$field = ($func == 'add' && $translation) ? $form->addMediaField('preview', $sql->getValue('preview')) : $form->addMediaField('preview');
		$field->setLabel('Preview Bild');

		$field = $form->addTextAreaField('text', null, array("class"=>"redactorEditor2-full"));
		$field->setLabel('Text');

		$field = ($func == 'add' && $translation) ? $form->addMediaListField('gallery', $sql->getValue('gallery')) : $form->addMediaListField('gallery');
		$field->setLabel('Gallery');

		$field = ($func == 'add' && $translation) ? $form->addSelectField('tags', $sql->getValue('tags')) : $form->addSelectField('tags');
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

		$field = $form->addSelectField('clang');
		$field->setLabel("Sprache");
		
		$select = $field->getSelect();
		$select->setSize(1);

		$field = $form->addHiddenField('artId', $translation ?: $type_id);

		if (!($translation === 0)) {
			$sql = rex_sql::factory();
			$sql->setQuery(sprintf('SELECT `clang` FROM `'.rex::getTablePrefix().'blogger_entries` WHERE id=%u', $translation));
			$sql->execute();
			foreach (rex_clang::getAll() as $key=>$value) {
				if ($sql->getValue('clang') != $value->getId())
					$select->addOption($value->getName(), $value->getId());
			}
		} else {
			foreach (rex_clang::getAll() as $key=>$value) {
				$select->addOption($value->getName(), $value->getId());
			}
		}
		// generic



		// translation table
		if ($func == 'edit' && $translation == false) {

			$sql = rex_sql::factory();
			$sql->setQuery('SELECT * FROM `'.rex::getTablePrefix().'blogger_entries` WHERE `translation`='.$type_id);
			$sql->execute();

			$query = 'SELECT e.`id`, l.`name` AS `langname`, e.`headline`, c.`name`, e.`offline` ';
			$query .= 'FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
			$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
			$query .= 'ON e.`category`=c.`id` ';
			$query .= 'LEFT JOIN `'.rex::getTablePrefix().'clang` AS l ';
			$query .= 'ON e.`clang`=l.`id` ';
			$query .= 'WHERE e.`translation`='.$type_id.' ';
			$query .= 'ORDER BY e.`id`';

			$list = rex_list::factory($query);

			$list->addTableAttribute('class', 'table-striped');
			$list->setNoRowsMessage('Es wurden keine Einträge gefunden');

			$thIcon = '<a href="'.$list->getUrl(['func' => 'add', 'translation_main' => $type_id]).'"><i class="rex-icon rex-icon-add-action"></i></a>';
			$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

			$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
			$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###', 'translation_main' => $type_id]);

			$list->setColumnLabel('id', 'Id');
			$list->setColumnLabel('langname', 'Sprache');
			$list->setColumnLabel('headline', 'Headline');
			$list->setColumnLabel('name', 'Kategorie');

			$list->setColumnLabel('offline', '');
			$list->setColumnFormat('offline', 'custom', function ($params) {
			    $list = $params['list'];

			    if ($list->getValue('e.offline') == "1") {
			    	$list->addLinkAttribute('offline', 'class', 'rex-offline');
			        return $list->getColumnLink('offline', '<i class="rex-icon rex-icon-offline"></i>&nbsp;offline', ['type_id' => '###id###', 'func' => 'online']);
			    } else {
			    	$list->addLinkAttribute('offline', 'class', 'rex-online');
			    	return $list->getColumnLink('offline', '<i class="rex-icon rex-icon-online"></i>&nbsp;online', ['type_id' => '###id###', 'func' => 'offline']);
			    }

			});


			$content = $list->get();

			$fragment = new rex_fragment();
			$fragment->setVar('content', $content, false);
			$content = $fragment->parse('core/page/section.php');

			echo '<h4>Übersetzungen</h4>';
			echo $content;

		}
		// translation table


		if ($translation != true) {
			$sql = rex_sql::factory();
			$sql->setQuery(sprintf('SELECT `headline` FROM `'.rex::getTablePrefix().'blogger_entries` WHERE id=%u', $translation));
			$sql->execute();
			$url = sprintf('http://mars.demoweb24.de/redaxo/index.php?page=blogger/entries&func=edit&id=%u', $translation);
			$form->setMessage('Dies ist eine Übersetzung für <a href="'.$url.'">"'. $sql->getValue('headline') .'" ['.$translation.']</a>');
		}
		// relative


		// time
		if ($func == 'add') {
			$form->addHiddenField('createdAt', date('Y-m-d H:i:s'));
			$form->addHiddenField('createdBy', rex::getUser()->getId());
		} else if ($func == 'edit') {
			$form->addHiddenField('updatedAt', date('Y-m-d H:i:s'));
			$form->addHiddenField('updatedBy', rex::getUser()->getId());
		}
		// time


		if ($func == 'edit') {
			$form->addParam('type_id', $type_id);
		}

		$content = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);
		$content = $fragment->parse('core/page/section.php');

		echo $content;

	}


?>