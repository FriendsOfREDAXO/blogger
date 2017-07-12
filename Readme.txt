"lang" folder is for .lang files
	.lang files store strings for the addon
	they are named like this:
		[language code]_[country code].lang

		example:	de_de.lang
					en_gb.lang

"lib" folder is for classes
	holdes .php files with "class{}" in it

"assets" folder is for assets like .js / .css / fonts / images etc.
	the content of this folder will be copied to the assets folder in the root directory

"pages" folder is for all pages shown in the backend
	it must contain a index.php file.
	the index.php files "requires" the file it needs with
	-> require rex_be_controller::getCurrentPageObject()->getSubPath();
	a title is added like this
	-> echo rex_view::title(rex_i18n::msg('yrewrite'));
	other pages can be made with every php code
	but redaxo has some classes to make it easier and to make not every addon look like diffrent shit and still fitting to the rest of the backend

"help.php" is shown when somebody goes to the help section of the addon

"install.sql" contains a SQL query that is executed when the addon is installed

"uninstall.sql" contains a SQL query that is executed when the addon is uninstalled

"package.yml" contains key:value pair objects with informations about the addon. The subpages have to be listed in here

"update.php" is unknown for now

"boot.php" contains php code that is executed on every page in the index.php in the root directory and in /redaxo/index.php

