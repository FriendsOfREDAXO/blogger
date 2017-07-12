<?php

	$func = rex_request('func', 'string');
	$type_id = rex_request('type_id', 'int') ?: rex_request('id', 'int');
	$parent_id = rex_request('parent_id', 'int');

	$beBlogger = new Blogger\BeBlogger();

	if (!empty($_POST)) {
		$edited = reset($_POST);
		if ($func == 'edit') {
			try {
				$updateSql = rex_sql::factory();
				$updateSql->setTable(rex::getTablePrefix().'blogger_entries');
				$updateSql->setWhere(['aid'=>$type_id]);
				$updateSql->setValues([
					'category'=>$edited['category'],
					'tags'=>"|".implode('|', $edited['tags'])."|",
					'postedAt'=>$edited['postedAt']
				]);
				$updateSql->update();
			} catch (rex_sql_exception $e) {
				echo $e;
			}
		}
	} elseif (isset($_GET['list']) && isset($_GET['form']) && $func == '') {
		try {
			$query = 'UPDATE `'.rex::getTablePrefix().'blogger_entries` ';
			$query .= 'SET `aid` = `id` ';
			$query .= 'WHERE `aid`=0;';

			$updateSql = rex_sql::factory();
			$updateSql->setQuery($query);
			$updateSql->execute();
		} catch (rex_sql_exception $e) {
			echo $e;
		}
	}


	if ($func == 'offline') {

		$sql = rex_sql::factory();
		$sql->setTable(rex::getTablePrefix() . 'blogger_entries');
		$sql->setWhere(sprintf('`id`=%u OR `aid`=%u', $type_id, $type_id));
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
		$sql->setTable(rex::getTablePrefix() . 'blogger_entries');
		$sql->setWhere(sprintf('`id`=%u OR `aid`=%u', $type_id, $type_id));
		$sql->setValues(array("offline"=>"0"));

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
		$query = 'SELECT e.id, e.aid, e.headline, c.name, e.postedAt, e.offline ';
		$query .= 'FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
		$query .= 'ON e.`category`=c.`id` ';
		$query .= 'WHERE `translation`=0 ';
		$query .= 'ORDER BY e.`id`';

		$list = rex_list::factory($query);

		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage('Es wurden keine Einträge gefunden');

		$thIcon = '<a href="'.$list->getUrl(['func' => 'add']).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

		$list->removeColumn('id');
		$list->setColumnLabel('aid', 'Nr.');
		$list->setColumnLabel('headline', 'Titel');
		$list->setColumnFormat('headline', 'custom', function($params) {
			$list = $params['list'];

			$title = $list->getValue('headline');
			$output = substr($title, 0, 50);

			return '<span title="'.$title.'">'.$output.'...</span>';
		});

		$list->setColumnLabel('name', 'Kategorie');

		$list->setColumnLabel('postedAt', 'Erstellt am');
		$list->setColumnFormat('postedAt', 'custom', function ($params) {
			$list = $params['list'];

			if ($list->getValue('e.postedAt') != "0000-00-00 00:00:00") {
				return date('d.m.Y', strtotime($list->getValue('e.postedAt')));
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
	
	} else if ($func == 'add') {
		/**
		 * ADD
		 */
		$formLabel = 'Neuen Eintrag erstellen';
		$form = rex_form::factory(rex::getTablePrefix().'blogger_entries', '', 'id='.$type_id);

		// form
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


			// headline
			$field = $form->addTextField('headline');
			$field->setLabel('Headline');


			// creator
			$field = $form->addTextField('postedBy');
			$field->setLabel('Ersteller');
			

			// text
			$field = $form->addTextAreaField('content', null, array("class"=>"redactorEditor2-full"));
			$field->setLabel('Text');


			// preview
			$field = $form->addMediaField('preview');
			$field->setLabel('Preview Bild');


			// gallery
			$field = $form->addMediaListField('gallery');
			$field->setLabel('Gallery');


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


			// post date
			$field = $form->addInputField('datetime-local', 'postedAt', date('Y-m-d').'T'.date('H:i'));
			$field->setLabel('Post Tag');


			// clang
			$field = $form->addSelectField('clang');
			$field->setLabel("Sprache");
			
			$select = $field->getSelect();
			$select->setSize(1);

			foreach (rex_clang::getAll() as $key=>$value) {
				$select->addOption($value->getName(), $value->getId());
			}


		// time update
		$form->addHiddenField('createdAt', date('Y-m-d H:i:s'));
		$form->addHiddenField('createdBy', rex::getUser()->getId());

		// output
		$content = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);

		$content = $fragment->parse('core/page/section.php');
		echo $content;

	} else if ($func == 'edit') {
		/**
		 * EDIT
		 */
		$formLabel = 'Eintrag bearbeiten';
		$form = rex_form::factory(rex::getTablePrefix().'blogger_entries', '', 'id='.$type_id);

		// translation table
		$query = 'SELECT e.`id`,e.`aid`, l.`name` AS `langname`, e.`headline`, c.`name`, e.`offline` ';
		$query .= 'FROM `'.rex::getTablePrefix().'blogger_entries` AS e ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'blogger_categories` AS c ';
		$query .= 'ON e.`category`=c.`id` ';
		$query .= 'LEFT JOIN `'.rex::getTablePrefix().'clang` AS l ';
		$query .= 'ON e.`clang`=l.`id` ';
		$query .= 'WHERE e.`aid`='.$type_id.' ';
		$query .= 'ORDER BY e.`id`';

		$list = rex_list::factory($query);

		$list->addTableAttribute('class', 'table-striped');
		$list->setNoRowsMessage('Es wurden keine Einträge gefunden');

		$thIcon = '<a href="'.$list->getUrl(['func' => 'tadd', 'parent_id' => $type_id]).'"><i class="rex-icon rex-icon-add-action"></i></a>';
		$tdIcon = '<i class="rex-icon fa-file-text-o"></i>';

		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
		$list->setColumnParams($thIcon, ['func' => 'tedit', 'id' => '###id###', 'parent_id' => $type_id]);

		$list->removeColumn('id');
		$list->setColumnLabel('aid', 'Nr.');
		$list->setColumnLabel('langname', 'Sprache');
		$list->setColumnLabel('headline', 'Titel');
		$list->setColumnFormat('headline', 'custom', function($params) {
			$list = $params['list'];

			$title = $list->getValue('headline');
			$output = substr($title, 0, 50);

			return '<span title="'.$title.'">'.$output.'...</span>';
		});

		$list->setColumnLabel('name', 'Kategorie');

		$list->setColumnLabel('offline', '');
		$list->setColumnFormat('offline', 'custom', function ($params) {
		    $list = $params['list'];

		    if ($list->getValue('e.offline') == "1") {
		    	$list->addLinkAttribute('offline', 'class', 'rex-offline');
		        return '<span class="rex-offline text-muted"><i class="rex-icon rex-icon-offline"></i>&nbsp;offline</span>';
		    } else {
		    	$list->addLinkAttribute('offline', 'class', 'rex-online');
		    	return '<span class="rex-online text-muted"><i class="rex-icon rex-icon-online"></i>&nbsp;online</span>';
		    }

		});


		$content = $list->get();

		$fragment = new rex_fragment();
		$fragment->setVar('content', $content, false);
		$content = $fragment->parse('core/page/section.php');

		echo '<h4>Übersetzungen</h4>';
		echo $content;

		// form
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


			// headline
			$field = $form->addTextField('headline');
			$field->setLabel('Headline');


			// creator
			$field = $form->addTextField('postedBy');
			$field->setLabel('Ersteller');


			// text
			$field = $form->addTextAreaField('content', null, array("class"=>"redactorEditor2-full"));
			$field->setLabel('Text');


			// preview
			$field = $form->addMediaField('preview');
			$field->setLabel('Preview Bild');


			// gallery
			$field = $form->addMediaListField('gallery');
			$field->setLabel('Gallery');


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


			// post date
			$myTime = new DateTime($form->getSql()->getValue('postedAt'));
			$field = $form->addInputField('datetime-local', 'postedAt', date('Y-m-d', $myTime->getTimestamp()).'T'.date('H:i', $myTime->getTimestamp()));
			$field->setLabel('Post Tag');


			// clang
			$field = $form->addSelectField('clang');
			$field->setLabel("Sprache");
			
			$select = $field->getSelect();
			$select->setSize(1);

			foreach (rex_clang::getAll() as $key=>$value) {
				$select->addOption($value->getName(), $value->getId());
			}


		// time update
		$form->addHiddenField('updatedAt', date('Y-m-d H:i:s'));
		$form->addHiddenField('updatedBy', rex::getUser()->getId());

		// end
		$form->addParam('id', $type_id);

		// output
		$content = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);

		$content = $fragment->parse('core/page/section.php');
		echo $content;

	} else if ($func == 'tadd') {
		/**
		 * TADD
		 */
		$formLabel = 'Neue Übersetzung';
		$form = rex_form::factory(rex::getTablePrefix().'blogger_entries', '', 'id='.$type_id);

		// parent
		$parent_sql = rex_sql::factory();
		$parent_sql->setTable(rex::getTablePrefix().'blogger_entries');
		$parent_sql->setWhere(['id' => $parent_id]);
		$parent_sql->select();

		// translation info message
		$url = 'index.php?page=blogger/entries&func=edit&id='.$parent_sql->getValue('id');
		echo '<div><p>Übersetzung von "<a href="'.$url.'">'.$parent_sql->getValue('headline').' - ['.$parent_sql->getValue('id').']</a>"</p></div>';

		// article id
		$form->addHiddenField('aid', $parent_id);

		// translation
		$form->addHiddenField('translation', 1);

		// form
			// category
			$form->addHiddenField('category', $parent_sql->getValue('category'));


			// headline
			$field = $form->addTextField('headline');
			$field->setLabel('Headline');


			// text
			$field = $form->addTextAreaField('content', null, array("class"=>"redactorEditor2-full"));
			$field->setLabel('Text');


			// preview
			$field = $form->addMediaField('preview', $parent_sql->getValue('preview'));
			$field->setLabel('Preview Bild');


			// gallery
			$field = $form->addMediaListField('gallery', $parent_sql->getValue('gallery'));
			$field->setLabel('Gallery');


			// tags
			$form->addHiddenField('tags', $parent_sql->getValue('tags'));


			// post date
			$myTime = new DateTime($parent_sql->getValue('postedAt'));
			$field = $form->addHiddenField('postedAt', date('Y-m-d', $myTime->getTimestamp()).'T'.date('H:i', $myTime->getTimestamp()));


			// clang
			$field = $form->addSelectField('clang');
			$field->setLabel("Sprache");
			
			$select = $field->getSelect();
			$select->setSize(1);

			$query = 'SELECT * FROM `'.rex::getTablePrefix().'blogger_entries` ';
			$query .= 'WHERE ';
			$query .= sprintf('(`id`=%u OR `aid`=%u) AND `id`!=%u', $parent_id, $parent_id, $type_id);

			$clang_sql = rex_sql::factory();
			$clang_sql->setQuery($query);
			$clang_sql->execute();

			$clang_entries = array();
			while ($clang_sql->hasNext()) {
				array_push($clang_entries, $clang_sql->getValue('clang'));
				$clang_sql->next();
			}

			$clang_rex = array();
			foreach (rex_clang::getAll() as $key=>$value) {
				$clang_rex[] = array('id'=>$value->getId(), 'name'=>$value->getName());
			}
			
			foreach ($clang_rex as $rkey=>$rvalue) {
				foreach ($clang_entries as $ekey=>$evalue) {
					if ($rvalue['id'] == $evalue) {
						unset($clang_rex[$rkey]);
					}
				}
			}

			foreach ($clang_rex as $rkey=>$rvalue) {
				$select->addOption($rvalue['name'], $rvalue['id']);
			}

			if (empty($clang_rex)) {
				$form->setWarning('Keine Sprache verfügbar');
			}

		// time update
		$form->addHiddenField('createdAt', date('Y-m-d H:i:s'));
		$form->addHiddenField('createdBy', rex::getUser()->getId());

		// output
		$content = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);

		$content = $fragment->parse('core/page/section.php');
		echo $content;

	} else if ($func == 'tedit') {
		/**
		 * TEDIT
		 */
		$formLabel = 'Übersetzung bearbeiten';
		$form = rex_form::factory(rex::getTablePrefix().'blogger_entries', '', 'id='.$type_id);

		// parent
		$parent_sql = rex_sql::factory();
		$parent_sql->setTable(rex::getTablePrefix().'blogger_entries');
		$parent_sql->setWhere(['id' => $parent_id]);
		$parent_sql->select();

		// translation info message
		$url = 'index.php?page=blogger/entries&func=edit&id='.$parent_sql->getValue('id');
		echo '<div><p>Übersetzung von "<a href="'.$url.'">'.$parent_sql->getValue('headline').' - ['.$parent_sql->getValue('id').']</a>"</p></div>';

		// form
			// headline
			$field = $form->addTextField('headline');
			$field->setLabel('Headline');


			// text
			$field = $form->addTextAreaField('content', null, array("class"=>"redactorEditor2-full"));
			$field->setLabel('Text');


			// preview
			$field = $form->addMediaField('preview');
			$field->setLabel('Preview Bild');


			// gallery
			$field = $form->addMediaListField('gallery');
			$field->setLabel('Gallery');


			// clang
			$field = $form->addSelectField('clang');
			$field->setLabel("Sprache");
			
			$select = $field->getSelect();
			$select->setSize(1);

			$query = 'SELECT * FROM `'.rex::getTablePrefix().'blogger_entries` ';
			$query .= 'WHERE ';
			$query .= sprintf('(`id`=%u OR `aid`=%u) AND `id`!=%u', $parent_id, $parent_id, $type_id);

			$clang_sql = rex_sql::factory();
			$clang_sql->setQuery($query);
			$clang_sql->execute();

			$clang_entries = array();
			while ($clang_sql->hasNext()) {
				array_push($clang_entries, $clang_sql->getValue('clang'));
				$clang_sql->next();
			}

			$clang_rex = array();
			foreach (rex_clang::getAll() as $key=>$value) {
				$clang_rex[] = array('id'=>$value->getId(), 'name'=>$value->getName());
			}
			
			foreach ($clang_rex as $rkey=>$rvalue) {
				foreach ($clang_entries as $ekey=>$evalue) {
					if ($rvalue['id'] == $evalue) {
						unset($clang_rex[$rkey]);
					}
				}
			}

			foreach ($clang_rex as $rkey=>$rvalue) {
				$select->addOption($rvalue['name'], $rvalue['id']);
			}

			if (empty($clang_rex)) {
				$form->setWarning('Keine Sprache verfügbar');
			}

		// time update
		$form->addHiddenField('updatedAt', date('Y-m-d H:i:s'));
		$form->addHiddenField('updatedBy', rex::getUser()->getId());

		// end
		$form->addParam('id', $type_id);

		// output
		$content = $form->get();

		$fragment = new rex_fragment();
		$fragment->setVar('class', 'edit', false);
		$fragment->setVar('title', $formLabel, false);
		$fragment->setVar('body', $content, false);

		$content = $fragment->parse('core/page/section.php');
		echo $content;

	}

?>