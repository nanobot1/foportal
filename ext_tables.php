<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_foportal_profile');

$TCA['tx_foportal_profile'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_foportal_profile.gif',
	),
);

$TCA['tx_foportal_fachbereiche'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_fachbereiche',		
		'label'     => 'fachbereich',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY fachbereich',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_foportal_fachbereiche.gif',
	),
);

$TCA['tx_foportal_institute'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_institute',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		// 'type' => 'name',	
		'default_sortby' => 'ORDER BY name',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_foportal_institute.gif',
	),
);

$TCA['tx_foportal_forschungsschwerpunkte'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_forschungsschwerpunkte',		
		'label'     => 'forschungsschwerpunkt',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		// 'type' => 'forschungsschwerpunkt',	
		'default_sortby' => 'ORDER BY forschungsschwerpunkt',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_foportal_forschungsschwerpunkte.gif',
	),
);

$TCA['tx_foportal_projekte'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte',		
		'label'     => 'projekttitel',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_foportal_projekte.gif',
	),
);

$TCA['tx_foportal_publikationen'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen',		
		'label'     => 'titel',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_foportal_publikationen.gif',
	),
);

$TCA['tx_foportal_tags'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_tags',		
		'label'     => 'tag',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY tag',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_foportal_tags.gif',
	),
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:foportal/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY, 'pi1/static/', 'Research Portal');

//flexform feld einblenden
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';

//xml datei laden
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1','FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');


if (TYPO3_MODE === 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_foportal_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY) . 'pi1/class.tx_foportal_pi1_wizicon.php';
}
?>