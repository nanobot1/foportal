<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_foportal_profile'] = array(
	'ctrl' => $TCA['tx_foportal_profile']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,name,personenelement,funktion,institute,fachbereich,forschungschwerpunkte,ind_forschungsschwerpunkte,mitgliedschaften,austattung,referenzprojekte,kooperationen,preise,downloads,links,zusatzinfos,tag'
	),
	'feInterface' => $TCA['tx_foportal_profile']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.name',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
/* 		'personenelement' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.personenelement',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_users',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		), */
		// PERSONEN ELEMENT statt fe_users 
		'personenelement' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.personenelement',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_txpersinfotest_persinfo',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		), 
		'funktion' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.funktion',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'institute' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.institute',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_institute',	
				'foreign_table_where' => 'AND tx_foportal_institute.pid=###STORAGE_PID### ORDER BY tx_foportal_institute.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,	
				"MM" => "tx_foportal_profile_institute_mm",	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_foportal_institute',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'list' => array(
						'type'   => 'script',
						'title'  => 'List',
						'icon'   => 'list.gif',
						'params' => array(
							'table' => 'tx_foportal_institute',
							'pid'   => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
		'fachbereich' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.fachbereich',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_fachbereiche',	
				'foreign_table_where' => 'AND tx_foportal_fachbereiche.pid=###STORAGE_PID### ORDER BY tx_foportal_fachbereiche.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,	
				"MM" => "tx_foportal_profile_fachbereich_mm",	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_foportal_fachbereiche',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'list' => array(
						'type'   => 'script',
						'title'  => 'List',
						'icon'   => 'list.gif',
						'params' => array(
							'table' => 'tx_foportal_fachbereiche',
							'pid'   => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
		'forschungschwerpunkte' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.forschungschwerpunkte',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_forschungsschwerpunkte',	
				'foreign_table_where' => 'AND tx_foportal_forschungsschwerpunkte.pid=###STORAGE_PID### ORDER BY tx_foportal_forschungsschwerpunkte.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,	
				"MM" => "tx_foportal_profile_forschungschwerpunkte_mm",
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_foportal_forschungsschwerpunkte',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'list' => array(
						'type'   => 'script',
						'title'  => 'List',
						'icon'   => 'list.gif',
						'params' => array(
							'table' => 'tx_foportal_forschungsschwerpunkte',
							'pid'   => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
		'ind_forschungsschwerpunkte' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.ind_forschungsschwerpunkte',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'mitgliedschaften' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.mitgliedschaften',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'austattung' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.austattung',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'referenzprojekte' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.referenzprojekte',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'kooperationen' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.kooperationen',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'preise' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.preise',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'downloads' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.downloads',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_foportal',
				'show_thumbs' => 1,	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'links' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.links',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'zusatzinfos' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.zusatzinfos',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'tag' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_profile.tag',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_tags',	
				'foreign_table_where' => 'AND tx_foportal_tags.pid=###STORAGE_PID### ORDER BY tx_foportal_tags.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				"MM" => "tx_foportal_profile_tag_mm",	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_foportal_tags',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'list' => array(
						'type'   => 'script',
						'title'  => 'List',
						'icon'   => 'list.gif',
						'params' => array(
							'table' => 'tx_foportal_tags',
							'pid'   => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, personenelement, funktion, institute, fachbereich, forschungschwerpunkte, ind_forschungsschwerpunkte;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], mitgliedschaften;;;richtext[]:rte_transform[mode=ts], austattung;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], referenzprojekte;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], kooperationen;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], preise;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], downloads, links;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], zusatzinfos;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], tag')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_foportal_fachbereiche'] = array(
	'ctrl' => $TCA['tx_foportal_fachbereiche']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,fachbereich,link,kennziffer'
	),
	'feInterface' => $TCA['tx_foportal_fachbereiche']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'fachbereich' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_fachbereiche.fachbereich',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
		'link' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_fachbereiche.link',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'wizards' => array(
					'_PADDING' => 2,
					'link' => array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					),
				),
			)
		),
		'kennziffer' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_fachbereiche.kennziffer',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, fachbereich, link, kennziffer')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_foportal_institute'] = array(
	'ctrl' => $TCA['tx_foportal_institute']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,name,link,short'
	),
	'feInterface' => $TCA['tx_foportal_institute']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_institute.name',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'link' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_institute.link',		
			'config' => array(
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		'short' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_institute.short',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, link, short')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_foportal_forschungsschwerpunkte'] = array(
	'ctrl' => $TCA['tx_foportal_forschungsschwerpunkte']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,forschungsschwerpunkt'
	),
	'feInterface' => $TCA['tx_foportal_forschungsschwerpunkte']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'forschungsschwerpunkt' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_forschungsschwerpunkte.forschungsschwerpunkt',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, forschungsschwerpunkt')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_foportal_projekte'] = array(
	'ctrl' => $TCA['tx_foportal_projekte']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,projekttitel,projekttyp,kurzbeschreibung,projektleiteranzeige, projektmitarbeiteranzeige,projektleiter,mitarbeiter,fachbereich,institut,jahr,projektbeginn,projektende,projektvolumen,foerdervolumen,foerdermittelgeber,forschungsschwerpunkt,webseite,downloads,tag'
	),
	'feInterface' => $TCA['tx_foportal_projekte']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'projekttitel' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projekttitel',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'projekttyp' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projekttyp',		
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projekttyp.I.0', '0'),
					array('LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projekttyp.I.1', '1'),
					array('LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projekttyp.I.2', '2'),
					array('LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projekttyp.I.3', '3'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'kurzbeschreibung' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.kurzbeschreibung',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		
		'projektleiteranzeige' => array(        
            'exclude' => 0,        
            'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projektleiteranzeige',        
            'config' => array(
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'required',
            )
        ),
		
		'projektmitarbeiteranzeige' => array(        
            'exclude' => 0,        
            'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projektmitarbeiteranzeige',        
            'config' => array(
                'type' => 'input',    
                'size' => '30',    
                
            )
        ),
		
		/*'projektleiter' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projektleiter',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_txpersinfotest_persinfo',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				"MM" => "tx_foportal_projekte_projektleiter_mm",
			)
		),
		'mitarbeiter' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.mitarbeiter',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_txpersinfotest_persinfo',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 15,	
				"MM" => "tx_foportal_projekte_mitarbeiter_mm",
			)
		),*/
		'projektleiter' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projektleiter',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_txpersinfotest_persinfo',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				
			)
		),
		'mitarbeiter' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.mitarbeiter',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_txpersinfotest_persinfo',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 15,	
				
			)
		),
		'fachbereich' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.fachbereich',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_fachbereiche',	
				'foreign_table_where' => 'AND tx_foportal_fachbereiche.pid=###STORAGE_PID### ORDER BY tx_foportal_fachbereiche.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,	
				"MM" => "tx_foportal_projekte_fachbereich_mm",
			)
		),
		'institut' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.institut',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_institute',	
				'foreign_table_where' => 'AND tx_foportal_institute.pid=###STORAGE_PID### ORDER BY tx_foportal_institute.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,	
				"MM" => "tx_foportal_projekte_institut_mm",
			)
		),
		'jahr' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.jahr',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'projektbeginn' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projektbeginn',		
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'projektende' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projektende',		
			'config' => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'projektvolumen' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.projektvolumen',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'foerdervolumen' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.foerdervolumen',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'foerdermittelgeber' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.foerdermittelgeber',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'forschungsschwerpunkt' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.forschungsschwerpunkt',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_forschungsschwerpunkte',	
				'foreign_table_where' => 'AND tx_foportal_forschungsschwerpunkte.pid=###STORAGE_PID### ORDER BY tx_foportal_forschungsschwerpunkte.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,	
				"MM" => "tx_foportal_projekte_forschungsschwerpunkt_mm",
			)
		),
		'webseite' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.webseite',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'downloads' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.downloads',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_foportal',
				'show_thumbs' => 1,	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,
			)
		),
		'tag' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_projekte.tag',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_tags',	
				'foreign_table_where' => 'AND tx_foportal_tags.pid=###STORAGE_PID### ORDER BY tx_foportal_tags.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				"MM" => "tx_foportal_projekte_tag_mm",	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_foportal_tags',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'list' => array(
						'type'   => 'script',
						'title'  => 'List',
						'icon'   => 'list.gif',
						'params' => array(
							'table' => 'tx_foportal_tags',
							'pid'   => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, projekttitel, projekttyp, kurzbeschreibung;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/],projektleiteranzeige, projektmitarbeiteranzeige, projektleiter, mitarbeiter, fachbereich, institut, jahr, projektbeginn, projektende, projektvolumen, foerdervolumen, foerdermittelgeber;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], forschungsschwerpunkt, webseite;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], downloads, tag')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_foportal_publikationen'] = array(
	'ctrl' => $TCA['tx_foportal_publikationen']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,titel,typ,autor,personenelement,beitrag_in,verlag,ort,isbn,jahr,ausgabe,seiten,link,abstract,tag'
	),
	'feInterface' => $TCA['tx_foportal_publikationen']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'titel' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.titel',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'typ' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.typ',		
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.typ.I.0', '0'),
					array('LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.typ.I.1', '1'),
					array('LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.typ.I.2', '2'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'autor' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.autor',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'personenelement' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.personenelement',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_txpersinfotest_persinfo',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		), 
		'beitrag_in' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.beitrag_in',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'verlag' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.verlag',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'ort' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.ort',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'isbn' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.isbn',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'jahr' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.jahr',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'ausgabe' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.ausgabe',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'seiten' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.seiten',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'link' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.link',		
			'config' => array(
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		'abstract' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.abstract',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'tag' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_publikationen.tag',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_foportal_tags',	
				'foreign_table_where' => 'AND tx_foportal_tags.pid=###STORAGE_PID### ORDER BY tx_foportal_tags.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 10,	
				"MM" => "tx_foportal_publikationen_tag_mm",	
				'wizards' => array(
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => array(
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => array(
							'table'    => 'tx_foportal_tags',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'list' => array(
						'type'   => 'script',
						'title'  => 'List',
						'icon'   => 'list.gif',
						'params' => array(
							'table' => 'tx_foportal_tags',
							'pid'   => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
					'edit' => array(
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, titel, typ, autor, personenelement ,beitrag_in, verlag, ort, isbn, jahr, ausgabe, seiten, link, abstract;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_foportal/rte/], tag')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_foportal_tags'] = array(
	'ctrl' => $TCA['tx_foportal_tags']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,tag'
	),
	'feInterface' => $TCA['tx_foportal_tags']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'tag' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:foportal/locallang_db.xml:tx_foportal_tags.tag',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, tag')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
?>