<?php

########################################################################
# Extension Manager/Repository config file for ext "foportal".
#
# Auto generated 14-06-2013 09:23
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
	'_md5_values_when_last_written' => 'a:90:{s:9:"ChangeLog";s:4:"1886";s:25:"class.tx_foportal_pi1.php";s:4:"e132";s:12:"ext_icon.gif";s:4:"a3b1";s:17:"ext_localconf.php";s:4:"3eda";s:14:"ext_tables.php";s:4:"eea8";s:14:"ext_tables.sql";s:4:"d574";s:15:"flexform_ds.xml";s:4:"9a87";s:33:"icon_tx_foportal_fachbereiche.gif";s:4:"475a";s:43:"icon_tx_foportal_forschungsschwerpunkte.gif";s:4:"475a";s:30:"icon_tx_foportal_institute.gif";s:4:"475a";s:28:"icon_tx_foportal_profile.gif";s:4:"95ea";s:29:"icon_tx_foportal_profiles.gif";s:4:"475a";s:29:"icon_tx_foportal_projects.gif";s:4:"475a";s:29:"icon_tx_foportal_projekte.gif";s:4:"7276";s:33:"icon_tx_foportal_publications.gif";s:4:"af12";s:34:"icon_tx_foportal_publikationen.gif";s:4:"092d";s:25:"icon_tx_foportal_tags.gif";s:4:"475a";s:13:"locallang.xml";s:4:"7f13";s:16:"locallang_db.xml";s:4:"1f50";s:11:"new  2.html";s:4:"91c8";s:10:"new  2.txt";s:4:"ab91";s:10:"README.txt";s:4:"ee2d";s:7:"tca.php";s:4:"f104";s:19:"doc/wizard_form.dat";s:4:"81aa";s:20:"doc/wizard_form.html";s:4:"eb47";s:14:"pi1/ce_wiz.gif";s:4:"02b6";s:29:"pi1/class.tx_foportal_pi1.php";s:4:"efa3";s:37:"pi1/class.tx_foportal_pi1_wizicon.php";s:4:"3978";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"9d86";s:13:"pi1/style.css";s:4:"a3a2";s:17:"pi1/template.html";s:4:"5207";s:17:"pi1/template.tmpl";s:4:"5d17";s:28:"pi1/static/chosen-sprite.png";s:4:"25b9";s:21:"pi1/static/chosen.css";s:4:"c2cd";s:27:"pi1/static/chosen.jquery.js";s:4:"484b";s:18:"pi1/static/date.js";s:4:"97a4";s:25:"pi1/static/datePicker.css";s:4:"ad83";s:19:"pi1/static/form.css";s:4:"a278";s:18:"pi1/static/ips.txt";s:4:"2d3c";s:30:"pi1/static/jquery-1.3.2.min.js";s:4:"181f";s:23:"pi1/static/jquery-ui.js";s:4:"c913";s:31:"pi1/static/jquery.datePicker.js";s:4:"246e";s:20:"pi1/static/jquery.js";s:4:"c0ac";s:35:"pi1/static/jquery.qtip-1.0.0-rc3.js";s:4:"1cac";s:39:"pi1/static/jquery.qtip-1.0.0-rc3.min.js";s:4:"9eb4";s:32:"pi1/static/jquery.quicksilver.js";s:4:"13dd";s:34:"pi1/static/jquery.simpleFAQ-0.7.js";s:4:"e204";s:20:"pi1/static/jsFile.js";s:4:"5260";s:20:"pi1/static/setup.txt";s:4:"b81f";s:20:"pi1/static/style.css";s:4:"af0d";s:40:"pi1/static/icons/1360854892_research.png";s:4:"61de";s:29:"pi1/static/icons/calendar.png";s:4:"fc9c";s:29:"pi1/static/icons/fopologo.png";s:4:"ec83";s:26:"pi1/static/icons/minus.png";s:4:"8cf4";s:32:"pi1/static/icons/minus_hover.png";s:4:"d056";s:25:"pi1/static/icons/plus.png";s:4:"bba0";s:31:"pi1/static/icons/plus_hover.png";s:4:"6a19";s:31:"pi1/static/icons/plusButton.png";s:4:"aa8e";s:32:"pi1/static/icons/profilelogo.png";s:4:"f3bf";s:34:"pi1/static/icons/profilelogosw.png";s:4:"c187";s:32:"pi1/static/icons/projektlogo.png";s:4:"18e7";s:34:"pi1/static/icons/projektlogosw.png";s:4:"b768";s:28:"pi1/static/icons/publogo.png";s:4:"e7ce";s:30:"pi1/static/icons/publogosw.png";s:4:"6dc2";s:30:"pi1/static/icons/sucheicon.gif";s:4:"c9c6";s:26:"pi1/static/icons/Thumbs.db";s:4:"0973";s:30:"pi1/static/jFormer/jformer.css";s:4:"4a90";s:29:"pi1/static/jFormer/jFormer.js";s:4:"1be2";s:30:"pi1/static/jFormer/jformer.php";s:4:"9cec";s:39:"pi1/static/jFormer/images/button-bg.png";s:4:"b3b4";s:38:"pi1/static/jFormer/images/calendar.gif";s:4:"6a0a";s:38:"pi1/static/jFormer/images/input-bg.gif";s:4:"ccde";s:35:"pi1/static/jFormer/images/Thumbs.db";s:4:"681c";s:52:"pi1/static/jFormer/images/tip-arrow-left-blurred.png";s:4:"b008";s:44:"pi1/static/jFormer/images/tip-arrow-left.gif";s:4:"07bb";s:44:"pi1/static/jFormer/images/tip-arrow-left.png";s:4:"58fe";s:42:"pi1/static/jFormer/images/icons/accept.png";s:4:"8bfe";s:39:"pi1/static/jFormer/images/icons/add.png";s:4:"1988";s:42:"pi1/static/jFormer/images/icons/cancel.png";s:4:"757a";s:41:"pi1/static/jFormer/images/icons/error.png";s:4:"c847";s:47:"pi1/static/jFormer/images/icons/exclamation.png";s:4:"e4dd";s:40:"pi1/static/jFormer/images/icons/help.png";s:4:"c381";s:40:"pi1/static/jFormer/images/icons/lock.png";s:4:"9719";s:57:"pi1/static/jFormer/images/icons/page-navigator-active.png";s:4:"d1ce";s:68:"pi1/static/jFormer/images/icons/page-navigator-dependency-locked.png";s:4:"082d";s:57:"pi1/static/jFormer/images/icons/page-navigator-locked.png";s:4:"f517";s:59:"pi1/static/jFormer/images/icons/page-navigator-unlocked.png";s:4:"9055";s:58:"pi1/static/jFormer/images/icons/page-navigator-warning.png";s:4:"f85f";s:41:"pi1/static/jFormer/images/icons/Thumbs.db";s:4:"a3ea";}',
	'suggests' => array(
	),
);

?>