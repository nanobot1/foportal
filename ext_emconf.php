<?php

########################################################################
# Extension Manager/Repository config file for ext "foportal".
#
# Auto generated 04-12-2012 09:22
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Forschungsportal',
	'description' => 'Listet Informationen zu Projekten, Forschern und ihren Publikationen auf.',
	'category' => 'plugin',
	'author' => 'Dennis & Marc Lange',
	'author_email' => 'marc.lange@stud.hn.de',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_foportal/rte/',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:28:{s:9:"ChangeLog";s:4:"1886";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"3eda";s:14:"ext_tables.php";s:4:"a73a";s:14:"ext_tables.sql";s:4:"9c0f";s:15:"flexform_ds.xml";s:4:"c858";s:33:"icon_tx_foportal_fachbereiche.gif";s:4:"475a";s:43:"icon_tx_foportal_forschungsschwerpunkte.gif";s:4:"475a";s:30:"icon_tx_foportal_institute.gif";s:4:"475a";s:28:"icon_tx_foportal_profile.gif";s:4:"475a";s:29:"icon_tx_foportal_profiles.gif";s:4:"475a";s:29:"icon_tx_foportal_projects.gif";s:4:"475a";s:29:"icon_tx_foportal_projekte.gif";s:4:"475a";s:33:"icon_tx_foportal_publications.gif";s:4:"475a";s:34:"icon_tx_foportal_publikationen.gif";s:4:"475a";s:25:"icon_tx_foportal_tags.gif";s:4:"475a";s:13:"locallang.xml";s:4:"7f13";s:16:"locallang_db.xml";s:4:"78d5";s:10:"README.txt";s:4:"ee2d";s:7:"tca.php";s:4:"a3da";s:19:"doc/wizard_form.dat";s:4:"81aa";s:20:"doc/wizard_form.html";s:4:"eb47";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:29:"pi1/class.tx_foportal_pi1.php";s:4:"e170";s:37:"pi1/class.tx_foportal_pi1_wizicon.php";s:4:"3978";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"92ca";s:20:"pi1/static/setup.txt";s:4:"b81f";}',
);

?>