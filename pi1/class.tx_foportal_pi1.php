<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Dennis & Marc Lange <marc.lange@stud.hn.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once('t3lib/class.t3lib_div.php');
require_once('typo3conf/ext/foportal/pi1/static/jFormer/jformer.php');
/**
 * Plugin 'Research Portal' for the 'foportal' extension.
 *
 * @author	Dennis & Marc Lange <marc.lange@stud.hn.de>
 * @package	TYPO3
 * @subpackage	tx_foportal
 */
class tx_foportal_pi1 extends tslib_pibase
	{
		public $prefixId = 'tx_foportal_pi1'; // Same as class name
		public $scriptRelPath = 'pi1/class.tx_foportal_pi1.php'; // Path to this script relative to the extension dir.
		public $extKey = 'foportal'; // The extension key.
		//public $pi_checkCHash = TRUE;
		var $ffdata;
		var $singlepageID;
		var $showSearchForm = 0;
		var $standardTemplate = 'typo3conf/ext/foportal/pi1/template.tmpl';
		var $cssFile = 'typo3conf/ext/foportal/pi1/static/style.css';
		var $cssFile2 = 'typo3conf/ext/foportal/pi1/static/jFormer/jformer.css';
		var $cssFile3 = 'typo3conf/ext/foportal/pi1/static/form.css';
		var $cssFile4 = 'typo3conf/ext/foportal/pi1/static/chosen.css';
		var $cssFile5 = 'typo3conf/ext/foportal/pi1/static/datePicker.css';
		var $jsFile = 'typo3conf/ext/foportal/pi1/static/jsFile.js';
		var $orAnd = "";
		var $schalter = 0;
		var $aktiv = 1; //IP Sperre aktiv !
		var $filter;
		var $personTable = 'tx_dmiwpersonen_data';
		//var $personTable = 'tx_txpersinfotest_persinfo';
		/**
		 * Main method of your Plugin.
		 *
		 * @param string $content The content of the Plugin
		 * @param array $conf The Plugin Configuration
		 * @return string The content that should be displayed on the website
		 */
		public function main($content, array $conf)
			{
				$GLOBALS['TSFE']->set_no_cache(); //Cache abschalten
				$this->conf = $conf;
				$this->pi_setPiVarDefaults();
				$this->pi_loadLL();
				$this->pi_USER_INT_obj = 1; //Soll Caching deaktivieren
				$this->pi_initPIflexForm();
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<link rel="stylesheet" href="' . $this->cssFile . '" type="text/css" /> 
				<link rel="stylesheet" href="' . $this->cssFile2 . '" type="text/css" /> 
				<link rel="stylesheet" href="' . $this->cssFile3 . '" type="text/css" /> 
				<link rel="stylesheet" href="' . $this->cssFile4 . '" type="text/css" />
				<link rel="stylesheet" href="' . $this->cssFile5 . '" type="text/css" />
				<script type="text/javascript" src="' . $this->jsFile . '" language="JavaScript"></script> 
		<script type="text/javascript" src="https://www.google.com/jsapi"></script> 
		<script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/jquery.quicksilver.js"></script> <script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/jquery.simpleFAQ-0.7.js"></script> <script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/chosen.jquery.js"></script> <script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/jquery-ui.js"></script>
		<script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/date.js"></script> <script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/jquery.datePicker.js"></script> <script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/jquery.qtip-1.0.0-rc3.js"></script>  <script type="text/javascript" src="typo3conf/ext/foportal/pi1/static/jquery.qtip-1.0.0-rc3.min.js"></script> 
		';
		
				//Flexform Array laden (wird in ext_tables eingebunden)
				$this->ffdata                                         = $this->cObj->data['pi_flexform'];
				//Page ID der Einzelansicht aus Flexform holen
				$this->singlepageID                                   = $this->pi_getFFValue($this->ffdata, 'singlepid', 'sOtherSettings');
				//Soll Suchfeld angezeigt werden (kann bald raus)
				$this->showSearchForm                                 = $this->pi_getFFValue($this->ffdata, 'showsearchform', 'sDEF');
				//Ansichts Modus /Einzelansicht/Listenansicht/Nur Suche/
				$viewmode                                             = $this->pi_getFFValue($this->ffdata, 'viewtype', 'sDEF');
				// Anhand der Variable "viemode" wird die Ausgabe generiert
				switch ($viewmode)
				{
						case 'NONE':
								$content .= "Kein Anzeigemodus konfiguriert.";
								break;
						case 'LIST':
								$content .= $this->generateListView();
								break;
						case 'SINGLE':
								$content .= $this->generateSingleView();
								break;
						case 'STATISTIK':
								$content .= $this->generateStatisticView();
								break;
						case 'TAGSEARCH':
								$content .= $this->generateTagView(); 
								break;
						case 'INPUTFORM':
								$content .= $this->inputForm(); 
								break;
				}
				return $this->pi_wrapInBaseClass($content);
			}
		/**
		 * function generateSearchView
		 *
		 * Generiert Suchformular und ersetzt die 
		 * entsprechenden Marker im Template
		 */
		function generateSearchView()
			{
				//Parameter werden gelesen
				$parameter = t3lib_div::_GET($this->prefixId);
				$url       = $this->pi_getPageLink($GLOBALS['TSFE']->id); //Seitenid wird in url gespeichert
				$GLOBALS['TSFE']->fe_user->setKey('ses', 'filter_an', '0');
				$statisticpid = $this->pi_getFFValue($this->ffdata, 'statisticpid', 'sOtherSettings');
				$tagpid       = $this->pi_getFFValue($this->ffdata, 'tagpid', 'sOtherSettings');
				$inputpid     = $this->pi_getFFValue($this->ffdata, 'inputpid', 'sOtherSettings');
				$timestamp = time();
				$aktYear = date('Y', $timestamp);
				//Überprüfung ob Parameter übergeben wurde
				if ($parameter == NULL)
					{
						$all         = array(
								$this->prefixId => array(
										'mode' => 'all',
										'suche' => $this->piVars['search'],
										'name' => $this->piVars['name'],
										'fb' => $this->piVars['fb'],
										'year' => $this->piVars['year'],
										'year2' => $this->piVars['year2'],
										'fsp' => $this->piVars['fsp'],
										'inst' => $this->piVars['inst']
								)
						);
						$publication = array(
								$this->prefixId => array(
										'mode' => 'publ',
										'suche' => $this->piVars['search'],
										'name' => $this->piVars['name'],
										'fb' => $this->piVars['fb'],
										'year' => $this->piVars['year'],
										'year2' => $this->piVars['year2'],
										'fsp' => $this->piVars['fsp'],
										'inst' => $this->piVars['inst']
								)
						);
						$researcher  = array(
								$this->prefixId => array(
										'mode' => 'researcher',
										'suche' => $this->piVars['search'],
										'name' => $this->piVars['name'],
										'fb' => $this->piVars['fb'],
										'year' => $this->piVars['year'],
										'year2' => $this->piVars['year2'],
										'fsp' => $this->piVars['fsp'],
										'inst' => $this->piVars['inst']
								)
						);
						$projects    = array(
								$this->prefixId => array(
										'mode' => 'projects',
										'suche' => $this->piVars['search'],
										'name' => $this->piVars['name'],
										'fb' => $this->piVars['fb'],
										'year' => $this->piVars['year'],
										'year2' => $this->piVars['year2'],
										'fsp' => $this->piVars['fsp'],
										'inst' => $this->piVars['inst']
								)
						);
					}
				else
					{
						$all         = array(
								$this->prefixId => array(
										'mode' => 'all',
										'suche' => $parameter['suche'],
										'name' => $parameter['name'],
										'fb' => $parameter['fb'],
										'year' => $parameter['year'],
										'year2' => $parameter['year2'],
										'fsp' => $parameter['fsp'],
										'inst' => $parameter['inst']
								)
						);
						$publication = array(
								$this->prefixId => array(
										'mode' => 'publ',
										'suche' => $parameter['suche'],
										'name' => $parameter['name'],
										'fb' => $parameter['fb'],
										'year' => $parameter['year'],
										'year2' => $parameter['year2'],
										'fsp' => $parameter['fsp'],
										'inst' => $parameter['inst']
								)
						);
						$researcher  = array(
								$this->prefixId => array(
										'mode' => 'researcher',
										'suche' => $parameter['suche'],
										'name' => $parameter['name'],
										'fb' => $parameter['fb'],
										'year' => $parameter['year'],
										'year2' => $parameter['year2'],
										'fsp' => $parameter['fsp'],
										'inst' => $parameter['inst']
								)
						);
						$projects    = array(
								$this->prefixId => array(
										'mode' => 'projects',
										'suche' => $parameter['suche'],
										'name' => $parameter['name'],
										'fb' => $parameter['fb'],
										'year' => $parameter['year'],
										'year2' => $parameter['year2'],
										'fsp' => $parameter['fsp'],
										'inst' => $parameter['inst']
								)
						);
					}
				// Template laden
				if (isset($this->conf["templateFile"]))
						$template = $this->cObj->fileResource($this->conf["templateFile"]);
				else
						$template = $this->cObj->fileResource($this->standardTemplate);
				// Subpart aus Template laden
				$subpart                                      = $this->cObj->getSubpart($template, "###SEARCH_TEMPLATE###");
				// set marker replacements -- Suchformular wird erstellt
				$markerARRAY['###SEARCH_TITLE###']            = $this->pi_getLL('titel');
				$markerARRAY['###LOGO###']                    = '<img src="typo3conf/ext/foportal/pi1/static/icons/fopologo.png" width="60" height="60" style="vertical-align:middle">';
				$markerARRAY['###SEARCH_BEGIN_FORM###']       = '<form method="POST" name="form" action="' . $url . '">';         
				$markerARRAY['###SEARCH_SEARCH_NAME_DESC###'] = $this->pi_getLL('search.inname');
				$markerARRAY['###SEARCH_SEARCH_FB_DESC###']   = $this->pi_getLL('search.infb');
				$markerARRAY['###SEARCH_SEARCH_FSP_DESC###']  = $this->pi_getLL('search.infsp');
				$markerARRAY['###SEARCH_SEARCH_YEAR_DESC###'] = $this->pi_getLL('search.inyear');
				$markerARRAY['###SEARCH_SEARCH_INST_DESC###'] = $this->pi_getLL('search.ininst');
				$markerARRAY['###SEARCH_ALPHA_DESC###']       = $this->pi_getLL('search.alpha');
				foreach (range('A', 'Z') as $c)
					{
						$markerARRAY['###SEARCH_ALPHA###'] .= $this->pi_linkToPage($c, $GLOBALS['TSFE']->id, '', array(
								$this->prefixId => array(
										'mode' => 'all',
										'alpha' => $c
								)
						)) . '&nbsp;';
					}
				if ($parameter['suche'] == '')
						$markerARRAY['###SEARCH_SEARCH_FOR_FIELD###'] = '<input name="' . $this->prefixId . '[search]" type="text" class="tx_foportal-search-field-box"	size="100" placeholder="'.$this->pi_getLL('search.searchfor.placeholder').'" value="' . $this->piVars['search'] . '"/>';
				else
					{
						$markerARRAY['###SEARCH_SEARCH_FOR_FIELD###'] = '<input name="' . $this->prefixId . '[search]" type="text" class="tx_foportal-search-field-box"	size="100" placeholder="'.$this->pi_getLL('search.searchfor.placeholder').'" value="' . $parameter['suche'] . '"/>';
					}
				if ($this->piVars['search'] != '' || $parameter['suche'] != '' || $parameter['year']  != '' )
					{
						$markerARRAY['###SEARCH_SEARCH_ALL_FIELD###']          = $this->pi_linkToPage($this->pi_getLL('search.all'), $GLOBALS['TSFE']->id, '', $all);
						$markerARRAY['###SEARCH_SEARCH_RESEARCHER_FIELD###']   = $this->pi_linkToPage($this->pi_getLL('search.researcher'), $GLOBALS['TSFE']->id, '', $researcher);
						$markerARRAY['###SEARCH_SEARCH_PROJECTS_FIELD###']     = $this->pi_linkToPage($this->pi_getLL('search.projects'), $GLOBALS['TSFE']->id, '', $projects);
						$markerARRAY['###SEARCH_SEARCH_PUBLICATIONS_FIELD###'] = $this->pi_linkToPage($this->pi_getLL('search.publications'), $GLOBALS['TSFE']->id, '', $publication);
					}
				else
					{
						$markerARRAY['###SEARCH_SEARCH_ALL_FIELD###']          = '';
						$markerARRAY['###SEARCH_SEARCH_RESEARCHER_FIELD###']   = '';
						$markerARRAY['###SEARCH_SEARCH_PROJECTS_FIELD###']     = '';
						$markerARRAY['###SEARCH_SEARCH_PUBLICATIONS_FIELD###'] = '';
					}
				$markerARRAY['###SHOW_SWITCH###'] = 'style="display:none;'; //$filter
				$markerARRAY['###SHOW_FILTER###'] = '<a href="javascript:showDiv(\'tx_foportal-search-fieldcontainer1\');">' . $this->pi_getLL('search.advanced') . '</a>';
				$resultnamen                      = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,name', 'tx_foportal_profile', 'name!="" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0" AND tx_foportal_profile.hidden ="0"', '', 'name');
				$resultfb                         = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,fachbereich', 'tx_foportal_fachbereiche', 'fachbereich!="" AND tx_foportal_fachbereiche.deleted="0" AND tx_foportal_fachbereiche.hidden="0" AND tx_foportal_fachbereiche.hidden ="0"', '', 'fachbereich');
				$resultfsp                        = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,forschungsschwerpunkt', 'tx_foportal_forschungsschwerpunkte', 'forschungsschwerpunkt!="" AND tx_foportal_forschungsschwerpunkte.deleted="0" AND tx_foportal_forschungsschwerpunkte.hidden="0" AND tx_foportal_forschungsschwerpunkte.hidden ="0"', '', 'forschungsschwerpunkt');
				$resultinst                       = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,short', 'tx_foportal_institute', 'short!="" AND tx_foportal_institute.deleted="0" AND tx_foportal_institute.hidden="0" AND tx_foportal_institute.hidden ="0"', '', 'short');
				//Die Jahreszahlen zum Filtern werden aus den Projekten und Publikationen ermittelt und über "UNION" zusammengefasst.
				$sqlJahr1                         = 'SELECT DISTINCT tx_foportal_publikationen.jahr  FROM tx_foportal_publikationen';
				$sqlJahr2                         = 'SELECT DISTINCT tx_foportal_projekte.jahr  FROM tx_foportal_projekte';
				$sqlJahrAll                       = '(' . $sqlJahr1 . ') UNION (' . $sqlJahr2 . ')';
				$sqlJahrAll .= 'ORDER BY Jahr DESC';
				$resultjahr                                    = $GLOBALS['TYPO3_DB']->sql_query($sqlJahrAll);
				$markerARRAY['###SEARCH_SEARCH_NAME_FIELD###'] = '<select name="' . $this->prefixId . '[name]" >' . '<option value="">' . $this->pi_getLL('search.allname') . '</option>';
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultnamen))
					{
						$markerARRAY['###SEARCH_SEARCH_NAME_FIELD###'] .= '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
					}
				$markerARRAY['###SEARCH_SEARCH_NAME_FIELD###'] .= '</select>';
				$markerARRAY['###SEARCH_SEARCH_FB_FIELD###'] = '<select name="' . $this->prefixId . '[fb]" >' . '<option value="0">' . $this->pi_getLL('search.allname') . '</option>';
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfb))
					{
						$markerARRAY['###SEARCH_SEARCH_FB_FIELD###'] .= '<option value="' . $row['fachbereich'] . '">' . $row['fachbereich'] . '</option>';
					}
				$markerARRAY['###SEARCH_SEARCH_FB_FIELD###'] .= '</select>';
				$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD###'] = '<select name="' . $this->prefixId . '[year]" >' . '<option value="0">' . $this->pi_getLL('search.allyear') . '</option>';
				for($y = 2005; $y <= $aktYear; $y++){
					$i = 1;
					$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD###'] .= '<option value="' . $y . '">' . $y . '</option>';
					$i += 1;
				}
				
				/* while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultjahr))
					{
						$i = 1;
						$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD###'] .= '<option value="' . $row['jahr'] . '">' . $row['jahr'] . '</option>';
						$i += 1;
					} */
				$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD###'] .= '</select>';
				$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD2###'] = '<select name="' . $this->prefixId . '[year2]" >' . '<option value="0">' . $this->pi_getLL('search.allyear') . '</option>';
				for($y = 2005; $y <= $aktYear; $y++){
					$i = 1;
					$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD2###'] .= '<option value="' . $y . '">' . $y . '</option>';
					$i += 1;
				}
				$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD2###'] .= '</select>';
				$markerARRAY['###SEARCH_SEARCH_FSP_FIELD###'] = '<select name="' . $this->prefixId . '[fsp]" >' . '<option value="0">' . $this->pi_getLL('search.allname') . '</option>';
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfsp))
					{
						$markerARRAY['###SEARCH_SEARCH_FSP_FIELD###'] .= '<option value="' . $row['forschungsschwerpunkt'] . '">' . $row['forschungsschwerpunkt'] . '</option>';
					}
				$markerARRAY['###SEARCH_SEARCH_FSP_FIELD###'] .= '</select>';
				$markerARRAY['###SEARCH_SEARCH_INST_FIELD###'] = '<select name="' . $this->prefixId . '[inst]" >' . '<option value="0">' . $this->pi_getLL('search.allname') . '</option>';
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultinst))
					{
						$markerARRAY['###SEARCH_SEARCH_INST_FIELD###'] .= '<option value="' . $row['short'] . '">' . $row['short'] . '</option>';
					}
				$markerARRAY['###SEARCH_SEARCH_INST_FIELD###'] .= '</select>';
				$markerARRAY['###SEARCH_SUBMIT###']   = '<button type="submit" value="' . $this->pi_getLL('search.submit') . '"  class="tx_foportal-search-field-button">
		<img width="16" height="16" class="retina" src="http://bai-hsnr.de/typo/typo3conf/ext/foportal/pi1/static/icons/sucheicon.gif"></button>';
				$markerARRAY['###SEARCH_END_FORM###'] = '</form>';
				$markerARRAY['###STATISTIK###']       = $this->pi_linkToPage('Statistik', $statisticpid, '', '');
				 $tagparams                            = array(
						$this->prefixId => array(
								'main' => $GLOBALS['TSFE']->id
								
						)
				); 
				$inputparams                            = array(
						$this->prefixId => array(
								'main' => $GLOBALS['TSFE']->id,
								'type' => 'ausw'
						)
				);
				debug($tagpid,'tagpid');
				debug($inputpid,'input');
				$markerARRAY['###TAGSUCHE###'] .= $this->pi_linkToPage(' Tagsuche', $tagpid, '', $tagparams);
				$markerARRAY['###STATISTIK###'] .= $this->pi_linkToPage(' Eingabeformular', $inputpid, '', $inputparams);
				//subsitute
				$content .= $this->cObj->substituteMarkerArray($subpart, $markerARRAY);
				return $content;
			}
		/**
		 * function generateListView
		 *
		 * Führt Sql Anfragen anhand der Suchparameter aus.
		 * 
		 */
		function generateListView()
			{	$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
				if (isset($this->conf["templateFile"]))
						$template = $this->cObj->fileResource($this->conf["templateFile"]);
				else
						$template = $this->cObj->fileResource($this->standardTemplate);
				$subpart   = $this->cObj->getSubpart($template, "###LIST_RESULT_TEMPLATE###");
				$counter   = 0;
				$parameter = t3lib_div::_GET($this->prefixId);
				if ($this->showSearchForm == 1)
						$content .= $this->generateSearchView(); //Suchleiste wird erstellt
				//------------------------Allgemein-----------------------------------------------------------------------------		      
				// check if to show from search -- Überprüfung ob "search" in POST/GET Seitenvariablen
				if (isset($this->piVars['search']) || !empty($parameter))
					{
						$this->orAnd = '';
						if ($parameter == NULL)
							{
								$suchfeld = $this->piVars['search'];
								$namefeld = $this->piVars['name'];
								$fbfeld   = $this->piVars['fb'];
								$yearfeld = $this->piVars['year'];
								$yearfeld2 = $this->piVars['year2'];
								$fspfeld  = $this->piVars['fsp'];
								$instfeld = $this->piVars['inst'];
								debug($yearfeld,'Jahr1:');
								debug($yearfeld2,'Jahr2:');
							}
						else
							{
								$suchfeld = $parameter['suche'];
								$namefeld = $parameter['name'];
								$fbfeld   = $parameter['fb'];
								$yearfeld = $parameter['year'];
								$yearfeld2 = $parameter['year2'];
								$fspfeld  = $parameter['fsp'];
								$instfeld = $parameter['inst'];
								debug($yearfeld,'Jahr1:');
								debug($yearfeld2,'Jahr2:');
							}
						
						
						//WHERE Klausel für Suchstring wird erstellt
						if ($this->piVars['search'] != '' || $parameter['suche'] != '')
							{
								$whereclres = 'MATCH(tx_foportal_profile.name,tx_foportal_profile.ind_forschungsschwerpunkte,tx_foportal_profile.austattung,tx_foportal_profile.referenzprojekte,tx_foportal_profile.kooperationen) 
								AGAINST ("%' . $GLOBALS['TYPO3_DB']->fullQuoteStr($suchfeld, 'tx_foportal_profile') . '*%" IN BOOLEAN MODE) AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
								$whereclpub = 'MATCH(tx_foportal_publikationen.titel,tx_foportal_publikationen.abstract) 
								AGAINST ("%' . $GLOBALS['TYPO3_DB']->fullQuoteStr($suchfeld, 'tx_foportal_publikationen') . '*%" IN BOOLEAN MODE) AND tx_foportal_publikationen.deleted="0" AND tx_foportal_publikationen.hidden="0" ';
								$whereclpro .= '  MATCH(tx_foportal_projekte.projekttitel,tx_foportal_projekte.kurzbeschreibung,tx_foportal_projekte.foerdermittelgeber,tx_foportal_projekte.projektleiteranzeige) AGAINST ("%' . $GLOBALS['TYPO3_DB']->fullQuoteStr($suchfeld, 'tx_foportal_projekte') . '*%" IN BOOLEAN MODE) AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0" ';
								
								
								$this->orAnd = ' AND ';
								if (!empty($this->piVars['name']) || !empty($parameter['name']))
									{
										$whereclpub .= 'AND tx_foportal_publikationen.autor LIKE "%' . $namefeld . '%" AND tx_foportal_publikationen.deleted="0" AND tx_foportal_publikationen.hidden="0"';
										$whereclres .= 'AND tx_foportal_profile.name LIKE "%' . $namefeld . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										$whereclpro .= 'AND tx_foportal_projekte.projektleiteranzeige LIKE "%' . $namefeld . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$this->orAnd = ' AND '; //<-	hier kommt checkbox Vatiable rein
										//$this->schalter =0;	
									}
								if (!empty($this->piVars['year']) || !empty($parameter['year']))
									{
										if($yearfeld == 0){$yearfeld = 2005;}
										if($yearfeld2=='0'){$yearfeld2 = date('Y', $timestamp);}
										$whereclpro .= ' tx_foportal_projekte.jahr BETWEEN '. $yearfeld .' AND '. $yearfeld2 .' AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$whereclpub .= ' tx_foportal_publikationen.jahr BETWEEN '. $yearfeld .' AND '. $yearfeld2 .' AND tx_foportal_publikationen.deleted="0" AND tx_foportal_publikationen.hidden="0"';
										$this->orAnd = ' AND ';
										//$this->schalter =0;
									}
								if (!empty($this->piVars['fb']) || !empty($parameter['fb']))
									{
										$whereclprofb   = ' AND ' . $whereclpro . ' AND tx_foportal_fachbereiche.fachbereich LIKE "%' . $fbfeld . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$whereclresfb   = ' AND ' . $whereclres . ' AND tx_foportal_fachbereiche.fachbereich LIKE "%' . $fbfeld . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										$this->orAnd    = ' AND ';
										//$whereclres =''; /////TEST
										$researchfb     = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_profile.uid, tx_foportal_profile.name, tx_foportal_profile.institute, tx_foportal_profile.fachbereich,tx_foportal_profile.forschungschwerpunkte', 'tx_foportal_profile', 'tx_foportal_profile_fachbereich_mm', 'tx_foportal_fachbereiche', $whereclresfb, '', '', '');
										$projectsfb     = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_projekte.uid, tx_foportal_projekte.jahr, tx_foportal_projekte.projektleiteranzeige, tx_foportal_projekte.projekttitel', 'tx_foportal_projekte', 'tx_foportal_projekte_fachbereich_mm', 'tx_foportal_fachbereiche', $whereclprofb, '', '', '');
										$this->schalter = 1;
									}
								
								if (!empty($this->piVars['fsp']) || !empty($parameter['fsp']))
									{
										$whereclprofsp  = ' AND ' . $whereclpro . ' AND tx_foportal_forschungsschwerpunkte.forschungsschwerpunkt LIKE "%' . $fspfeld . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$whereclresfsp  = ' AND ' . $whereclres . ' AND tx_foportal_forschungsschwerpunkte.forschungsschwerpunkt LIKE "%' . $fspfeld . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										$this->orAnd    = ' AND ';
										$researchfsp    = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_profile.uid, tx_foportal_profile.name, tx_foportal_profile.institute, tx_foportal_profile.fachbereich,forschungsschwerpunkt', 'tx_foportal_profile', 'tx_foportal_profile_forschungschwerpunkte_mm', 'tx_foportal_forschungsschwerpunkte', $whereclresfsp, '', '', '');
										$projectsfsp    = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_projekte.uid, tx_foportal_projekte.jahr, tx_foportal_projekte.projektleiteranzeige, tx_foportal_projekte.projekttitel', 'tx_foportal_projekte', 'tx_foportal_projekte_forschungsschwerpunkt_mm', 'tx_foportal_forschungsschwerpunkte', $whereclprofsp, '', '', '');
										$this->schalter = 1;
									}
								if (!empty($this->piVars['inst']) || !empty($parameter['inst']))
									{
										$whereclproinst = ' AND ' . $whereclpro . ' AND tx_foportal_institute.short LIKE "%' . $instfeld . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$whereclresinst = ' AND ' . $whereclres . ' AND tx_foportal_institute.short LIKE "%' . $instfeld . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										$this->orAnd    = ' AND ';
										$researchinst   = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_profile.uid,tx_foportal_institute.uid AS \'iuid\',tx_foportal_profile.name,short', 'tx_foportal_profile', 'tx_foportal_profile_institute_mm', 'tx_foportal_institute', $whereclresinst, '', '', '');
										$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = 1;
										$projectsinst   = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_projekte.uid,tx_foportal_institute.uid AS \'iuid\',tx_foportal_institute.short,projekttitel,jahr', 'tx_foportal_projekte', 'tx_foportal_projekte_institut_mm', 'tx_foportal_institute', $whereclproinst, '', '', '');
										$this->schalter = 1;
										//debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery,'Suchfeld + Instfilter:');
										
									}
								if ($this->schalter != 1)
									{
										if (!empty($whereclres))
												$research = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_profile', $whereclres);
										if (!empty($whereclpub))
												$publ = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_publikationen ', $whereclpub);
										if (!empty($whereclpro))
												$projects = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_projekte ', $whereclpro);
										$this->schalter = 0;
									}
							}
						elseif ($this->piVars['search'] == '' && $parameter['suche'] == '')
							{
								if (!empty($this->piVars['alpha']))
									{
										$whereclpub = 'tx_foportal_publikationen.autor LIKE "' . $this->piVars['alpha'] . '%" AND tx_foportal_publikationen.deleted="0" AND tx_foportal_publikationen.hidden="0"';
										$whereclres = 'tx_foportal_profile.name LIKE "' . $this->piVars['alpha'] . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										$whereclpro = 'tx_foportal_projekte.projektleiteranzeige LIKE "' . $this->piVars['alpha'] . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
									}
								if (!empty($this->piVars['name']))
									{
										$whereclpub  = 'tx_foportal_publikationen.autor LIKE "%' . $this->piVars['name'] . '%" AND tx_foportal_publikationen.deleted="0" AND hidden="0"';
										$whereclres  = 'tx_foportal_profile.name LIKE "%' . $this->piVars['name'] . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										//Projektleiter Name in Projekt einfügen (String)
										$this->orAnd = ' AND '; //<-	hier kommt checkbox Vatiable rein	
									}
								if (!empty($this->piVars['year']) || !empty($parameter['year']))
									{
										if($yearfeld == 0){$yearfeld = 2005;}
										if($yearfeld2 == '0'){$yearfeld2 = date('Y', $timestamp);}
										$whereclpro .= ' tx_foportal_projekte.jahr BETWEEN '. $yearfeld .' AND '. $yearfeld2 .' AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$whereclpub .= ' tx_foportal_publikationen.jahr BETWEEN '. $yearfeld .' AND '. $yearfeld2 .' AND tx_foportal_publikationen.deleted="0" AND tx_foportal_publikationen.hidden="0"';
										$this->orAnd = ' AND ';
									}
								if (!empty($this->piVars['fb']) || !empty($parameter['fb']))
									{
										$whereclprofb = ' AND tx_foportal_fachbereiche.fachbereich LIKE "%' . $this->piVars['fb'] . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0" ';
										$whereclresfb = ' AND tx_foportal_fachbereiche.fachbereich LIKE "%' . $this->piVars['fb'] . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0" ';
										if (!empty($whereclres))
												$whereclresfb = ' AND ' . $whereclres . ' AND tx_foportal_fachbereiche.fachbereich LIKE "%' . $this->piVars['fb'] . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0" ';
										if (!empty($whereclpro))
										$whereclresfb =' AND ' . $whereclpro . ' AND tx_foportal_fachbereiche.fachbereich LIKE "%' . $this->piVars['fb'] . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0" ';
										$this->orAnd    = ' AND ';
										$researchfb     = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_profile.uid, tx_foportal_profile.name, tx_foportal_profile.institute, tx_foportal_profile.fachbereich,forschungschwerpunkte', 'tx_foportal_profile', 'tx_foportal_profile_fachbereich_mm', 'tx_foportal_fachbereiche', $whereclresfb, '', '', '');
										//debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery,'Fachbereich-Query:');	
										$projectsfb     = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_projekte.uid, tx_foportal_projekte.jahr, tx_foportal_projekte.projektleiteranzeige, tx_foportal_projekte.projekttitel', 'tx_foportal_projekte', 'tx_foportal_projekte_fachbereich_mm', 'tx_foportal_fachbereiche', $whereclprofb, '', '', '');
										$this->schalter = 1;
										
									}
								
								if (!empty($this->piVars['fsp']) || !empty($parameter['fsp']))
									{
										$whereclprofsp = 'AND tx_foportal_forschungsschwerpunkte.forschungsschwerpunkt LIKE "%' . $this->piVars['fsp'] . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$whereclresfsp = 'AND tx_foportal_forschungsschwerpunkte.forschungsschwerpunkt LIKE "%' . $this->piVars['fsp'] . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										if (!empty($whereclpro))
												$whereclprofsp = ' AND ' . $whereclpro . 'AND tx_foportal_forschungsschwerpunkte.forschungsschwerpunkt LIKE "%' . $this->piVars['fsp'] . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										if (!empty($whereclres))
												$whereclresfsp = ' AND ' . $whereclres . 'AND tx_foportal_forschungsschwerpunkte.forschungsschwerpunkt LIKE "%' . $this->piVars['fsp'] . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										$this->orAnd    = ' AND ';
										$researchfsp    = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_profile.uid, tx_foportal_profile.name, tx_foportal_profile.institute, tx_foportal_profile.fachbereich,forschungsschwerpunkt', 'tx_foportal_profile', 'tx_foportal_profile_forschungschwerpunkte_mm', 'tx_foportal_forschungsschwerpunkte', $whereclresfsp, '', '', '');
										$projectsfsp    = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_projekte.uid, tx_foportal_projekte.jahr, tx_foportal_projekte.projektleiteranzeige, tx_foportal_projekte.projekttitel', 'tx_foportal_projekte', 'tx_foportal_projekte_forschungsschwerpunkt_mm', 'tx_foportal_forschungsschwerpunkte', $whereclprofsp, '', '', '');
										$this->schalter = 1;
									}
								if (!empty($this->piVars['inst']) || !empty($parameter['inst']))
									{
										$whereclproinst = ' AND tx_foportal_institute.short LIKE "%' . $instfeld . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										$whereclresinst = ' AND tx_foportal_institute.short LIKE "%' . $instfeld . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										if (!empty($whereclpro))
												$whereclproinst = ' AND ' . $whereclpro . ' AND tx_foportal_institute.short LIKE "%' . $instfeld . '%" AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0"';
										if (!empty($whereclres))
												$whereclresinst = ' AND ' . $whereclres . ' AND tx_foportal_institute.short LIKE "%' . $instfeld . '%" AND tx_foportal_profile.deleted="0" AND tx_foportal_profile.hidden="0"';
										$this->orAnd    = ' AND ';
										$researchinst   = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_profile.uid,tx_foportal_profile.name,short', 'tx_foportal_profile', 'tx_foportal_profile_institute_mm', 'tx_foportal_institute', $whereclresinst, '', '', '');
										$projectsinst   = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('tx_foportal_projekte.uid,short,projekttitel,jahr', 'tx_foportal_projekte', 'tx_foportal_projekte_institut_mm', 'tx_foportal_institute', $whereclproinst, '', '', '');
										$this->schalter = 1;
									}
								if ($this->schalter != 1)
									{
										if (!empty($whereclres))
												$research = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_profile', $whereclres);
										if (!empty($whereclpub))
												$publ = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_publikationen ', $whereclpub);
										if (!empty($whereclpro))
												$projects = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_projekte ', $whereclpro);
										$this->schalter = 0;
									}
								//return $content;
							}
						//Engültige Abfrage (Noch ohne Kategorie nur auf Publikationen)
						if (!empty($whereclres))
							{
								//$research = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_profile',$whereclres);
							}
						if (!empty($whereclpub))
							{
								//$publ = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_publikationen ',$whereclpub);
							}
						if (!empty($whereclpro))
							{
								//$projects = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_projekte ',$whereclpro);
							}
						//--------------------------------------------------Gilt für alle Modi---------------------------------------------------------------------------------------------------			
						$counterpub += $GLOBALS['TYPO3_DB']->sql_num_rows($publ);
						$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($research);
						$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($researchfb);
						$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($researchfsp);
						$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($researchinst);
						$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projects);
						$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projectsfb);
						$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projectsfsp);
						$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projectsinst);
						$counter = $counterpub + $counterres + $counterpro;
						if ($counter == 0)
							{
								$content .= $this->pi_getLL('list.noentries');
							}
						
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($research) > 0 && ($parameter['mode'] == 'researcher' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($research))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($researchfb) > 0 && ($parameter['mode'] == 'researcher' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($researchfb))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($researchfsp) > 0 && ($parameter['mode'] == 'researcher' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($researchfsp))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($researchinst) > 0 && ($parameter['mode'] == 'researcher' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($researchinst))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($projects) > 0 && ($parameter['mode'] == 'projects' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projects))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($projectsfb) > 0 && ($parameter['mode'] == 'projects' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projectsfb))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($projectsfsp) > 0 && ($parameter['mode'] == 'projects' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projectsfsp))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($projectsinst) > 0 && ($parameter['mode'] == 'projects' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projectsinst))
										$entry[] = $row;
							}
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($publ) > 0 && ($parameter['mode'] == 'publ' || $parameter['mode'] == 'all' || $parameter['mode'] == ''))
							{
								while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($publ))
										$entry[] = $row;
							}
						if ($counter > 0)
							{
								$markerARRAY['###LIST_RESULT_PUB###'] = $counterpub . ' ' . $this->pi_getLL('list.pubentries');
								$markerARRAY['###LIST_RESULT_RES###'] = $counterres . ' ' . $this->pi_getLL('list.resentries');
								$markerARRAY['###LIST_RESULT_PRO###'] = $counterpro . ' ' . $this->pi_getLL('list.proentries');
								$markerARRAY['###LIST_RESULT_FOR###'] = '';
								if ($this->piVars['search'] != '' || $parameter['suche'] != '')
										$markerARRAY['###LIST_RESULT_FOR###'] = $this->pi_getLL('list.searchFor') . ' "' . $suchfeld . '" ' . $this->pi_getLL('list.searchFor2') . ' <br />';
								if (!empty($this->piVars['alpha']))
									{
										$markerARRAY['###LIST_RESULT_FOR###'] = $this->pi_getLL('list.searchForIndex') . ' "' . $this->piVars['alpha'] . '" ' . $this->pi_getLL('list.searchFor2') . ' <br />';
									}
							}
						else
							{
								$markerARRAY['###LIST_RESULT_PUB###'] = '';
								$markerARRAY['###LIST_RESULT_RES###'] = '';
								$markerARRAY['###LIST_RESULT_PRO###'] = '';
								$markerARRAY['###LIST_RESULT_FOR###'] = '';
							}
						$content .= $this->cObj->substituteMarkerArray($subpart, $markerARRAY);
						//Array mit Publikationen werden an Funktion showList übergeben. Ergebnis von showList wird an content gehängt
						if ($counter > 0)
								$content .= $this->showList($entry);
					}
				
				
				return ($content);
			}
		//return($content);
		/**
		 * function showList
		 *
		 * Stellt die ermittelten Einträge aus der DB
		 * dar
		 * @param  array $entry Ein Eintrag (Publikation, Profil oder Projekt)
		 * aus der Datenbank
		 */
		function showList($entry)
			{	debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);	
				// Einzelansicht Seite aus Flexform holen
				$singlepid = $this->pi_getFFValue($this->ffdata, 'singlepid', 'sOtherSettings'); //Angabe der Seite für die Einzelansich aus dem Flexform
				// Template laden
				if (isset($this->conf["templateFile"]))
						$template = $this->cObj->fileResource($this->conf["templateFile"]);
				else
						$template = $this->cObj->fileResource($this->standardTemplate);
				// Subparts aus Template holen
				$subpart         = $this->cObj->getSubpart($template, "###LIST_ITEM_TEMPLATE###");
				$subpart_browser = $this->cObj->getSubpart($template, "###LIST_BROWSE_TEMPLATE###");
				$pagebrowser     = '';
				$start           = 0;
				//maximale Anzahl der Einträge aus Flexform holen
				$max             = $this->pi_getFFValue($this->ffdata, 'resultnum', 'sDEF');
				$end             = $max;
				if ($end > sizeof($entry))
						$end = sizeof($entry); //Falls es in einer Zeile, Falls es weniger Publikationen als Max mögliche Seiteneinträge gibt
				if (!isset($this->piVars['pnum']))
						$this->piVars['pnum'] = 1; //Seitennummer wird auf 1 gestzt falls noch keine gesetzt
				if (sizeof($entry) > $max && $max > 0) //Falls Anzahl der Publikationen größer max Seiteneinträg & Max Seiteneinträge größer 0
					{
						$next = $this->piVars['pnum'] + 1; //Nächste Seitennummer +1
						$prev = $this->piVars['pnum'] - 1; //Vorherige Seitennummer -1
						$last = sizeof($entry) / $max; //Letzte Seite = Anz. Publikationen / max Seiteneinträge
						if (isset($this->piVars['pnum']) && $this->piVars['pnum'] > 1) //Falls Seitenummer gesetzt und Seitennummer größer 1
								$pagebrowser .= $this->pi_linkTP_keepPIvars('<<', array(
										'pnum' => $prev
								)) . '&nbsp;'; // erstellt einen Link "<<" zurück. erhält aber die Page Variablen             
						for ($i = 0; $i <= $last; $i++) //bis zur letzen Seite
							{
								$c = $i + 1;
								if ($this->piVars['pnum'] == $c || (!isset($this->piVars['pnum']) && $c == 1)) //Falls auf aktueller Seiter oder nicht auf Seite 1
										$link = $c; //Wird der Link für die aktuelle Seite der Inhalt von c
								else
										$link = $this->pi_linkTP_keepPIvars($c, array(
												'pnum' => $c
										)); //Ansonsten wird ein neuer Link mit der entsprechenden Nummer (2,3,4,...) angelegt
								$pagebrowser .= $link . ' ';
							}
						if ($this->piVars['pnum'] < $last)
								$pagebrowser .= '&nbsp;' . $this->pi_linkTP_keepPIvars('>>', array(
										'pnum' => $next
								)); //Link auf letzte Seite "<<" wird angelegt               
					}
				// Page Browser an Marker (oben) anlegen
				$bmarkerARRAY['###LIST_PAGEBROWSER###'] = $pagebrowser;
				$content .= $this->cObj->substituteMarkerArray($subpart_browser, $bmarkerARRAY); //Pagebwoser wird an seiner Marke abgelegt
				if (isset($this->piVars['pnum']))
					{
						$start = $max * ($this->piVars['pnum'] - 1);
						$end   = $max * $this->piVars['pnum'];
						if ($end > sizeof($entry))
								$end = sizeof($entry);
					}
				// --------------------------- Eintragsliste rendern ------------------------------------
				if (sizeof($entry) == 0)
						$content .= '<p>' . $this->pi_getLL('list.noentries') . '</p>'; //Falls keine Einträge existieren wird no entries ausgegeben             
				for ($i = $start; $i < $end; $i++) //Für alle Einträge
					{
						$row         = $entry[$i];
						$p_title     = '';
						$p_author    = '';
						$p_file      = '';
						$p_more      = '';
						$p_order     = '';
						$num_year    = '';
						$p_year      = '';
						$p_publisher = '';
						$p_subtitle  = '';
						$p_location  = '';
						if ($row['titel'] != '')
							{
								$params                          = array(
										$this->prefixId => array(
												'entryid' => $row['uid'],
												ppid => $GLOBALS['TSFE']->id,
												'type' => 'pub'
										)
								);
								$markerARRAY['###LIST_ICON###']  = '<img src="typo3conf/ext/foportal/pi1/static/icons/publogosw.png" width="36" height="36" >';
								$markerARRAY['###LIST_TITLE###'] = $this->pi_linkToPage($row['titel'], $singlepid, '', $params);
								$markerARRAY['###LIST_SUBTITLE###'] = $this->pi_getLL('SINGLE_AUTHOR_DESC') . ' ' . $row['autor'] . '</br>';
								$markerARRAY['###LIST_MORE###']     = $p_more;
								$markerARRAY['###LIST_YEAR###']     = ' ' . $this->pi_getLL('SINGLE_YEAR_DESC') . ' ' . $row['jahr'];
							}
						if ($row['projekttitel'] != '')
							{
								$params                             = array(
										$this->prefixId => array(
												'entryid' => $row['uid'], //MARKE
												ppid => $GLOBALS['TSFE']->id,
												'type' => 'proj'
										)
								);
								//debug($row,'Row Proj:');
								$markerARRAY['###LIST_ICON###']     = '<img src="typo3conf/ext/foportal/pi1/static/icons/projektlogosw.png" width="30" height="30" >';
								$markerARRAY['###LIST_TITLE###']    = $this->pi_linkToPage($row['projekttitel'], $singlepid, '', $params);
								$markerARRAY['###LIST_YEAR###']     = '' . $this->pi_getLL('SINGLE_YEAR_DESC') . ' ' . $row['jahr'];
								$markerARRAY['###LIST_SUBTITLE###'] = $this->pi_getLL('SINGLE_PROJECT_MANAGER_DESC') . ' ' . $row['projektleiteranzeige'] . '</br>';
							}
						if ($row['name'] != '')
							{
								$fsp                                = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_profile', 'tx_foportal_profile_forschungschwerpunkte_mm', 'tx_foportal_forschungsschwerpunkte', 'AND tx_foportal_profile_forschungschwerpunkte_mm.uid_local = ' . $row['uid'] . ' ', '', '', '');
								$fb                                 = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_profile', 'tx_foportal_profile_fachbereich_mm', 'tx_foportal_fachbereiche', 'AND tx_foportal_profile_fachbereich_mm.uid_local = ' . $row['uid'] . ' ', '', '', '');
								$markerARRAY['###LIST_SUBTITLE###'] = $this->pi_getLL('SINGLE_PROFILE_EMPHASIS_DESC') . ' ';
								while ($erg = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($fsp))
									{
										$markerARRAY['###LIST_SUBTITLE###'] .= $erg['forschungsschwerpunkt'] . '</br> ';
									}
								$params                          = array(
										$this->prefixId => array(
												'entryid' => $row['uid'],
												ppid => $GLOBALS['TSFE']->id,
												'type' => 'prof'
										)
								);
								$markerARRAY['###LIST_ICON###']  = '<img src="typo3conf/ext/foportal/pi1/static/icons/profilelogosw.png" width="30" height="30" >';
								$markerARRAY['###LIST_TITLE###'] = $this->pi_linkToPage($row['name'], $singlepid, '', $params);
								$fberg                           = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($fb);
								//$markerARRAY['###LIST_MORE###']=$p_more;
								$markerARRAY['###LIST_YEAR###']  = $this->pi_getLL('SINGLE_PROJECT_FACULTY_DESC') . ' ' . $fberg['fachbereich'];
							}
						//$params = array( $this->prefixId => array( 'entryid' => $row['uid'], ppid => $GLOBALS['TSFE']->id)); //Hier Link mit Marker für Typ Versehen?!
						$p_more                         = $this->pi_linkToPage($this->pi_getLL('more'), $singlepid, '', $params);
						$markerARRAY['###LIST_MORE###'] = $p_more;
						$content .= $this->cObj->substituteMarkerArray($subpart, $markerARRAY);
					}
				// show page browser on the bottom of list
				$content .= $this->cObj->substituteMarkerArray($subpart_browser, $bmarkerARRAY); //Pagebrowser wird unten nocheinmal angezeigt
				return ($content);
			}
		/**
		 * generateSingleView
		 *
		 * Generiert eine Einzelansicht eines bestimmten 
		 * Eintrags
		 */
		function generateSingleView()
			{
				$params     = t3lib_div::_GET($this->prefixId); //  Ermittelt Parameter Aus GET Werten
				$entryid    = $params['entryid']; //wird von der Listenansicht übergeben
				$type       = $params['type'];
				$returnlink = $this->pi_linkToPage('<< ' . $this->pi_getLL('back'), 74);
				$singlepid2 = $this->pi_getFFValue($this->ffdata, 'singlepid', 'sOtherSettings');
				if (isset($this->conf["templateFile"]))
						$template = $this->cObj->fileResource($this->conf["templateFile"]);
				else
						$template = $this->cObj->fileResource($this->standardTemplate);
				if ($type == 'pub')
					{
						$result                                     = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_publikationen', 'uid=' . $entryid);
						$pub                                        = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
						$autorHN = explode(',',$pub['personenelement']);
						debug($autorHN,'autorHN');
						// Subpart aus Template laden
						$subpart                                    = $this->cObj->getSubpart($template, "###SINGLE_TEMPLATE###");
						// Ersetzungen anlegen
						$markerARRAY['###SINGLE_TITLE###']          = $pub['titel']; //TEST
						$markerARRAY['###SINGLE_HEAD_PUB###']       = $this->pi_getLL('SINGLE_HEAD_PUB');
						$markerARRAY['###SINGLE_HEAD_PUB_NAME###']  = $pub['titel'];
						$markerARRAY['###SINGLE_AUTHOR_DESC###']    = $this->pi_getLL('SINGLE_AUTHOR_DESC');
						$markerARRAY['###SINGLE_TITLE_DESC###']     = $this->pi_getLL('SINGLE_TITLE_DESC');
						$markerARRAY['###SINGLE_PUBLISHER_DESC###'] = $this->pi_getLL('SINGLE_PUBLISHER_DESC');
						$markerARRAY['###SINGLE_PLACE_DESC###']     = $this->pi_getLL('SINGLE_PLACE_DESC');
						$markerARRAY['###SINGLE_YEAR_DESC###']      = $this->pi_getLL('SINGLE_YEAR_DESC');
						$markerARRAY['###SINGLE_COMMENT_DESC###']      = $this->pi_getLL('SINGLE_COMMENT_DESC');
						$markerARRAY['###SINGLE_PAGE_DESC###']      = $this->pi_getLL('SINGLE_PAGE_DESC');
						$markerARRAY['###SINGLE_EDITION_DESC###']      = $this->pi_getLL('SINGLE_EDITION_DESC');
						$markerARRAY['###SINGLE_LINK_DESC###']      = $this->pi_getLL('SINGLE_LINK_DESC');
						$markerARRAY['###SINGLE_ISBN_DESC###']      = $this->pi_getLL('SINGLE_ISBN_DESC');
						$markerARRAY['###SINGLE_ABSTRACT_DESC###']  = $this->pi_getLL('SINGLE_ABSTRACT_DESC');
						$markerARRAY['###SINGLE_INFO_ALLG###']      = $this->pi_getLL('SINGLE_INFO_ALLG');
						$markerARRAY['###SINGLE_INFO_WEITER###']    = $this->pi_getLL('SINGLE_INFO_WEITER');
						$markerARRAY['###SINGLE_AUTHOR_HN_DESC###']	= $this->pi_getLL('tx_foportal_publikationen.autor_hn');
						$markerARRAY['###SINGLE_AUTHOR###']         = $pub['autor'];
						$markerARRAY['###SINGLE_PAGE###']         = $pub['seiten'];
						$markerARRAY['###SINGLE_COMMENT###']         = $pub['beitrag_in'];
						$markerARRAY['###SINGLE_EDITION###']         = $pub['ausgabe'];
						$markerARRAY['###SINGLE_LINK###']         = '<a href="http://' . $pub['link'] . '" >Link</a>';
						$markerARRAY['###SINGLE_PUBLISHER###']      = $pub['verlag'];
						$markerARRAY['###SINGLE_PLACE###']          = $pub['ort'];
						$markerARRAY['###SINGLE_YEAR###']           = $pub['jahr'];
						$markerARRAY['###SINGLE_ISBN###']           = $pub['isbn'];
						$markerARRAY['###SINGLE_ABSTRACT###']       = $pub['abstract'];
						//$markerARRAY['###SINGLE_AUTHOR_HN###']		.= foreach($autorHN as $a){$this->showPersonElement($a);}
						
						foreach($autorHN as $a){
							$markerARRAY['###SINGLE_AUTHOR_HN###']		.= $this->showPersonElement($a);
						}
						//$markerARRAY['###SINGLE_ABSTRACT###']       = $pub['tag'];
						
						$markerARRAY['###SINGLE_RETURNLINK###']     = $returnlink;
						if ($pub['verlag'] == '')
							{
								$markerARRAY['###SINGLE_PUBLISHER_DESC###'] = '';
							}
						if ($pub['jahr'] == '')
							{
								$markerARRAY['###SINGLE_YEAR_DESC###'] = '';
							}
						if ($pub['titel'] == '')
							{
								$markerARRAY['###SINGLE_TITLE_DESC###'] = '';
							}
						if ($pub['autor'] == '')
							{
								$markerARRAY['###SINGLE_AUTHOR_DESC###'] = '';
							}
						if ($pub['ort'] == '')
							{
								$markerARRAY['###SINGLE_PLACE_DESC###'] = '';
							}
						if ($pub['seiten'] == '')
							{
								$markerARRAY['###SINGLE_PAGE_DESC###'] = '';
							}
						if ($pub['beitrag_in'] == '')
							{
								$markerARRAY['###SINGLE_COMMENT_DESC###'] = '';
							}
						if ($pub['ausgabe'] == '')
							{
								$markerARRAY['###SINGLE_EDITION_DESC###'] = '';
							}
						if ($pub['link'] == '')
							{
								$markerARRAY['###SINGLE_LINK_DESC###'] = '';
							}
						if ($pub['isbn'] == '')
							{
								$markerARRAY['###SINGLE_ISBN_DESC###'] = '';
							}
						if ($pub['abstract'] == '')
							{
								$markerARRAY['###SINGLE_ABSTRACT_DESC###'] = '';
							}
						if ($this->aktiv == 0 || ($this->aktiv == 1 && $this->pruf_IP($_SERVER['REMOTE_ADDR'], $pub['uid'], 'pub') == 0))
								$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_foportal_publikationen SET klicks = klicks +1 WHERE uid =' . $entryid . '');
					}
				if ($type == 'prof')
					{
						$j      = 0;
						$k      = 0;
						$l      = 0;
						$m		= 0;
						$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_profile', 'uid=' . $entryid);
						$prof   = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
						$fsp    = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_profile', 'tx_foportal_profile_forschungschwerpunkte_mm', 'tx_foportal_forschungsschwerpunkte', 'AND tx_foportal_profile_forschungschwerpunkte_mm.uid_local = ' . $prof['uid'] . ' ', '', '', '');
						$fb     = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_profile', 'tx_foportal_profile_fachbereich_mm', 'tx_foportal_fachbereiche', 'AND tx_foportal_profile_fachbereich_mm.uid_local = ' . $prof['uid'] . ' ', '', '', '');
						$inst   = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_profile', 'tx_foportal_profile_institute_mm', 'tx_foportal_institute', 'AND tx_foportal_profile_institute_mm.uid_local = ' . $prof['uid'] . ' ', '', '', '');
						//$whererefpro .= '  MATCH(tx_foportal_projekte.projektleiteranzeige) AGAINST ("%' . $GLOBALS['TYPO3_DB']->fullQuoteStr($prof['name'], 'tx_foportal_projekte') . '*%" IN BOOLEAN MODE) AND deleted="0" AND hidden="0" ';
						$nameOProf = preg_replace("/(Prof.|Dr.|Ing.|Dr.-Ing|Dipl.|Des.|Math.|Dipl.-Wirt.Ing.|Dipl.-Ing|Dipl.-Psych.|Dr.med.|oec.troph.|rer.|nat.|habil.)/i","",$prof['name']);
						
						$whererefpro .= '  MATCH(tx_foportal_projekte.projektleiteranzeige) AGAINST (\'"' . $nameOProf . '"\' IN BOOLEAN MODE) AND tx_foportal_projekte.deleted="0" AND tx_foportal_projekte.hidden="0" ';
						$refpro	= $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_projekte', $whererefpro);
						debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery,'Projektliste Statement:');
								
						 
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($fsp))
							{
								$markerARRAY['###SINGLE_PROFILE_EMPHASIS###'] .= $row['forschungsschwerpunkt'] . ' </br>';
								$j++;
							}
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($fb))
							{
								$markerARRAY['###SINGLE_PROFILE_FACULTY###'] .= $row['fachbereich'] . ' </br>'; //LINK! über a href $row['link']
								$k++;
							}
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($inst))
							{
								$markerARRAY['###SINGLE_PROFILE_INSTITUTE###'] .= $row['short'] . ' </br>'; //LINK! über a href $row['link']
								$l++;
							}
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($refpro))
							{
								$paramspro                             = array(
										$this->prefixId => array(
												'entryid' => $row['uid'], //MARKE
												ppid => $GLOBALS['TSFE']->id,
												'type' => 'proj'
										)
								);
								$markerARRAY['###SINGLE_PROFILE_PROJECTS###'] .= $this->pi_linkToPage($row['projekttitel'], $singlepid2, '', $paramspro).'<br>' ; //LINK! über a href $row['link']
								$m++;
							}
							//debug($this->getPersonElement($prof['personenelement']),'perselement');
						$subpart                                                      = $this->cObj->getSubpart($template, "###SINGLE_TEMPLATE_PROF###");
						// Ersetzungen anlegen
						$markerARRAY['###SINGLE_PROFILE_HEAD###']                     = $this->pi_getLL('SINGLE_PROFILE_HEAD');
						if(empty($prof['personenelement'])){$markerARRAY['###SINGLE_PROFILE_PELEMENT###']				  = '';}
						else{$markerARRAY['###SINGLE_PROFILE_PELEMENT###']				= $this->showPersonElement($prof['personenelement']);}
						//$markerARRAY['###SINGLE_PROFILE_PELEMENT###']				  = $this->showPersonElement($prof['personenelement']);
						$markerARRAY['###SINGLE_PROFILE_INFO_ALG###']                 = $this->pi_getLL('SINGLE_PROFILE_INFO_ALG');
						$markerARRAY['###SINGLE_PROFILE_NAME_DESC###']                = $this->pi_getLL('SINGLE_PROFILE_NAME_DESC');
						$markerARRAY['###SINGLE_PROFILE_FUNKTION_DESC###']            = $this->pi_getLL('SINGLE_PROFILE_FUNKTION_DESC');
						$markerARRAY['###SINGLE_PROFILE_FACULTY_DESC###']             = $this->pi_getLL('SINGLE_PROFILE_FACULTY_DESC');
						$markerARRAY['###SINGLE_PROFILE_INSTITUTE_DESC###']           = $this->pi_getLL('SINGLE_PROFILE_INSTITUTE_DESC');
						$markerARRAY['###SINGLE_PROFILE_EMPHASIS_DESC###']            = $this->pi_getLL('SINGLE_PROFILE_EMPHASIS_DESC');
						$markerARRAY['###SINGLE_PROFILE_INDIVIDUAL_EMPHASIS_DESC###'] = $this->pi_getLL('SINGLE_PROFILE_INDIVIDUAL_EMPHASIS_DESC');
						$markerARRAY['###SINGLE_PROFILE_MEMBERSHIPS_DESC###']         = $this->pi_getLL('SINGLE_PROFILE_MEMBERSHIPS_DESC');
						$markerARRAY['###SINGLE_PROFILE_EQUIPTMENT_DESC###']          = $this->pi_getLL('SINGLE_PROFILE_EQUIPTMENT_DESC');
						$markerARRAY['###SINGLE_PROFILE_PROJECTS_DESC###']            = $this->pi_getLL('SINGLE_PROFILE_PROJECTS_DESC');
						$markerARRAY['###SINGLE_PROFILE_COOPERATIONS_DESC###']        = $this->pi_getLL('SINGLE_PROFILE_COOPERATIONS_DESC');
						$markerARRAY['###SINGLE_PROFILE_AWARDS_DESC###']              = $this->pi_getLL('SINGLE_PROFILE_AWARDS_DESC');
						$markerARRAY['###SINGLE_PROFILE_LINK_DESC###']                = $this->pi_getLL('SINGLE_PROFILE_LINK_DESC');
						$markerARRAY['###SINGLE_PROFILE_ADD_INFO_DESC###']            = $this->pi_getLL('SINGLE_PROFILE_ADD_INFO_DESC');
						$markerARRAY['###SINGLE_PROFILE_INFO_WEITER###']              = $this->pi_getLL('SINGLE_PROFILE_INFO_WEITER');
						$markerARRAY['###SINGLE_PROFILE_NAME###']                     = $prof['name']; //TEST
						$markerARRAY['###SINGLE_PROFILE_FUNKTION###']                 = $prof['funktion'];
						$markerARRAY['###SINGLE_PROFILE_INDIVIDUAL_EMPHASIS###']      = $prof['ind_forschungsschwerpunkte'];
						$markerARRAY['###SINGLE_PROFILE_MEMBERSHIPS###']              = $prof['mitgliedschaften'];
						$markerARRAY['###SINGLE_PROFILE_EQUIPTMENT###']               = $prof['austattung'];
						//$markerARRAY['###SINGLE_PROFILE_PROJECTS###']                 = $prof['referenzprojekte'];
						$markerARRAY['###SINGLE_PROFILE_COOPERATIONS###']             = $prof['kooperationen'];
						$markerARRAY['###SINGLE_PROFILE_AWARDS###']                   = $prof['preise'];
						$markerARRAY['###SINGLE_PROFILE_DOWNLOADS###']                = $prof['downloads'];
						if(!empty($prof['links']) && $prof['links'] != '; '){$markerARRAY['###SINGLE_PROFILE_LINK###']                     = $prof['links'];} else{$markerARRAY['###SINGLE_PROFILE_LINK###'] = '';}
						$markerARRAY['###SINGLE_PROFILE_ADD_INFO###']                 = $prof['zusatzinfos'];
						$markerARRAY['###SINGLE_PROFILE_TAG###']                      = $prof['name'];
						$markerARRAY['###SINGLE_RETURNLINK###']                       = $returnlink;
						if ($prof['funktion'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_FUNKTION_DESC###'] = '';
							}
						if ($k == 0)
							{
								$markerARRAY['###SINGLE_PROFILE_FACULTY_DESC###'] = '';
								$markerARRAY['###SINGLE_PROFILE_FACULTY###']      = '';
							}
						if ($l == 0)
							{
								$markerARRAY['###SINGLE_PROFILE_INSTITUTE_DESC###'] = '';
								$markerARRAY['###SINGLE_PROFILE_INSTITUTE###']      = '';
							}
						if ($j == 0)
							{
								$markerARRAY['###SINGLE_PROFILE_EMPHASIS_DESC###'] = '';
								$markerARRAY['###SINGLE_PROFILE_EMPHASIS###']      = '';
							}
						if ($m == 0)
							{
								$markerARRAY['###SINGLE_PROFILE_PROJECTS_DESC###'] = '';
								$markerARRAY['###SINGLE_PROFILE_PROJECTS###']      = '';
							}
						if ($prof['ind_forschungsschwerpunkte'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_INDIVIDUAL_EMPHASIS_DESC###'] = '';
							}
						if ($prof['mitgliedschaften'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_MEMBERSHIPS_DESC###'] = '';
							}
						if ($prof['austattung'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_EQUIPTMENT_DESC###'] = '';
							}
						/* if ($prof['referenzprojekte'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_PROJECTS_DESC###'] = '';
							} */
						if ($prof['kooperationen'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_COOPERATIONS_DESC###'] = '';
							}
						if ($prof['preise'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_AWARDS_DESC###'] = '';
							}
						if ($prof['links'] == '' || $prof['links'] == '; ' )
							{
								$markerARRAY['###SINGLE_PROFILE_LINK_DESC###'] = '';
								$prof['links'] = '';
							}
						if ($prof['zusatzinfos'] == '')
							{
								$markerARRAY['###SINGLE_PROFILE_ADD_INFO_DESC###'] = '';
							}
						if ($this->aktiv == 0 || ($this->aktiv == 1 && $this->pruf_IP($_SERVER['REMOTE_ADDR'], $prof['uid'], 'prof') == 0))
							{
								$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_foportal_profile SET klicks = klicks +1 WHERE uid =' . $entryid . '');
							}
						}
				if ($type == 'proj')
					{
						$j      = 0;
						$k      = 0;
						$l      = 0;
						$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_projekte', 'uid=' . $entryid);
						//debug($entryid,'entryid in proj:');
						$proj   = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
						$fsp    = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_projekte', 'tx_foportal_projekte_forschungsschwerpunkt_mm', 'tx_foportal_forschungsschwerpunkte', 'AND tx_foportal_projekte_forschungsschwerpunkt_mm.uid_local = ' . $proj['uid'] . ' ', '', '', '');
						$fb     = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_projekte', 'tx_foportal_projekte_fachbereich_mm', 'tx_foportal_fachbereiche', 'AND tx_foportal_projekte_fachbereich_mm.uid_local = ' . $proj['uid'] . ' ', '', '', '');
						$inst   = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query('*', 'tx_foportal_projekte', 'tx_foportal_projekte_institut_mm', 'tx_foportal_institute', 'AND tx_foportal_projekte_institut_mm.uid_local = ' . $proj['uid'] . ' ', '', '', '');
						
						
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($fsp))
							{
								$markerARRAY['###SINGLE_PROJECT_EMPHASIS###'] .= $row['forschungsschwerpunkt'] . ' </br>';
								$j++;
							}
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($fb))
							{
								$markerARRAY['###SINGLE_PROJECT_FACULTY###'] .= $row['fachbereich'] . ' </br>'; //LINK! über a href $row['link']
								$k++;
							}
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($inst))
							{
								$markerARRAY['###SINGLE_PROJECT_INSTITUTE###'] .= $row['short'] . ' </br>'; //LINK! über a href $row['link']
								$l++;
							}
						// Subpart aus Template laden
						$subpart                                            = $this->cObj->getSubpart($template, "###SINGLE_TEMPLATE_PROJ###");
						$projekttypen                                       = array(
								"Forschungsprojekt",
								"Entwicklungsprojekt",
								"Design-Projekt",
								"Studenten-Projekt"
						);
						$date1 = strtotime(date('d.m.Y', $proj['projektende'])); 
						$date2 = strtotime(date('d.m.Y', $proj['projektbeginn']));
						$date3 = date('d.m.Y', time());						
						$diff = $date1-$date2;
						$diff2 = $date3- $date1;
						$monate = floor($diff/2628000) +1;
						$status = $this->pi_getLL('SINGLE_PROJECT_STATE1');
						$projektleiterKontakt = explode(',',$proj['projektleiter']);
						$projektmitarbeiterKontakt = explode(',',$proj['mitarbeiter']);
						//debug($diff2,'Projekt');
						// Ersetzungen anlegen
						$markerARRAY['###SINGLE_PROJECT_INFO_ALG###']       = $this->pi_getLL('SINGLE_PROJECT_INFO_ALG');
						$markerARRAY['###SINGLE_PROJECT_INFO_WEITER###']    = $this->pi_getLL('SINGLE_PROJECT_INFO_WEITER');
						$markerARRAY['###SINGLE_PROJECT_TITLE_DESC###']     = $this->pi_getLL('SINGLE_PROJECT_TITLE_DESC');
						$markerARRAY['###SINGLE_PROJECT_MANAGER_DESC###']   = $this->pi_getLL('SINGLE_PROJECT_MANAGER_DESC');
						$markerARRAY['###SINGLE_PROJECT_FACULTY_DESC###']   = $this->pi_getLL('SINGLE_PROJECT_FACULTY_DESC');
						$markerARRAY['###SINGLE_PROJECT_INSTITUTE_DESC###'] = $this->pi_getLL('SINGLE_PROJECT_INSTITUTE_DESC');
						$markerARRAY['###SINGLE_PROJECT_VOLUME_DESC###']    = $this->pi_getLL('SINGLE_PROJECT_VOLUME_DESC');
						$markerARRAY['###SINGLE_PROJECT_TYPE_DESC###']      = $this->pi_getLL('SINGLE_PROJECT_TYPE_DESC');
						$markerARRAY['###SINGLE_PROJECT_BEGIN_DESC###']     = $this->pi_getLL('SINGLE_PROJECT_BEGIN_DESC');
						$markerARRAY['###SINGLE_PROJECT_END_DESC###']       = $this->pi_getLL('SINGLE_PROJECT_END_DESC');
						$markerARRAY['###SINGLE_PROJECT_FUNDING_DESC###']   = $this->pi_getLL('SINGLE_PROJECT_FUNDING_DESC');
						$markerARRAY['###SINGLE_PROJECT_FUNDER_DESC###']    = $this->pi_getLL('SINGLE_PROJECT_FUNDER_DESC');
						$markerARRAY['###SINGLE_PROJECT_EMPHASIS_DESC###']  = $this->pi_getLL('SINGLE_PROJECT_EMPHASIS_DESC');
						$markerARRAY['###SINGLE_PROJECT_WEBSITE_DESC###']   = $this->pi_getLL('SINGLE_PROJECT_WEBSITE_DESC');
						$markerARRAY['###SINGLE_PROJECT_DOWNLOADS_DESC###'] = $this->pi_getLL('SINGLE_PROJECT_DOWNLOADS_DESC');
						$markerARRAY['###SINGLE_PROJECT_HEAD###']           = $this->pi_getLL('SINGLE_PROJECT_HEAD');
						$markerARRAY['###SINGLE_RETURNLINK###']             = $this->pi_getLL('SINGLE_RETURNLINK');
						$markerARRAY['######']                              = $this->pi_getLL('');
						$markerARRAY['###SINGLE_PROJECT_TITLE###']          = $proj['projekttitel']; //TEST
						$markerARRAY['###SINGLE_PROJECT_MANAGER###']        = $proj['projektleiteranzeige'];
						$markerARRAY['###SINGLE_PROJECT_TYPE###']           = $this->pi_getLL($projekttypen[$proj['projekttyp']]);
						$markerARRAY['###SINGLE_PROJECT_YEAR###']           = $proj['jahr'];
						$markerARRAY['###SINGLE_PROJECT_BEGIN###']          = date('d.m.Y', $proj['projektbeginn']);
						$markerARRAY['###SINGLE_PROJECT_END###']            = date('d.m.Y', $proj['projektende']);
						$markerARRAY['###SINGLE_PROJECT_VOLUME###']         = number_format($proj['projektvolumen'], 2, ',', '.') . ' &euro;';
						$markerARRAY['###SINGLE_PROJECT_FUNDING###']        = number_format($proj['foerdervolumen'], 2, ',', '.') . ' &euro;';
						$markerARRAY['###SINGLE_PROJECT_FUNDER###']         = $proj['foerdermittelgeber'];
						$markerARRAY['###SINGLE_PROJECT_WEBSITE###']        = '<a href="http://' . $proj['webseite'] . '" >Projektwebsite</a>';
						$markerARRAY['###SINGLE_PROFILE_LINK###']           = '<a href="http://' . $proj['links'] . '" >Link</a>';
						$markerARRAY['###SINGLE_PROJECT_DOWNLOADS###']      = $proj['downloads'];
						$markerARRAY['###SINGLE_PROJECT_TAG###']            = $proj['tag'];
						$markerARRAY['###SINGLE_RETURNLINK###']             = $returnlink;
						$markerARRAY['###SINGLE_PROJECT_DESC_DESC###']		= $this->pi_getLL('SINGLE_PROJECT_DESC_DESC');
						$markerARRAY['###SINGLE_PROJECT_DESC###']			= $proj['kurzbeschreibung'];
						$markerARRAY['###SINGLE_PROJECT_RUNTIME_DESC###']	= $this->pi_getLL('SINGLE_PROJECT_RUNTIME');
						$markerARRAY['###SINGLE_PROJECT_RUNTIME###']		= $monate.' '.$this->pi_getLL('MONTH');
						$markerARRAY['###SINGLE_PROJECT_RUNTIME_DESC###']	= $this->pi_getLL('SINGLE_PROJECT_RUNTIME');
						$markerARRAY['###SINGLE_PROJECT_RUNTIME###']		= $monate.' '.$this->pi_getLL('MONTH');
						$markerARRAY['###SINGLE_PROJECT_STATE_DESC###']	= '';
						$markerARRAY['###SINGLE_PROJECT_STATE###']		= '';
						$markerARRAY['###SINGLE_PROJECT_LEITER_KONTAKT_DESC###'] = $this->pi_getLL('SINGLE_PROJECT_LEITER_KONTAKT_DESC');
						debug($proj['foerdervolumen'],'debug:');
						if(empty($proj['foerdervolumen'])){$markerARRAY['###SINGLE_PROJECT_FUNDING###']        = number_format(0, 2, ',', '.') . ' &euro;';}
						if($proj['projektleiter'] != '0'){
						foreach($projektleiterKontakt as $l){
							$markerARRAY['###SINGLE_PROJECT_LEITER_KONTAKT###']		.= $this->showPersonElement($l);
						}
						foreach($projektmitarbeiterKontakt as $m){
							$markerARRAY['###SINGLE_PROJECT_MITARBEITER_KONTAKT###']		.= $this->showPersonElement($m);
						}
						}
						else
							{
								$markerARRAY['###SINGLE_PROJECT_LEITER_KONTAKT###']		.= 'Keine Kontaktinformationen';
								$markerARRAY['###SINGLE_PROJECT_MITARBEITER_KONTAKT###']		.= '';
							}
						if ($k == 0)
							{
								$markerARRAY['###SINGLE_PROJECT_FACULTY_DESC###'] = '';
								$markerARRAY['###SINGLE_PROJECT_FACULTY###']      = '';
							}
						if ($l == 0)
							{
								$markerARRAY['###SINGLE_PROJECT_INSTITUTE_DESC###'] = '';
								$markerARRAY['###SINGLE_PROJECT_INSTITUTE###']      = '';
							}
						
						if ($proj['projekttyp'] == '')
							{
								$markerARRAY['###SINGLE_PROJECT_TYPE_DESC###'] = '';
							}
						if ($proj['projektbeginn'] == '')
							{
								$markerARRAY['###SINGLE_PROJECT_BEGIN_DESC###'] = '';
							}
						if ($proj['projektende'] == '')
							{
								$markerARRAY['###SINGLE_PROJECT_END_DESC###'] = '';
							}
						
						if ($proj['foerdermittelgeber'] == '')
							{
								$markerARRAY['###SINGLE_PROJECT_FUNDER_DESC###'] = '';
							}
						if ($j == 0)
							{
								$markerARRAY['###SINGLE_PROJECT_EMPHASIS_DESC###'] = '';
								$markerARRAY['###SINGLE_PROJECT_EMPHASIS###']      = '';
							}
						if ($proj['webseite'] == '')
							{
								$markerARRAY['###SINGLE_PROJECT_WEBSITE_DESC###'] = '';
								$markerARRAY['###SINGLE_PROJECT_WEBSITE###']      = '';
							}
						if ($proj['projektleiteranzeige'] == '')
							{
								$markerARRAY['###SINGLE_PROJECT_HEAD###'] = '';
							}
						if ($proj['kurzbeschreibung'] == '')
							{
								$markerARRAY['###SINGLE_PROJECT_DESC_DESC###']		= '';
								$markerARRAY['###SINGLE_PROJECT_DESC###']			= '';
							}
						if ($proj['projektbeginn'] == '' || $proj['projektende'] == '' )
							{
								$markerARRAY['###SINGLE_PROJECT_RUNTIME_DESC###']		= '';
								$markerARRAY['###SINGLE_PROJECT_RUNTIME###']			= '';
								$markerARRAY['###SINGLE_PROJECT_STATE_DESC###']	= '';
								$markerARRAY['###SINGLE_PROJECT_STATE###']		= '';
							}
						if ($this->aktiv == 0 || ($this->aktiv == 1 && $this->pruf_IP($_SERVER['REMOTE_ADDR'], $proj['uid'], 'proj') == 0))
								$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_foportal_projekte SET klicks = klicks +1 WHERE uid =' . $entryid . '');
					}
				
				$returnlink                             = '<a href="javascript:history.back()">zur&uuml;ck</a>';
				
				$markerARRAY['###SINGLE_RETURNLINK###'] = $returnlink;
				
				$singlepage .= $this->cObj->substituteMarkerArray($subpart, $markerARRAY);
				return $singlepage;
			}
		function pruf_IP($rem_addr, $rem_uid, $rem_typ)
			{
				$ipdatei = "typo3conf/ext/foportal/pi1/static/ips.txt";
				$zeit    = 60;
				@$ip_array = file($ipdatei);
				$reload_dat = fopen($ipdatei, "w");
				$this_time  = time();
				for ($i = 0; $i < count($ip_array); $i++)
					{
						list($ip_addr, $time_stamp, $uid, $typ) = explode("|", $ip_array[$i]);
						if ($this_time < ($time_stamp + 60 * $zeit))
							{
								if (($ip_addr == $rem_addr) && ($uid == $rem_uid) && ($typ == $rem_typ))
									{
										$gefunden = 1;
									}
								else
									{
										fwrite($reload_dat, "$ip_addr|$time_stamp|$uid|$typ");
									}
							}
					}
				fwrite($reload_dat, "$rem_addr|$this_time|$rem_uid|$rem_typ\n");
				fclose($reload_dat);
				return ($gefunden == 1) ? 1 : 0;
			}

			function generateStatisticView()
			{
				$i = 0;
				if (isset($this->conf["templateFile"]))
						$template = $this->cObj->fileResource($this->conf["templateFile"]);
				else
						$template = $this->cObj->fileResource($this->standardTemplate);
				$researcherKlicks = $GLOBALS['TYPO3_DB']->exec_SELECTquery('name,klicks', 'tx_foportal_profile', '','','klicks DESC');
				$pubKlicks = $GLOBALS['TYPO3_DB']->exec_SELECTquery('titel,klicks', 'tx_foportal_publikationen', '','','klicks DESC');
				$projKlicks = $GLOBALS['TYPO3_DB']->exec_SELECTquery('projekttitel,klicks', 'tx_foportal_projekte', '','','klicks DESC');
				$profileSum       = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('SUM(klicks) AS summe', 'tx_foportal_profile', ''));
				$pubSum           = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('SUM(klicks) AS summe', 'tx_foportal_publikationen', ''));
				$projektSum       = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECTquery('SUM(klicks) AS summe', 'tx_foportal_projekte', ''));
				$markerARRAY['###GOOGLE_API###'] .= '<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var profileData = google.visualization.arrayToDataTable([';
				$markerARRAY['###GOOGLE_API###'] .= '[\'Forscher\', \'Klicks\'],';
				while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($researcherKlicks)) && ($i <= 19))
					{
						$markerARRAY['###GOOGLE_API###'] .= '[ \' ' . $row['name'] . ' \' , ' . $row['klicks'] . '],';
						$i++;
					}
				$markerARRAY['###GOOGLE_API###'] .= ']);';
		$i = 0;
		$markerARRAY['###GOOGLE_API###'] .= 'var pubData = google.visualization.arrayToDataTable([';
				$markerARRAY['###GOOGLE_API###'] .= '[\'Publikation\', \'Klicks\'],';
				while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pubKlicks)) && ($i <= 19))
					{
						$markerARRAY['###GOOGLE_API###'] .= '[ \' ' . $row['titel'] . ' \' , ' . $row['klicks'] . '],';
						$i++;
					}
				$markerARRAY['###GOOGLE_API###'] .= ']);';
		$i = 0;
		$markerARRAY['###GOOGLE_API###'] .= 'var projData = google.visualization.arrayToDataTable([';
				$markerARRAY['###GOOGLE_API###'] .= '[\'Projekt\', \'Klicks\'],';
				while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projKlicks)) && ($i <= 19))
					{
						$markerARRAY['###GOOGLE_API###'] .= '[ \' ' . $row['projekttitel'] . ' \' , ' . $row['klicks'] . '],';
						$i++;
					}
				$markerARRAY['###GOOGLE_API###'] .= ']);';
				
				$markerARRAY['###GOOGLE_API###'] .= ' var andereData = google.visualization.arrayToDataTable([';
				$markerARRAY['###GOOGLE_API###'] .= '[\'Typ\', \'Klicks\'],
          [\'Forscherprofile\',  ' . $profileSum['summe'] . '],
          [\'Publikationen\',  ' . $pubSum['summe'] . '],
		  [\'Projekte\',  ' . $projektSum['summe'] . ']';
				$markerARRAY['###GOOGLE_API###'] .= ']);';
				$markerARRAY['###GOOGLE_API###'] .= '    var options = {
          title: \'Anzahl Klicks Forscherprofile\',
          hAxis: {title: \'\', titleTextStyle: {color: \'blue\'}}
        };
		
		var options2 = {
          title: \'Vergleich Profile, Projekte und Publikationen\',
          hAxis: {title: \'\', titleTextStyle: {color: \'blue\'}}
        };
		
		var options3 = {
          title: \'Anzahl Klicks Publikationen\',
          hAxis: {title: \'\', titleTextStyle: {color: \'blue\'}}
        };
		
		var options4 = {
          title: \'Anzahl Klicks Projekte\',
          hAxis: {title: \'\', titleTextStyle: {color: \'blue\'}}
        };
		
        var chart = new google.visualization.BarChart(document.getElementById(\'chart_div\'));
		var chart3 = new google.visualization.BarChart(document.getElementById(\'chart_div3\'));
		var chart4 = new google.visualization.BarChart(document.getElementById(\'chart_div4\'));
		var chart2 = new google.visualization.BarChart(document.getElementById(\'chart_div2\'));
        chart.draw(profileData, options);
		chart2.draw(andereData, options2);
		chart3.draw(pubData, options3);
		chart4.draw(projData, options4);
		
      }
    </script>';
				$markerARRAY['###DRAWAREA###'] .= '<div id="chart_div" style="width: 450px; height: 400px;"></div>';
				$markerARRAY['###DRAWAREA###'] .= '<div id="chart_div3" style="width: 450px; height: 400px;"></div>';
				$markerARRAY['###DRAWAREA###'] .= '<div id="chart_div4" style="width: 450px; height: 400px;"></div>';
				$markerARRAY['###DRAWAREA###'] .= '<div id="chart_div2" style="width: 450px; height: 400px;"></div>';
				$markerARRAY['###BACK###'] = '<a href="javascript:history.back()">'.$this->pi_getLL('back').'</a>';
				$subpart = $this->cObj->getSubpart($template, "###STATISTIK_TEMPLATE###");
				$singlepage .= $this->cObj->substituteMarkerArray($subpart, $markerARRAY);
				return $singlepage;
			}
		
		/**
		* URL validation method
		*
		* @param	string		$url: URL to validate
		* @return	boolean		Success: valid / not valid
		*/
		function generateTagView()
			{
				$uparams            = t3lib_div::_GET($this->prefixId);
				$this->singlepageID = $this->pi_getFFValue($this->ffdata, 'singlepid', 'sOtherSettings');
				$mainpage           = $uparams['main'];
				$wert = $this->singlepageID;
				if (isset($this->conf["templateFile"]))
						$template = $this->cObj->fileResource($this->conf["templateFile"]);
				else
						$template = $this->cObj->fileResource($this->standardTemplate);
				$subpart                      = $this->cObj->getSubpart($template, "###TAGSEARCH_TEMPLATE###");
				$markerARRAY['###CAPTION###'] = $this->pi_getLL('tagsearch.caption');
				foreach (range('A', 'Z') as $c)
					{
						$markerARRAY['###INDEX###'] .= $this->pi_linkToPage($c, $GLOBALS['TSFE']->id, '', array(
								$this->prefixId => array(
										'main' => $mainpage,
										'alpha' => $c
								)
						)) . '&nbsp;';
					}
				$markerARRAY['###LISTE###'] = '<ul id="faqList">';
				if (!empty($this->piVars['alpha']))
					{
						$wherecl = 'tx_foportal_tags.tag LIKE "' . $this->piVars['alpha'] . '%" AND tx_foportal_tags.deleted="0" AND hidden="0"';
						$tagerg  = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_foportal_tags ', $wherecl);
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($tagerg))
							{
								$markerARRAY['###LISTE###'] .= '<li> <p class=\'question\'>' . $row['tag'] . '</p>';
								$proj = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
									'*',
									'tx_foportal_projekte', 'tx_foportal_projekte_tag_mm',
									'tx_foportal_tags',
									'AND tx_foportal_projekte_tag_mm.uid_foreign = ' . $row['uid'] . ' ',
									'',
									'',
									'');
								$pub  = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
									'*',
									'tx_foportal_publikationen',
									'tx_foportal_publikationen_tag_mm',
									'tx_foportal_tags', 'AND tx_foportal_publikationen_tag_mm.uid_foreign = ' . $row['uid'] . ' ',
									'',
									'',
									'');
								$prof = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
									'*',
									'tx_foportal_profile',
									' tx_foportal_profile_tag_mm', 'tx_foportal_tags',
									'AND  tx_foportal_profile_tag_mm.uid_foreign = ' . $row['uid'] . ' ',
									'',
									'', 
									'');
								$markerARRAY['###LISTE###'] .= '<div class=\'answer\'>';
								//03.06.2013$markerARRAY['###LISTE###'] .= '<div class="trenner"></div>';
								while ($lentry = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($proj))
									{
										$params = array(
												$this->prefixId => array(
														'entryid' => $lentry['uid_local'],
														ppid => 70,
														'type' => 'proj'
												)
										);
										$markerARRAY['###LISTE###'] .= '<p> <img src="typo3conf/ext/foportal/pi1/static/icons/projektlogosw.png" width="30" height="30" > ' 
										. $this->pi_linkToPage('Forschungsprojekt: '.$lentry['projekttitel'], intval($wert), '', $params) . '</p>';
									}
								//$markerARRAY['###LISTE###'] .= '<p> Publikationen </p>';	
								$markerARRAY['###LISTE###'] .= '<div class="trenner"></div>';	
								while ($lentry = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pub))
									{
										//$params = array( $this->prefixId => array( 'entryid' => $row['uid'], ppid => $GLOBALS['TSFE']->id, 'type' => 'pub'));
										$params = array(
												$this->prefixId => array(
														'entryid' => $lentry['uid_local'],
														ppid => 70,
														'type' => 'pub'
												)
										);
										if($lentry['typ'] == 0){
											$pre = 'Monographie';
										}
										if($lentry['typ'] == 1){
											$pre = 'Beitrag in einem Herausgeberwerk';
										}
										else {$pre = 'Artikel in einer Fachzeitschrift';}
										$markerARRAY['###LISTE###'] .= '<p> <img src="typo3conf/ext/foportal/pi1/static/icons/publogosw.png" width="30" height="30" > ' 
										. $this->pi_linkToPage($pre.': '.$lentry['titel'], intval($wert), '', $params) . '</p>';
										//$markerARRAY['###LISTE###'] .='<p>Autor(en): '.$lentry['autor'].'  Jahr: '.$lentry['jahr'].'</p>';
									}
								$markerARRAY['###LISTE###'] .= '<div class="trenner"></div>';	
								while ($lentry = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($prof))
									{
										$params = array(
												$this->prefixId => array(
														'entryid' => $lentry['uid_local'],
														ppid => 84,
														'type' => 'prof'
												)
										);
										$markerARRAY['###LISTE###'] .= '<p> <img src="typo3conf/ext/foportal/pi1/static/icons/profilelogosw.png" width="30" height="30" > ' 
										. $this->pi_linkToPage($lentry['name'], intval($wert), '', $params) . '</p>';
									}
								$markerARRAY['###LISTE###'] .= '</div></li>';
							}
						$markerARRAY['###LISTE###'] .= '</ul>';
					}
				$markerARRAY['###LISTE###'] .= '<script type="text/javascript">
									$(document).ready(function() {
										$(\'#faqList\').simpleFAQ({speed: 225});
									});
								</script>';
				$markerARRAY['###BACK###'] = $this->pi_linkToPage('<< ' . $this->pi_getLL('back'), $mainpage);
				$tagpage .= $this->cObj->substituteMarkerArray($subpart, $markerARRAY);
				return $tagpage;
			}
			
			function inputForm()
				{	
					$params     = t3lib_div::_GET($this->prefixId); //  Ermittelt Parameter Aus GET Werten
					$this->pi_setPiVarDefaults(); //POST GET VORLADEN
					$type       = $params['type'];
					$resultfb                         = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,fachbereich', 'tx_foportal_fachbereiche', 'fachbereich!="" AND tx_foportal_fachbereiche.deleted="0" AND tx_foportal_fachbereiche.hidden="0"', '', 'fachbereich');
					$resultfsp                        = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,forschungsschwerpunkt', 'tx_foportal_forschungsschwerpunkte', 'forschungsschwerpunkt!="" AND tx_foportal_forschungsschwerpunkte.deleted="0" AND tx_foportal_forschungsschwerpunkte.hidden="0"', '', 'forschungsschwerpunkt');
					$resultinst                       = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,short', 'tx_foportal_institute', 'short!="" AND tx_foportal_institute.deleted="0" AND tx_foportal_institute.hidden="0"', '', 'short');
					$resulttag                      = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,tag', 'tx_foportal_tags', 'tag!="" AND tx_foportal_tags.deleted="0" AND tx_foportal_tags.hidden="0"', '', 'tag');
					//$typeP = $this->postvars ['typ'];
					if (isset($this->conf["templateFile"]))
						$template = $this->cObj->fileResource($this->conf["templateFile"]);
					else
						$template = $this->cObj->fileResource($this->standardTemplate);
					$inputpid     = $this->pi_getFFValue($this->ffdata, 'inputpid', 'sOtherSettings');
					$storagepid     = $this->pi_getFFValue($this->ffdata, 'storagepid', 'sOtherSettings');
					$url = $GLOBALS ["TSFE"]->id ;
					
										//if($type == 'pub'|| $typeP == 'pub'){
					$errors = array();
					$message = '';
					$class = '';
					
					
					if(isset($this->piVars['send_form']) && !isset($this->piVars['ausw'])) {
					
						if($this->piVars['typ'] == 'proj'){
						
					
						if(!isset($this->piVars['projekttitel']) || trim($this->piVars['projekttitel']) == '') {
							$errors['projekttitel'] = 'Bitte geben Sie einen Projekttitel ein!';
						}
						if(!isset($this->piVars['projektleiteranzeige']) || trim($this->piVars['projektleiteranzeige']) == '') {
							$errors['projektleiteranzeige'] = 'Bitte geben Sie mindestens einen Projektleiter an.';
						}
						if(!isset($this->piVars['jahr']) || trim($this->piVars['jahr']) == '') {
							$errors['jahr'] = 'Bitte geben Sie das Startjahr an.';
						}
						/* if ( trim($this->piVars['webseite']) != '' ){
							if($this->isURL ( $this->piVars['webseite'] ) == false){
							$errors['webseite'] = 'Bitte geben Sie eine korrekte URL ein.';}
						} */
					}
					
					if($this->piVars['typ'] == 'prof'){
						
					
						if(!isset($this->piVars['name']) || trim($this->piVars['name']) == '') {
							$errors['name'] = 'Bitte geben Sie einen Namen ein!';
						}
						if(!isset($this->piVars['fachbereich']) || count($this->piVars['fachbereich']) < 1) {
							 $errors['fachbereich'] = 'Bitte geben Sie mindestens einen Fachbereich an.';
						}
						if(!isset($this->piVars['forschungschwerpunkte']) || count($this->piVars['forschungschwerpunkte']) < 1) {
							 $errors['forschungsschwerpunkte'] = 'Bitte geben Sie mindestens einen Forschungsschwerpunkt der HN an.';
						}
						// if(!isset($this->piVars['jahr']) || trim($this->piVars['jahr']) == '') {
							// $errors['jahr'] = 'Bitte geben Sie das Startjahr an.';
						// }
						// if ($this->isURL ( $this->piVars['webseite'] ) == false){
							// $errors['webseite'] = 'Bitte geben Sie eine korrekte URL ein.';
						// }
					}
					
					if($this->piVars['typ'] == 'pub'){
						
					
						if(!isset($this->piVars['titel']) || trim($this->piVars['titel']) == '') {
							$errors['titel'] = 'Bitte geben Sie einen Titel ein!';
						}
						if(!isset($this->piVars['autor']) || trim($this->piVars['autor']) == '') {
							$errors['autor'] = 'Bitte geben Sie mindestens einen Autor an.';
						}
						/* if ( trim($this->piVars['link']) != '' ){
							if($this->isURL ( $this->piVars['link'] ) == false){
							$errors['link'] = 'Bitte geben Sie eine korrekte URL ein.';}
						} */
					}
					
					
							debug($errors,'errors');
						if(count($errors) == 0) {
							//	Absenden der Nachricht
							$date = date_create();
							 
							$message = $this->pi_getLL('inputform.placeholder_erfolg');
							$class = 'message';
							$saveData ['uid'] = '';
							$saveData ['pid'] = $storagepid;
							$saveData ['tstamp'] = date_timestamp_get($date);
							$saveData ['crdate'] = date_timestamp_get($date);
							$saveData ['deleted'] = '0';
							$saveData ['hidden'] = '1';
							$saveData ['klicks'] = '0';
							
							if($this->piVars['typ'] == 'prof'){
								$db_fields = array ('name', 'funktion', 'mitgliedschaften', 'austattung', 'kooperationen', 'preise');
								
								for ($i = 2; $i <= 6; $i++){
									if(isset($this->piVars['ind_forschungsschwerpunkte'.$i.'']))
									$ind_fsp .= ''.$this->piVars['ind_forschungsschwerpunkte'.$i].'; ';
									if(isset($this->piVars['links'.$i.'']))
									$links .= ''.$this->piVars['links'.$i].'; ';
								}
							
								$saveData ['fachbereich'] = count($this->piVars['fachbereich']);
								$saveData ['institute'] = count($this->piVars['institute']);
								$saveData ['forschungschwerpunkte'] = count($this->piVars['forschungschwerpunkte']);
								$saveData ['tag'] = count($this->piVars['tag']);
								$saveData ['ind_forschungsschwerpunkte'] = $this->piVars['ind_forschungsschwerpunkte'].'; '. $ind_fsp;
								$saveData ['links'] = $this->piVars['links'].'; '. $links;
								
								foreach ( $this->piVars as $k => $v ) {
										if (in_array ( $k, $db_fields )) {										
											$saveData [$k] =  $v;
										}
									}
								$insert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_profile', $saveData );
								$last = $GLOBALS ['TYPO3_DB']->sql_insert_id();
								
								if($insert){
									foreach ( $this->piVars['fachbereich'] as $s => $t ) {
										$fbinsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_profile_fachbereich_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
									}
									foreach ( $this->piVars['institute'] as $s => $t ) {
										$instinsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_profile_institute_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
									}
									foreach ( $this->piVars['forschungschwerpunkte'] as $s => $t ) {
										$fspinsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_profile_forschungschwerpunkte_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
									}
									foreach ( $this->piVars['tag'] as $s => $t ) {
										$taginsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_profile_tag_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
									}
									
									if (! empty ( $this->config ['notify_mail'] )) {
										$this->sendNotificationMail ( $this->config ['notify_mail'] );
									}
								}
							
							
							}
							// $insert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_profile', $saveData );
							// $last = $GLOBALS ['TYPO3_DB']->sql_insert_id();
							
							if($this->piVars['typ'] == 'proj'){
								$beginn = new DateTime($this->piVars['projektbeginn']);
								$ende = new DateTime($this->piVars['projektende']);
								$db_fields = array ('projekttitel', 'projekttyp', 'kurzbeschreibung', 'jahr', 'projektvolumen', 'foerdervolumen', 'foerdermittelgeber', 'webseite');
								
								 for ($i = 2; $i <= 10; $i++){
									if(isset($this->piVars['projektleiteranzeige'.$i.'']))
									$projektleiter .= ''.$this->piVars['projektleiteranzeige'.$i].'; ';
									if(isset($this->piVars['projektmitarbeiteranzeige'.$i.'']))
									$projektmitarbeiter .= ''.$this->piVars['projektmitarbeiteranzeige'.$i].'; ';
								}
								$saveData ['projektleiteranzeige'] = ''.$this->piVars['projektleiteranzeige'].'; '.$projektleiter;	
								$saveData ['projektmitarbeiteranzeige'] = ''.$this->piVars['projektmitarbeiteranzeige'].'; '.$projektmitarbeiter;
								$saveData ['fachbereich'] = count($this->piVars['fachbereich']);
								$saveData ['institut'] = count($this->piVars['institut']);
								$saveData ['forschungsschwerpunkt'] = count($this->piVars['forschungsschwerpunkt']);
								$saveData ['tag'] = count($this->piVars['tag']);
								$saveData ['projektbeginn'] = date_timestamp_get($beginn);
								$saveData ['projektende'] = date_timestamp_get($ende);
								
								foreach ( $this->piVars as $k => $v ) {
										if (in_array ( $k, $db_fields )) {										
											$saveData [$k] =  $v;
										}
									}
								$insert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_projekte', $saveData );
								$last = $GLOBALS ['TYPO3_DB']->sql_insert_id();
								foreach ( $this->piVars['fachbereich'] as $s => $t ) {
									$fbinsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_projekte_fachbereich_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
								}
								foreach ( $this->piVars['institut'] as $s => $t ) {
									$instinsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_projekte_institut_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
								}
								foreach ( $this->piVars['forschungsschwerpunkt'] as $s => $t ) {
									$fspinsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_projekte_forschungsschwerpunkt_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
								}
								foreach ( $this->piVars['tag'] as $s => $t ) {
									$taginsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_projekte_tag_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
								}
							
							//$fbinsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_projekte', $saveData );
							}
							
							if($this->piVars['typ'] == 'pub'){
							//Hier Unterscheidung pub,projekt...
								$db_fields = array ('titel', 'autor', 'beitrag_in', 'verlag', 'ort', 'isbn', 'jahr', 'ausgabe', 'seiten', 'link', 'abstract');
								
								
								foreach ( $this->piVars as $k => $v ) {
										if (in_array ( $k, $db_fields )) {										
											$saveData [$k] =  $v;
										}
									}
								
								$saveData['typ'] = $this->piVars['pubtyp'];
								
								$insert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_publikationen', $saveData );
								$last = $GLOBALS ['TYPO3_DB']->sql_insert_id();
								
								if($insert){
									foreach ( $this->piVars['tag'] as $s => $t ) {
										$taginsert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_publikationen_tag_mm', array('uid_local' => $last, 'uid_foreign' => $t) );
									}
									foreach ( $this->piVars as $k => $v ) {
										if (in_array ( $k, $db_fields )) {										
											unset($this->piVars[$k]);
										}
									}
								}
							
							}
						}
						else {
							$message = 'Die Nachricht konnte nicht abgesendet werden. Bitte überprüfen Sie Ihre Eingaben!';
							$class = 'error';
						}
					}
					
					debug($this->piVars,'piVars');
					debug($saveData,'savedata');

				 if($this->piVars['typ'] == 'ausw' || $type == 'ausw'){
					$subpart                      = $this->cObj->getSubpart($template, "###INPUT_FORM_AUSW###");
					$markerARRAY ['###INPUT_FORM_BEGIN###'] = '<form method="post" action="'.htmlspecialchars ( $this->pi_getPageLink ( $inputpid, '', '') ).'">';
					$markerARRAY['###FORM_AUSW_TEXT###'] = $this->pi_getLL('FORM_AUSW');
					$markerARRAY['###FORM_AUSW###'] = '<label for="'. $this->prefixId .'typ">'. $this->pi_getLL('SINGLE_TYP_DESC').'</label>
					<select name="'. $this->prefixId .'[typ]" class="tx-foportal-inputfield" id="'. $this->prefixId .'typ">
						<option value="pub">Publikation</option>
						<option value="proj">Forschungsprojekt</option>
						<option value="prof">Forscherprofil</option>
					</select>';
					$markerARRAY['###FORM_AUSW_HIDDEN###'] = '<input type="hidden" name="' . $this->prefixId . '[ausw]" value="1" />';
					$markerARRAY['###FORM_AUSW_SUBMIT###'] = '<input type="submit" name="' . $this->prefixId . '[send_form]" value="Start" class="tx-foportal-submit"/> ';
				}  	
				if($this->piVars['typ'] == 'pub' || $type == 'pub'){
					$subpart                      = $this->cObj->getSubpart($template, "###INPUT_FORM_PUB###");
						
					$markerARRAY ['###INPUT_FORM_BEGIN###'] = ''.$this->wrapMessage($message, '<div class="'.$class.'">', '</div>').'<form method="post" action="'.htmlspecialchars ( $this->pi_getPageLink ( $inputpid, '', '') ).'">';
					
					$markerARRAY['###FORM_PUB_TITEL###'] = '<label for="' . $this->prefixId . 'titel" >'. $this->pi_getLL('search.titlefield').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[titel]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'titel " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_titel').'" value="'.$this->piVars['titel'].'" />' . $this->wrapMessage($errors['titel'], '<div class="error">', '</div>') . '';
					
					$markerARRAY['###FORM_PUB_TYP###'] = '<label for="' . $this->prefixId . 'pubtyp">'. $this->pi_getLL('SINGLE_TYP_DESC').'</label>
					<select name="'. $this->prefixId . '[pubtyp]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'titel"  value="'.$this->piVars['pubtyp'].'">
						<option value="0">Monographie</option>
						<option value="1">Beitrag in Herausgeberwerk</option>
						<option value="2">Artikel</option>
					</select>';
					$markerARRAY['###FORM_PUB_AUTOR###'] = '<label for="' . $this->prefixId . 'autor" required="required">'. $this->pi_getLL('SINGLE_AUTHOR_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' . $this->prefixId . '[autor]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'autor" value="'.$this->piVars['autor'].'" /> ' . $this->wrapMessage($errors['autor'], '<div class="error">', '</div>') . '';
					$markerARRAY['###FORM_PUB_BEITRAG###'] = '<label for="' . $this->prefixId . 'beitrag_in">'. $this->pi_getLL('SINGLE_COMMENT_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[beitrag_in]"  class="tx-foportal-inputfield" id="' . $this->prefixId . 'beitrag_in" value="'.$this->piVars['beitrag_in'].'" />';
					$markerARRAY['###FORM_PUB_VERLAG###'] = '<label for="' . $this->prefixId . 'verlag">'. $this->pi_getLL('SINGLE_PUBLISHER_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[verlag]"  class="tx-foportal-inputfield" id="' . $this->prefixId . 'verlag" value="'.$this->piVars['verlag'].'"/>';
					$markerARRAY['###FORM_PUB_ORT###'] = '<label for="' . $this->prefixId . 'ort">'. $this->pi_getLL('SINGLE_PLACE_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[ort]"  class="tx-foportal-inputfield" id="' . $this->prefixId . 'ort" value="'.$this->piVars['ort'].'"/>';
					$markerARRAY['###FORM_PUB_ISBN###'] = '<label for="' . $this->prefixId . 'isbn">'. $this->pi_getLL('SINGLE_ISBN_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[isbn]"  class="tx-foportal-inputfield" id="' . $this->prefixId . '' . $this->prefixId . 'isbn" value="'.$this->piVars['isbn'].'"/>';
					$markerARRAY['###FORM_PUB_JAHR###'] = '<label for="' . $this->prefixId . 'jahr">'. $this->pi_getLL('SINGLE_YEAR_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[jahr]"  class="tx-foportal-inputfield" id="' . $this->prefixId . 'jahr" value="'.$this->piVars['jahr'].'" />';
					$markerARRAY['###FORM_PUB_AUSGABE###'] = '<label for="' . $this->prefixId . 'ausgabe">'. $this->pi_getLL('SINGLE_EDITION_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[ausgabe]"  class="tx-foportal-inputfield" id="' . $this->prefixId . 'ausgabe" value="'.$this->piVars['ausgabe'].'"/>';
					$markerARRAY['###FORM_PUB_SEITE###'] = '<label for="' . $this->prefixId . 'seiten">'. $this->pi_getLL('SINGLE_PAGE_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[seiten]"  class="tx-foportal-inputfield" id="' . $this->prefixId . 'seiten" value="'.$this->piVars['seiten'].'"/>';
					$markerARRAY['###FORM_PUB_LINK###'] = '<label for="' . $this->prefixId . 'link">'. $this->pi_getLL('SINGLE_LINK_DESC').'</label>
					<input type="text" name="' . $this->prefixId . '[link]"  class="tx-foportal-inputfield" id="' . $this->prefixId . 'link" value="'.$this->piVars['link'].'" />'. $this->wrapMessage($errors['link'], '<div class="error">', '</div>') .'';
					$markerARRAY['###FORM_PUB_ABSTRACT###'] = '<label for="' . $this->prefixId . 'abstract">'. $this->pi_getLL('SINGLE_ABSTRACT_DESC').'</label>
					<textarea  name="' . $this->prefixId . '[abstract]" class="tx-foportal-inputfield" value="'.$this->piVars['abstract'].'" id="' . $this->prefixId . 'abstract" cols="50" rows="10" >'.$this->piVars['abstract'].'</textarea>';
					
					$markerARRAY['###FORM_PUB_SUBMIT###'] = '<input type="submit" name="' . $this->prefixId . '[send_form]" value="Senden" class="tx-foportal-submit"/> <input type="reset" name="Name" value="Leeren">';
					$markerARRAY['###FORM_PUB_TAGS###'] = '<label for="' . $this->prefixId . 'tag">'. $this->pi_getLL('SINGLE_PROFILE_TAG_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_tag').'" name="' . $this->prefixId . '[tag][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'tag" >';
										
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resulttag))
					{
						$markerARRAY['###FORM_PUB_TAGS###'] .= '<option value="' . $row['uid'] . '">' . $row['tag'] . '</option>';
					}
					$markerARRAY['###FORM_PUB_TAGS###'] .= '</select>';
					
					$markerARRAY['###FORM_PUB_HIDDEN###'] = '<input type="hidden" name="' . $this->prefixId . '[typ]" value="pub" />
					<script type="text/javascript">
						$(document).ready(function(){
							jQuery(".tx-foportal-inputfield-chosen").data("placeholder","Fachbereich").chosen();
						});
					</script>';
				}
				
				if($this->piVars['typ'] == 'proj' || $type == 'proj'){
				
					/*SELECTS für Selector Boxen*/
					
				
					$subpart                      = $this->cObj->getSubpart($template, "###INPUT_FORM_PROJ###");
					$markerARRAY ['###INPUT_FORM_BEGIN###'] = ''.$this->wrapMessage($message, '<div class="'.$class.'">', '</div>').'<form method="post" action="'.htmlspecialchars ( $this->pi_getPageLink ( $inputpid, '', '') ).'">';
					
					$markerARRAY['###FORM_PROJ_TITEL###'] = '<label for="' . $this->prefixId . 'projekttitel" >'. $this->pi_getLL('SINGLE_PROJECT_TITLE_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[projekttitel]" class="tx-foportal-inputfield" value="'.$this->piVars['projekttitel'].'" id="' . $this->prefixId . 'projekttitel " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_projekttitel').'" /> ' . $this->wrapMessage($errors['projekttitel'], '<div class="error">', '</div>') . '';
					$markerARRAY['###FORM_PROJ_TYP###'] = '<label for="' . $this->prefixId . 'prjekttyp">'. $this->pi_getLL('SINGLE_PROJECT_TYPE_DESC').'<span class="tx-foportal-req">*</span></label>
					<select name="' . $this->prefixId . '[projekttyp]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'projekttyp">
						<option value="0">Forschungsprojekt</option>
						<option value="1">Entwicklungsprojekt</option>
						<option value="2">Designprojekt</option>
						<option value="3">Studentenprojekt</option>
					</select>';
					$markerARRAY['###FORM_PROJ_ABSTRACT###'] = '<label for="' . $this->prefixId . 'kurzbeschreibung">'. $this->pi_getLL('SINGLE_PROJECT_DESC_DESC').'</label>
					<textarea  name="' . $this->prefixId . '[kurzbeschreibung]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'kurzbeschreibung" cols="50" rows="10" >'.$this->piVars['kurzbeschreibung'].'</textarea>';
					$markerARRAY['###FORM_PROJ_LEITER###'] = '<label for="' . $this->prefixId . 'projektleiteranzeige" >'. $this->pi_getLL('SINGLE_PROJECT_MANAGER_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[projektleiteranzeige]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'projektleiteranzeige" value="'.$this->piVars['projektleiteranzeige'].'" required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_projektleiter').'" /> ' . $this->wrapMessage($errors['sender_mail'], '<div class="error">', '</div>') . '';
					$markerARRAY['###FORM_PROJ_LEITER###'] .= '<input type="button" value="   " id="addButton">
					<input type="button" value="   " id="removeButton">';
					$markerARRAY['###FORM_PROJ_MITARBEITER###'] = '<label for="' . $this->prefixId . 'projektmitarbeiteranzeige" >'. $this->pi_getLL('SINGLE_PROJECT_MEMBER_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[projektmitarbeiteranzeige]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'projektmitarbeiteranzeige"  placeholder="'.$this->pi_getLL('inputform.placeholder_mitarbeiter').'" />';
					$markerARRAY['###FORM_PROJ_MITARBEITER###'] .=  '<input type="button" value="   " id="addButton2">
					<input type="button" value="   " id="removeButton2">';
					$markerARRAY['###FORM_PROJ_FB###'] = '<label for="' . $this->prefixId . 'fachbereich">'. $this->pi_getLL('SINGLE_PROJECT_FACULTY_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_fb').'" name="' . $this->prefixId . '[fachbereich][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'fachbereich" >';
					
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfb))
					{
						$markerARRAY['###FORM_PROJ_FB###'] .= '<option value="' . $row['uid'] . '">' . $row['fachbereich'] . '</option>';
					}
					$markerARRAY['###FORM_PROJ_FB###'] .= '</select>';
					$markerARRAY['###FORM_PROJ_INST###'] = '<label for="' . $this->prefixId . 'institut">'. $this->pi_getLL('SINGLE_PROJECT_INSTITUTE_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_institut').'" name="' . $this->prefixId . '[institut][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'institut" >';
					
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultinst))
					{
						$markerARRAY['###FORM_PROJ_INST###'] .= '<option value="' . $row['uid'] . '">' . $row['short'] . '</option>';
					}
					$markerARRAY['###FORM_PROJ_INST###'] .= '</select>';
					$markerARRAY['###FORM_PROJ_JAHR###'] = '<label for="' . $this->prefixId . 'jahr" >'. $this->pi_getLL('SINGLE_YEAR_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[jahr]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'jahr " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_jahr').'" /> ' . $this->wrapMessage($errors['jahr'], '<div class="error">', '</div>') . '';
					$markerARRAY['###FORM_PROJ_BEGINN###'] = '<label for="' . $this->prefixId . 'projektbeginn" >'. $this->pi_getLL('SINGLE_PROJECT_BEGIN_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[projektbeginn]" class="tx-foportal-datefield" id="' . $this->prefixId . 'projektbeginn " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_beginn').'" />';
					$markerARRAY['###FORM_PROJ_ENDE###'] = '<label for="' . $this->prefixId . 'projektende" >'. $this->pi_getLL('SINGLE_PROJECT_END_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[projektende]" class="tx-foportal-datefield" id="' . $this->prefixId . 'projektende " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_ende').'" />';
					$markerARRAY['###FORM_PROJ_VOL###'] = '<label for="' . $this->prefixId . 'projektvolumen" >'. $this->pi_getLL('SINGLE_PROJECT_VOLUME_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[projektvolumen]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'projektvolumen " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_volumen').'" />';
					$markerARRAY['###FORM_PROJ_FOERDER###'] = '<label for="' . $this->prefixId . 'foerdervolumen" >'. $this->pi_getLL('SINGLE_PROJECT_FUNDING_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[foerdervolumen]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'foerdervolumen " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_foerder').'" />';
					$markerARRAY['###FORM_PROJ_GEBER###'] = '<label for="' . $this->prefixId . 'foerdermittelgeber">'. $this->pi_getLL('SINGLE_PROJECT_FUNDER_DESC').'</label>
					<textarea  name="' . $this->prefixId . '[foerdermittelgeber]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'foerdermittelgeber" cols="50" rows="10" ></textarea>';
					$markerARRAY['###FORM_PROJ_FSP###'] = '<label for="' . $this->prefixId . 'forschungsschwerpunkt">'. $this->pi_getLL('SINGLE_PROJECT_EMPHASIS_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_fsp').'" name="' . $this->prefixId . '[forschungsschwerpunkt][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'forschungsschwerpunkt" >';
										
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfsp))
					{
						$markerARRAY['###FORM_PROJ_FSP###'] .= '<option value="' . $row['uid'] . '">' . $row['forschungsschwerpunkt'] . '</option>';
					}
					$markerARRAY['###FORM_PROJ_FSP###'] .= '</select>';
					
					$markerARRAY['###FORM_PROJ_WEBSEITE###'] = '<label for="' . $this->prefixId . 'webseite" >'. $this->pi_getLL('SINGLE_PROJECT_WEBSITE_DESC').'</label>
					<input type="text" name="' .$this->prefixId .'[webseite]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'webseite" placeholder="'.$this->pi_getLL('inputform.placeholder_website').'" /> ' . $this->wrapMessage($errors['webseite'], '<div class="error">', '</div>') . '';
					
					$markerARRAY['###FORM_PROJ_TAGS###'] = '<label for="' . $this->prefixId . 'tag">'. $this->pi_getLL('SINGLE_PROFILE_TAG_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_tag').'" name="' . $this->prefixId . '[tag][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'tag" >';
										
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resulttag))
					{
						$markerARRAY['###FORM_PROJ_TAGS###'] .= '<option value="' . $row['uid'] . '">' . $row['tag'] . '</option>';
					}
					$markerARRAY['###FORM_PROJ_TAGS###'] .= '</select>';
					
					$markerARRAY['###FORM_PROJ_SUBMIT###'] = '<input type="submit" name="' . $this->prefixId . '[send_form]" value="Senden" class="tx-foportal-submit"/> <input type="reset" name="Name" value="Leeren"> ';
					
					$markerARRAY['###FORM_PROJ_HIDDEN###'] = '<input type="hidden" name="' . $this->prefixId . '[typ]" value="proj" />
					
					<script type="text/javascript">
									
									$(document).ready(function(){
										Date.format = \'dd.mm.yyyy\';
										$(\'.tx-foportal-datefield\').datePicker({startDate:\'01/01/1996\'});
									 	jQuery(".tx-foportal-inputfield-chosen").data("placeholder","Fachbereich").chosen();
										
										var counter = 2;
										var counter2 = 2;
										$("#addButton").click(function () {
									 
										if(counter>10){
												alert("Only 10 textboxes allow");
												return false;
										}   
									 
										var newTextBoxDiv = $(document.createElement(\'div\')).attr("id", \'tx_foportal-inputfield\' + counter);
									 
										newTextBoxDiv.html(\'<label for="' . $this->prefixId . 'projektleiteranzeige \'+ counter + \'" >'. $this->pi_getLL('SINGLE_PROJECT_MANAGER_DESC').' #\'+ counter + \' : </label>\' +
											  \'<input type="text" name="' .$this->prefixId .'[projektleiteranzeige\' + counter + 
											  \']" id="' . $this->prefixId . 'projektleiteranzeige\' + counter + \'" value="'.$this->piVars['projektleiteranzeige + counter +'].'" class="tx-foportal-inputfield" >\');
									 
										newTextBoxDiv.appendTo("#tx_foportal-leiter");
									 
									 
										counter++;
										 });
										
										$("#addButton2").click(function () {
									 
										if(counter2>10){
												alert("Only 10 textboxes allow");
												return false;
										}   
									 
										var newTextBoxDiv = $(document.createElement(\'div\')).attr("id", \'tx_foportal-inputfieldM\' + counter2);
									 
										newTextBoxDiv.html(\'<label for="' . $this->prefixId . 'projektmitarbeiteranzeige \'+ counter2 + \'" >'. $this->pi_getLL('SINGLE_PROJECT_MEMBER_DESC').' #\'+ counter2 + \' : </label>\' +
											  \'<input type="text" name="' .$this->prefixId .'[projektmitarbeiteranzeige\' + counter2 + 
											  \']" id="' . $this->prefixId . 'projektmitarbeiteranzeige\' + counter2 + \'" class="tx-foportal-inputfieldM" >\');
									 
										newTextBoxDiv.appendTo("#tx_foportal-mitarbeiter");
									 
									 
										counter2++;
										 });										
									 
										 $("#removeButton").click(function () {
										if(counter==1){
											  alert("No more textbox to remove");
											  return false;
										   }   
									 
										counter--;
									 
											$("#tx_foportal-inputfield" + counter).remove();
									 
										 });
									 
									 $("#removeButton2").click(function () {
										if(counter2==1){
											  alert("No more textbox to remove");
											  return false;
										   }   
									 
										counter2--;
									 
											$("#tx_foportal-inputfieldM" + counter2).remove();
									 
										 });
									 
										 $("#getButtonValue").click(function () {
									 
										var msg = \'\';
										for(i=1; i<counter; i++){
										  msg += "\n Textbox #" + i + " : " + $(\'#textbox\' + i).val();
										}
											  alert(msg);
										 });
									  });
									</script>';
				}
				
				if($this->piVars['typ'] == 'prof' || $type == 'prof'){
				
					$subpart                      = $this->cObj->getSubpart($template, "###INPUT_FORM_PROF###");
					$markerARRAY ['###INPUT_FORM_BEGIN###'] = ''.$this->wrapMessage($message, '<div class="'.$class.'">', '</div>').'<form method="post" action="'.htmlspecialchars ( $this->pi_getPageLink ( $inputpid, '', '') ).'">';
					
					$markerARRAY['###FORM_PROF_NAME###'] = '<label for="' . $this->prefixId . 'name" >'. $this->pi_getLL('SINGLE_PROFILE_NAME_DESC').'<span class="tx-foportal-req">*</span></label>
					<input type="text" name="' .$this->prefixId .'[name]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'name " required="required" placeholder="'.$this->pi_getLL('inputform.placeholder_name').'" /> ' . $this->wrapMessage($errors['name'], '<div class="error">', '</div>') . '';
					$markerARRAY['###FORM_PROF_FUNKTION###'] = '<label for="' . $this->prefixId . 'funktion" >'. $this->pi_getLL('SINGLE_PROFILE_FUNKTION_DESC').'</label>
					<input type="text" name="' .$this->prefixId .'[funktion]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'funktion " placeholder="'.$this->pi_getLL('inputform.placeholder_funktion').'" />';
					$markerARRAY['###FORM_PROF_INST###'] = '<label for="' . $this->prefixId . 'institute">'. $this->pi_getLL('SINGLE_PROFILE_INSTITUTE_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_institut').'" name="' . $this->prefixId . '[institute][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'institute" >';
					
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultinst))
					{
						$markerARRAY['###FORM_PROF_INST###'] .= '<option value="' . $row['uid'] . '">' . $row['short'] . '</option>';
					}
					$markerARRAY['###FORM_PROF_INST###'] .= '</select>';
					$markerARRAY['###FORM_PROF_FB###'] = '<label for="' . $this->prefixId . 'fachbereich">'. $this->pi_getLL('SINGLE_PROFILE_FACULTY_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_fb').'" name="' . $this->prefixId . '[fachbereich][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'fachbereich" >';
					
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfb))
					{
						$markerARRAY['###FORM_PROF_FB###'] .= '<option value="' . $row['uid'] . '">' . $row['fachbereich'] . '</option>';
					}
					$markerARRAY['###FORM_PROF_FB###'] .= '</select> ' . $this->wrapMessage($errors['fachbereich'], '<div class="error">', '</div>') . '';
					
					$markerARRAY['###FORM_PROF_FSP###'] = '<label for="' . $this->prefixId . 'forschungsschwerpunkte">'. $this->pi_getLL('SINGLE_PROFILE_EMPHASIS_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_fsp').'" name="' . $this->prefixId . '[forschungschwerpunkte][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'forschungsschwerpunkte" >';
										
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfsp))
					{
						$markerARRAY['###FORM_PROF_FSP###'] .= '<option value="' . $row['uid'] . '">' . $row['forschungsschwerpunkt'] . '</option>';
					}
					$markerARRAY['###FORM_PROF_FSP###'] .= '</select> ' . $this->wrapMessage($errors['forschungsschwerpunkte'], '<div class="error">', '</div>') . '';
					$markerARRAY['###FORM_PROF_IND_FSP###'] = '<label for="' . $this->prefixId . 'ind_forschungsschwerpunkte" >'. $this->pi_getLL('SINGLE_PROFILE_INDIVIDUAL_EMPHASIS_DESC').'</label>
					<input type="text" name="' .$this->prefixId .'[ind_forschungsschwerpunkte]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'ind_forschungsschwerpunkte "  placeholder="'.$this->pi_getLL('inputform.placeholder_titel').'" />';
					$markerARRAY['###FORM_PROF_IND_FSP###'] .= '<input type="button" value="   " id="addButton1">
					<input type="button" value="   " id="removeButton1">';
					$markerARRAY['###FORM_PROF_MITGLIED###'] = '<label for="' . $this->prefixId . 'mitgliedschaften">'. $this->pi_getLL('SINGLE_PROFILE_MEMBERSHIPS_DESC').'</label>
					<textarea  name="' . $this->prefixId . '[mitgliedschaften]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'mitgliedschaften" cols="50" rows="10" ></textarea>';
					$markerARRAY['###FORM_PROF_AUSST###'] = '<label for="' . $this->prefixId . 'austattung">'. $this->pi_getLL('SINGLE_PROFILE_INPUT_AUSS').'</label>
					<textarea  name="' . $this->prefixId . '[austattung]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'austattung" cols="50" rows="10" ></textarea>';
					$markerARRAY['###FORM_PROF_KOOP###'] = '<label for="' . $this->prefixId . 'kooperationen">'. $this->pi_getLL('SINGLE_PROFILE_COOPERATIONS_DESC').'</label>
					<textarea  name="' . $this->prefixId . '[kooperationen]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'kooperationen" cols="50" rows="10" ></textarea>';
					$markerARRAY['###FORM_PROF_PREISE###'] = '<label for="' . $this->prefixId . 'preise">'. $this->pi_getLL('SINGLE_PROFILE_AWARDS_DESC').'</label>
					<textarea  name="' . $this->prefixId . '[preise]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'preise" cols="50" rows="10" ></textarea>';
					$markerARRAY['###FORM_PROF_LINKS###'] = '<label for="' . $this->prefixId . 'links" >'. $this->pi_getLL('SINGLE_PROFILE_LINK_DESC').'</label>
					<input type="text" name="' .$this->prefixId .'[links]" class="tx-foportal-inputfield" id="' . $this->prefixId . 'links "  placeholder="'.$this->pi_getLL('inputform.placeholder_titel').'" />';
					$markerARRAY['###FORM_PROF_LINKS###'] .= '<input type="button" value="   " id="addButton2">
					<input type="button" value="   " id="removeButton2">';
					$markerARRAY['###FORM_PROF_TAG###'] = '<label for="' . $this->prefixId . 'tag">'. $this->pi_getLL('SINGLE_PROFILE_TAG_DESC').'</label>
					<select data-placeholder="'.$this->pi_getLL('inputform.placeholder_fsp').'"' . $this->prefixId . '[tag][]" multiple class="tx-foportal-inputfield-chosen" id="' . $this->prefixId . 'tag" >';
										
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resulttag))
					{
						$markerARRAY['###FORM_PROF_TAG###'] .= '<option value="' . $row['uid'] . '">' . $row['tag'] . '</option>';
					}
					$markerARRAY['###FORM_PROF_TAG###'] .= '</select>';
					
					$markerARRAY['###FORM_PROF_SUBMIT###'] = '<input type="submit" name="' . $this->prefixId . '[send_form]" value="Senden" class="tx-foportal-submit"/> <input type="reset" name="Name" value="Leeren"> ';
					
					$markerARRAY['###FORM_PROF_HIDDEN###'] = '<input type="hidden" name="' . $this->prefixId . '[typ]" value="prof" />
					
					<script type="text/javascript">
									
									$(document).ready(function(){
										Date.format = \'dd.mm.yyyy\';
										$(\'.tx-foportal-datefield\').datePicker({startDate:\'01/01/1996\'});
									 	jQuery(".tx-foportal-inputfield-chosen").data("placeholder","Fachbereich").chosen();
										
										var counter = 2;
										var counter2 = 2;
										$("#addButton1").click(function () {
									 
										if(counter>6){
												alert("Maximal 6 Eingaben erlaubt");
												return false;
										}   
									 
										var newTextBoxDiv = $(document.createElement(\'div\')).attr("id", \'tx_foportal-inputfield\' + counter);
									 
										newTextBoxDiv.html(\'<label for="' . $this->prefixId . 'ind_forschungsschwerpunkte \'+ counter + \'" >'. $this->pi_getLL('SINGLE_PROFILE_INDIVIDUAL_EMPHASIS_DESC').' #\'+ counter + \': </label>\' +
											  \'<input type="text" name="' .$this->prefixId .'[ind_forschungsschwerpunkte\' + counter + 
											  \']" id="' . $this->prefixId . 'ind_forschungsschwerpunkte\' + counter + \'" class="tx-foportal-inputfield" >\');
									 
										newTextBoxDiv.appendTo("#tx_foportal-ind_fsp");
									 
									 
										counter++;
										 });
										
										$("#addButton2").click(function () {
									 
										if(counter2>10){
												alert("Maximal 6 Eingaben erlaubt");
												return false;
										}   
									 
										var newTextBoxDiv = $(document.createElement(\'div\')).attr("id", \'tx_foportal-inputfield\' + counter2);
									 
										newTextBoxDiv.html(\'<label for="' . $this->prefixId . 'links \'+ counter2 + \'" >'. $this->pi_getLL('SINGLE_PROFILE_LINK_DESC').' #\'+ counter2 + \': </label>\' +
											  \'<input type="text" name="' .$this->prefixId .'[links\' + counter2 + 
											  \']" id="' . $this->prefixId . 'links\' + counter2 + \'" class="tx-foportal-inputfield" >\');
									 
										newTextBoxDiv.appendTo("#tx_foportal-links");
									 
									 
										counter2++;
										 });										
									 
										 $("#removeButton1").click(function () {
										if(counter==1){
											  alert("Keine weiteren Felder zum entfernen vorhanden");
											  return false;
										   }   
									 
										counter--;
									 
											$("#tx_foportal-inputfield" + counter).remove();
									 
										 });
									 
									 $("#removeButton2").click(function () {
										if(counter2==1){
											  alert("Keine weiteren Felder zum entfernen vorhanden");
											  return false;
										   }   
									 
										counter2--;
									 
											$("#tx_foportal-inputfield" + counter2).remove();
									 
										 });
									 
										 $("#getButtonValue").click(function () {
									 
										var msg = \'\';
										for(i=1; i<counter; i++){
										  msg += "\n Textbox #" + i + ": " + $(\'#textbox\' + i).val();
										}
											  alert(msg);
										 });
									  });
									</script>';
					
				}
						
						
						
						
					
					
					/*if(1){
						
						$this->postvars ['typ'] = 'pub';
						if (isset ( $this->postvars ['submitted'] ) && $this->postvars ['submitted'] == 1) { //hiddenFeld in tmpl immer submitted
							
							foreach ( $this->postvars as $key => $value ) {
								$value = $this->local_cObj->removeBadHTML ( $value, array () );
								$this->postvars [$key] = $value;
							}
							
							foreach ( $this->postvars as $k => $v ) {
								$werteArray ['VALUE_' . strtoupper ( $k ) ] = stripslashes ( $v );
							}
							
							//$error = $this->checkForm (); //Fehlerüberprüfung folgt
							$error = 1;
							if ($error != 1) {
							$markerArray ['###FORM_ERROR###'] = $this->pi_getLL ( 'form_error' );
							$markerArray ['###FORM_ERROR_FIELDS###'] = $error;
							}
							else{
								$db_fields = array ('titel', 'typ', 'autor', 'beitrag_in', 'verlag', 'ort', 'isbn', 'jahr', 'ausgabe', 'seiten', 'link', 'abstract');
								$saveData ['uid'] = '';
								$saveData ['pid'] = $this->config ['pid_list'];
								$saveData ['tstamp'] = time ();
								$saveData ['crdate'] = time ();
								$saveData ['deleted'] = '0';
								$saveData ['hidden'] = '1';
								$saveData ['klicks'] = '0';
								
								foreach ( $this->postvars as $k => $v ) {
									if (in_array ( $k, $db_fields )) {
										
										 if ($this->config ['allowedTags']) { // Blacklist Funktion
											$v = strip_tags ( $v, $this->config ['allowedTags'] );
										} 
										
										$saveData [$k] = $this->local_cObj->removeBadHTML ( $v, array () );
									}
								}
								
								$insert = $GLOBALS ['TYPO3_DB']->exec_INSERTquery ( 'tx_foportal_publikationen', $saveData );
							}
						}
						if ($insert) {
						
							 if (! empty ( $this->config ['notify_mail'] )) {
								$this->sendNotificationMail ( $this->config ['notify_mail'] );
							} 
						
						}
						
						
						
						
						
						
						
						
					}*/
					
					
					

					$inputform .= $this->cObj->substituteMarkerArray($subpart, $markerARRAY);
					return $inputform;
    
				}
				
				function wrapMessage($message, $prefix, $suffix) {
					if(trim($message) != '') {
						return $prefix . $message . $suffix;
					}
					else {
						return '';
					}
				}
				
				function isURL($url) {
					if (! preg_match ( '#^http\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url )) {
						return false;
					} else {
						return true;
					}
				}
				
				function getPersonElement($id) {
					$data = $array;
					$where = ' uid = '.$id;
					
					$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*",$this->personTable,$where);
					while($row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res)){
						$data = $row;
					}
					return $data;
				}
				
			 // shows a person
				function showPersonElement($id) {
				   
					$person = $this->getPersonElement($id);
					
					$imagePath = 'uploads/tx_dmiwpersonen/';
					//$imagePath = 'uploads/tx_txpersinfotest/';
					
					if($person['description']) {
						$description = ', '.$person['description'];
					}
					$name = $person['title'].' '.$person['firstname'].' '.$person['name'].$description;
					
					if($person['more_information']){
						$headline = '<h2 id="person_'.$person['uid'].'" class="name"><a class="tx-iwpersonen-pi1-detaillink" href="'.$this->pi_getPagelink($person['more_information']).'" title="'.$this->pi_getLL('more').' '.$person['title'].' '.$person['firstname'].' '.$person['name'].'">'.$person['title'].' '.$person['firstname'].' '.$person['name'].$description.'</a></h2>';
					}
					else{
						$headline = '<h2 id="person_'.$person['uid'].'">'.$person['title'].' '.$person['firstname'].' '.$person['name'].$description.'</h2>';		
					}
							
					if($person['room'] != '') {
						$room = '<span class="font-size-11"><br /><strong>Raum: '.$person['room'].'</strong></span><br />';
					}
					else {
						$room = '';
					}
					
					 if($person['image']) $imageArray = getimagesize($this->getImageResource($person['image'],130));
					
					//$altText = ($this->getAltText($imagePath,$person['image'])?$this->getAltText($imagePath,$array['image']):$person['name']);
					$altText = 'Bild: '.$person['name'];
					 if($person['image'] != '') {
						if($person['more_information']){
							$image = '<a class="tx-iwpersonen-pi1-detaillink_image" href="'.$this->pi_getPagelink($person['more_information']).'" >'.$this->getImage($imagePath.$person['image'], 130, '', $altText).'</a>';
						}
						else{
							$image = $this->getImage($imagePath.$person['image'], 130, '', $altText);	
						}
					}
					else {
						  $image = '';
					} 
						//$image = '';   
						debug($image,'hier bild');
					$times = $this->renderAddInfos($person);
				   //$times ='';
					$out = '';
					$out .= '<div class="tx-iwpersonen-pi1-item-box">	
								<div class="tx-iwpersonen-pi1-container">
									'.$headline.'
									<span class="tx-iwpersonen-pi1-paragraph">'.$person['name'].'</span> 
									'.$times.$room;
									if($person['email'] = '') $out .= '<span class="font-size-11">E-Mail:</span> '.$this->renderMail($person['email']);              		
				   $out .=     '</div>
								<div class="tx-iwpersonen-pi1-container-image">
								   '.$image.'<br />
								</div>	 
							</div>
					<div class="clearer">&nbsp;</div>' ;
					debug($person,'perselementerg');
					return $out;
				}
				
				// renders an e-mail address
				function renderMail($email){
					if($email){
						$validEmail = str_replace('@', '(at)', trim($email)); 
						$content = '<a class="font-size-11" href="'.$this->pi_getPageLink($email).'" title="E-Mail to '.$validEmail.'">'.$validEmail.'</a>';
					}
					else{
						$content = '';
					}
					return $content;
				}
				
				
				// gets an image resource
				function getImageResource($file,$width,$path=''){
					if(!$path)$path = 'uploads/tx_dmiwpersonen/';
					//if(!$path)$path = 'uploads/tx_txpersinfotest/';
					if(is_file($path.$file)){
						$img['file'] = $path.$file;
						$img['file.']['width'] = $width;
						//debug($img,'image');
						return $this->cObj->IMG_RESOURCE($img);
					}
				}
							/**
				 * The getImage function
				 * 
				 * renders and displays an image
				 *
				 * @param	string		$file: the image file
				 * @param	int			$width: the image file
				 * @param	string		$params: the params for the image
				 * @param	string		$altText: the alternative text
				 * @return	the image
				 */	
				function getImage($file, $width, $params='', $altText=''){	

					$img['file'] = $file;
					$img['file.']['width'] = $width;
					$img['params'] = $params;
					$img['altText'] = $altText;
					$img['titleText'] = $altText;	
								
					return $this->cObj->IMAGE($img);			
				}
				
				    // gets an alternative text for an image
					function getAltText($filePath, $fileName) {  
						// Reading meta data from DAM-table  
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('alt_text', 'tx_dam as d, tx_dam_file_tracking as f',"f.file_name='" . $fileName . "' and f.file_path='" . $filePath . "' and f.file_hash = d.file_hash", '', '', 1);
				 
						$imgAltText = '';        
					   
						if($res) {
							$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
							$imgAltText = $row['alt_text'];
					   }  
						
					   return $imgAltText;   
					}
					
					// renders additional information for a person
					function renderAddInfos($infos){
						
						$content = '';
						if($this->hasValue($infos['street'])){
							$content .= '<p>'.$infos['street'].'</p>';
						}
						if($this->hasValue($infos['city'])){
							$content .= '<p>'.$infos['plz'].' '.$infos['city'].'</p>';
						}
						debug($content,'render');
						if($this->hasValue($infos['phone'])){
							$content .= '<p>'.$infos['phone'].'</p>';
						}
						if($this->hasValue($infos['fax'])){
							$content .= '<p>'.$infos['fax'].'</p>';
						}
						if($this->hasValue($infos['mobil'])){
							$content .= '<p>'.$infos['mobil'].'</p>';
						}
						if($this->hasValue($infos['private'])){
							$content .= '<p>'.$infos['private'].'</p>';
						}
						if($this->hasValue($infos['consultation_hours'])){
							$content .= '<p>'.$infos['consultation_hours'].'</p>';
						}
						
						return $content;	
					}
					
					 // checks value of an input field
					function hasValue($item){
						if(strlen($item) >0){
							return true;
						}
						else{
							return false;
						}		
					}
				/**
	 * After submitting a new entry you can activate a notification mail to remind the admin
	 *
	 * @param	string		$emailto: E-Mail recipient
	 * @return	boolean		Mail delivery: true / false
	 */
		function sendNotificationMail($emailto) {
			$notification_mail_subject = $this->pi_getLL ( 'notification_mail_subject' );
			$notification_mail_text = $this->pi_getLL ( 'notification_mail_text' );
			
			$markerArray ['###SERVER_NAME###'] = $_SERVER ['SERVER_NAME'];
			$markerArray ['###URL###'] = t3lib_div::getIndpEnv ( 'TYPO3_SITE_URL' ) . $this->getUrl ( $this->config ['guestbook'] );
			
			if (is_array ( $this->postvars )) {
				foreach ( $this->postvars as $k => $v ) {
					$markerArray ['###' . strtoupper ( $k ) . '###'] = stripslashes ( $v );
				}
				
				$notification_mail_subject = $this->cObj->substituteMarkerArrayCached ( $notification_mail_subject, $markerArray, array (), array () );
				$notification_mail_text = $this->cObj->substituteMarkerArrayCached ( $notification_mail_text, $markerArray, array (), array () );
				
				$emailfrom_name = $this->getEmailFromName ();
				$emailfrom = $this->getEmailFromMail ();
				
				return t3lib_div::plainMailEncoded ( $emailto, $notification_mail_subject, $notification_mail_text, "From: " . $emailfrom_name . " <" . $emailfrom . ">\r\nReply-To: " . $emailfrom );
			}
		}


	}
	
if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/foportal/pi1/class.tx_foportal_pi1.php']))
	{
		include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/foportal/pi1/class.tx_foportal_pi1.php']);
	}
?>
