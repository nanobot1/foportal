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

// require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once('t3lib/class.t3lib_div.php');

/**
 * Plugin 'Research Portal' for the 'foportal' extension.
 *
 * @author	Dennis & Marc Lange <marc.lange@stud.hn.de>
 * @package	TYPO3
 * @subpackage	tx_foportal
 */
class tx_foportal_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_foportal_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_foportal_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'foportal';	// The extension key.
	//public $pi_checkCHash = TRUE;
	var $ffdata;
    var $singlepageID;
    var $showSearchForm = 0;
	var $standardTemplate = 'typo3conf/ext/foportal/pi1/template.tmpl';
	var $cssFile = 'typo3conf/ext/foportal/pi1/static/style.css';
	var $jsFile = 'typo3conf/ext/foportal/pi1/static/jsFile.js';
	//var $cssFile = t3lib_extMgm::siteRelPath('foportal').'pi1/static/style.css';
	/**
	 * Main method of your Plugin.
	 *
	 * @param string $content The content of the Plugin
	 * @param array $conf The Plugin Configuration
	 * @return string The content that should be displayed on the website
	 */
	public function main($content, array $conf) {
		$GLOBALS['TSFE']->set_no_cache(); //Cache abschalten
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1; //Soll Caching deaktivieren
		$this->pi_initPIflexForm();
		
		debug($this->cssFile,'CSS:');
		debug($this->jsFile,'java:');
		
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<link rel="stylesheet" href="'.$this->cssFile.'" type="text/css" />';
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = '<script type="text/javascript" src="'.$this->jsFile.'" language="JavaScript"></script>';
		//Flexform Array laden (wird in ext_tables eingebunden)
		$this->ffdata = $this->cObj->data['pi_flexform'];
		//debug($cssFile,'CSS:');
		
		//Page ID der Einzelansicht aus Flexform holen
		$this->singlepageID = $this->pi_getFFValue($this->ffdata,'singlepid','sOtherSettings');
		
		//Soll Suchfeld angezeigt werden (kann bald raus)
        $this->showSearchForm = $this->pi_getFFValue($this->ffdata,'showsearchform','sDEF');
		
		//Ansichts Modus /Einzelansicht/Listenansicht/Nur Suche/
		$viewmode = $this->pi_getFFValue($this->ffdata,'viewtype','sDEF');
		
		// Anhand der Variable "viemode" wird die Ausgabe generiert
		switch($viewmode) {
            case 'NONE': $content .= "View mode not configured yet.";
				break;
            case 'LIST': $content .= $this->generateListView();
                break;
			case 'SINGLE': $content .= $this->generateSingleView();
				break;
			case 'ORDER': $content .= $this->generateOrderView();
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
	 
	function generateSearchView() {
		//Parameter werden gelesen
		$parameter = t3lib_div::_GET($this->prefixId);
        $url=$this->pi_getPageLink($GLOBALS['TSFE']->id); //Seitenid wird in url gespeichert
		
		
		//Überprüfung ob Parameter übergeben wurde
		if($parameter == NULL){
			$all = array( $this->prefixId => array( 'mode' => 'all', 'suche' => $this->piVars['search'], 'feld' => $this->piVars['field']));
			$publication = array( $this->prefixId => array( 'mode' => 'publ', 'suche' => $this->piVars['search'], 'feld' => $this->piVars['field']));
			$researcher = array( $this->prefixId => array( 'mode' => 'researcher', 'suche' => $this->piVars['search'], 'feld' => $this->piVars['field']));
			$projects = array( $this->prefixId => array( 'mode' => 'projects', 'suche' => $this->piVars['search'], 'feld' => $this->piVars['field']));
		}
		else{
			$all = array( $this->prefixId => array( 'mode' => 'all', 'suche' => $parameter['suche'], 'feld' => $parameter['feld']));
			$publication = array( $this->prefixId => array( 'mode' => 'publ', 'suche' => $parameter['suche'], 'feld' => $parameter['feld']));
			$researcher = array( $this->prefixId => array( 'mode' => 'researcher', 'suche' => $parameter['suche'], 'feld' => $parameter['feld']));
			$projects = array( $this->prefixId => array( 'mode' => 'projects', 'suche' => $parameter['suche'], 'feld' => $parameter['feld']));
		}		
		 
		 
		debug($parameter,'Parameter:');
		//debug($this->piVars['search'],'Suche:');
		// Template laden
               
        if (isset($this->conf["templateFile"]))
            $template = $this->cObj->fileResource($this->conf["templateFile"]);
        else
			$template = $this->cObj->fileResource($this->standardTemplate);

			
		// Subpart aus Template laden
		$subpart = $this->cObj->getSubpart($template,"###SEARCH_TEMPLATE###");
	
	    // set marker replacements -- Suchformular wird erstellt
		
		$markerARRAY['###SEARCH_TITLE###'] = $this->pi_getLL('search');
		$markerARRAY['###SEARCH_BEGIN_FORM###']= '<form method="POST" action="'.$url.'">';
		$markerARRAY['###SEARCH_SEARCH_FOR_DESC###'] = $this->pi_getLL('search.searchfor');
		$markerARRAY['###SEARCH_SEARCH_WHAT_DESC###'] = $this->pi_getLL('search.searchwhat');
		//$markerARRAY['###SEARCH_SEARCH_IN_DESC###']  = $this->pi_getLL('search.infield');         
		$markerARRAY['###SEARCH_SEARCH_NAME_DESC###']  = $this->pi_getLL('search.inname'); 
		$markerARRAY['###SEARCH_SEARCH_FB_DESC###']  = $this->pi_getLL('search.infb'); 
		$markerARRAY['###SEARCH_SEARCH_FSP_DESC###']  = $this->pi_getLL('search.infsp'); 
		$markerARRAY['###SEARCH_SEARCH_YEAR_DESC###']  = $this->pi_getLL('search.inyear');
		
			

		$markerARRAY['###SEARCH_SEARCH_FOR_FIELD###'] = '<input name="'.$this->prefixId.'[search]" type="text" />';
		$markerARRAY['###SEARCH_SEARCH_ALL_FIELD###'] = $this->pi_linkToPage($this->pi_getLL('search.all'),$GLOBALS['TSFE']->id,'',$all);
		$markerARRAY['###SEARCH_SEARCH_RESEARCHER_FIELD###'] = $this->pi_linkToPage($this->pi_getLL('search.researcher'),$GLOBALS['TSFE']->id,'',$researcher);
		$markerARRAY['###SEARCH_SEARCH_PROJECTS_FIELD###'] = $this->pi_linkToPage($this->pi_getLL('search.projects'),$GLOBALS['TSFE']->id,'',$projects);
		$markerARRAY['###SEARCH_SEARCH_PUBLICATIONS_FIELD###'] = $this->pi_linkToPage($this->pi_getLL('search.publications'),$GLOBALS['TSFE']->id,'',$publication); 
		
		
		$resultnamen = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,name','tx_foportal_profile','name!="" AND deleted="0"','','name');
		$resultfb = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,fachbereich','tx_foportal_fachbereiche','fachbereich!="" AND deleted="0"','','fachbereich');
		$resultfsp = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,forschungsschwerpunkt','tx_foportal_forschungsschwerpunkte','forschungsschwerpunkt!="" AND deleted="0"','','forschungsschwerpunkt');
		
		//Die Jahreszahlen zum Filtern werden aus den Projekten und Publikationen ermittelt und über "UNION" zusammengefasst.
		$sqlJahr1 = 'SELECT DISTINCT tx_foportal_publikationen.jahr  FROM tx_foportal_publikationen';
		$sqlJahr2 = 'SELECT DISTINCT tx_foportal_projekte.jahr  FROM tx_foportal_projekte';
		$sqlJahrAll = '(' . $sqlJahr1 . ') UNION (' . $sqlJahr2 . ')';
		$sqlJahrAll .= 'ORDER BY Jahr DESC' ;
		$resultjahr = $GLOBALS['TYPO3_DB']->sql_query($sqlJahrAll);
		
		

		
		$markerARRAY['###SEARCH_SEARCH_NAME_FIELD###'] = '<select name="'.$this->prefixId.'[name]" >'.
        '<option value="0">'.$this->pi_getLL('search.allname').'</option>';
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultnamen)) {
             $markerARRAY['###SEARCH_SEARCH_NAME_FIELD###'].='<option value="'.$row['name'].'">'.$row['name'].'</option>';
        }
		$markerARRAY['###SEARCH_SEARCH_NAME_FIELD###'].='</select>';
		
		$markerARRAY['###SEARCH_SEARCH_FB_FIELD###'] = '<select name="'.$this->prefixId.'[fb]" >'.
        '<option value="0">'.$this->pi_getLL('search.allname').'</option>';
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfb)) {
             $markerARRAY['###SEARCH_SEARCH_FB_FIELD###'].='<option value="'.$row['fachbereich'].'">'.$row['fachbereich'].'</option>';
        }
		$markerARRAY['###SEARCH_SEARCH_FB_FIELD###'].='</select>';
		
		$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD###'] = '<select name="'.$this->prefixId.'[year]" >'.
        '<option value="0">'.$this->pi_getLL('search.allyear').'</option>';
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultjahr)) {
			$i = 1;
            $markerARRAY['###SEARCH_SEARCH_YEAR_FIELD###'].='<option value="'.$row['jahr'].'">'.$row['jahr'].'</option>';
			$i += 1;
        }
		$markerARRAY['###SEARCH_SEARCH_YEAR_FIELD###'].='</select>';
		
		$markerARRAY['###SEARCH_SEARCH_FSP_FIELD###'] = '<select name="'.$this->prefixId.'[fsp]" >'.
        '<option value="0">'.$this->pi_getLL('search.allname').'</option>';
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultfsp)) {
             $markerARRAY['###SEARCH_SEARCH_FSP_FIELD###'].='<option value="'.$row['forschungsschwerpunkt'].'">'.$row['forschungsschwerpunkt'].'</option>';
        }
		$markerARRAY['###SEARCH_SEARCH_FSP_FIELD###'].='</select>';
		
		$markerARRAY['###SEARCH_SUBMIT###'] = '<input type="submit" value="'.$this->pi_getLL('search.submit').'"/>';
		$markerARRAY['###SEARCH_END_FORM###'] = '</form>';
		
		//subsitute
		$content .= $this->cObj->substituteMarkerArray($subpart,$markerARRAY);
	
	
             
             
       return $content;

	}
	


	/**
	 * function generateListView
	 *
	 * Führt Sql Anfragen anhand der Suchparameter aus.
	 * 
	 */
	 
	 function generateListView() {
		
		$counter = 0;
		$parameter = t3lib_div::_GET($this->prefixId);
		
		
		
		//else echo '<script type="text/javascript">alert("Hat nicht geklappt");</script>';
        
		if ($this->showSearchForm == 1)
             $content .= $this->generateSearchView(); //Suchleiste wird erstellt
			 
		if ($parameter['mode'] == 'all'){
			
			$this->piVars['search'] = $parameter['suche'];
			
			
			
			$whereclpub = 'MATCH(tx_foportal_publikationen.titel,tx_foportal_publikationen.abstract) AGAINST ("'.$parameter['suche'].'") AND tx_foportal_publikationen.deleted="0"';
			$whereclres = 'MATCH(tx_foportal_profile.name,tx_foportal_profile.ind_forschungsschwerpunkte,tx_foportal_profile.austattung,tx_foportal_profile.referenzprojekte,tx_foportal_profile.kooperationen) AGAINST ("'.$parameter['suche'].'") AND tx_foportal_profile.deleted="0"';
			$whereclpro = 'MATCH(tx_foportal_projekte.projekttitel,tx_foportal_projekte.kurzbeschreibung,tx_foportal_projekte.foerdermittelgeber) AGAINST ("'.$parameter['suche'].'") AND tx_foportal_projekte.deleted="0"';
			
			$research = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_profile',$whereclres);
			$pub = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_publikationen ',$whereclpub);
			$projects = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_projekte ',$whereclpro);
			
			$counterpub += $GLOBALS['TYPO3_DB']->sql_num_rows($publ);
			$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($research);
			$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projects);	
			$counter = $counterpub + $counterres + $counterpro;
			if($counter == 0){
				$content .= $this->pi_getLL('list.noentries');
			}
			else{
			$content .= $counterpub.' '. $this->pi_getLL('list.pubentries') .' <br />';
			$content .= $counterres.' '. $this->pi_getLL('list.resentries') .' <br />';
			$content .= $counterpro.' '. $this->pi_getLL('list.proentries') .' <br />';			//Anzahl der gefundenen Einträge wird ausgegeben
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($publ) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($publ)) 
				$entry[] = $row;  
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($research) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($research)) 
				$entry[] = $row;  
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($projects) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projects)) 
				$entry[] = $row;  
			}
	        
			//Array mit Publikationen werden an Funktion showList übergeben. Ergebnis von showList wird an content gehängt
			if ($counter > 0)			
			$content .= $this->showList($entry); 
			
			return $content;
			
			
		}
		
		if ($parameter['mode'] == 'publ'){
			
			$this->piVars['search'] = $parameter['suche'];
			
			
			
			$whereclpub = 'MATCH(tx_foportal_publikationen.titel,tx_foportal_publikationen.abstract) AGAINST ("'.$parameter['suche'].'") AND tx_foportal_publikationen.deleted="0"';
			$publ = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_publikationen ',$whereclpub);
			$counterpub += $GLOBALS['TYPO3_DB']->sql_num_rows($publ);
				
			$counter = $counterpub;
			if($counter == 0){
				$content .= $this->pi_getLL('list.noentries');
			}
			else{
			$content .= $counterpub.' '. $this->pi_getLL('list.pubentries') .' <br />';
						//Anzahl der gefundenen Einträge wird ausgegeben
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($publ) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($publ)) 
				$entry[] = $row;  
			}
			
			if ($counter > 0)			
			$content .= $this->showList($entry);
			return $content;
			
		}
		
		if ($parameter['mode'] == 'researcher'){
			
			$this->piVars['search'] = $parameter['suche'];
			
			
			
			$whereclres = 'MATCH(tx_foportal_profile.name,tx_foportal_profile.ind_forschungsschwerpunkte,tx_foportal_profile.austattung,tx_foportal_profile.referenzprojekte,tx_foportal_profile.kooperationen) AGAINST ("'.$parameter['suche'].'") AND tx_foportal_profile.deleted="0"';
			$researcher = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_profile',$whereclres);
			$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($researcher);
				
			$counter = $counterres;
			if($counter == 0){
				$content .= $this->pi_getLL('list.noentries');
			}
			else{
			$content .= $counterres.' '. $this->pi_getLL('list.resentries') .' <br />';
						//Anzahl der gefundenen Einträge wird ausgegeben
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($researcher) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($researcher)) 
				$entry[] = $row;  
			}
			
			if ($counter > 0)			
			$content .= $this->showList($entry);
			return $content;
			
		}
		
		
		if ($parameter['mode'] == 'projects'){
			
			$this->piVars['search'] = $parameter['suche'];
						
			
			$whereclpro = 'MATCH(tx_foportal_projekte.projekttitel,tx_foportal_projekte.kurzbeschreibung,tx_foportal_projekte.foerdermittelgeber) AGAINST ("'.$parameter['suche'].'") AND tx_foportal_projekte.deleted="0"';
			$projects = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_projekte',$whereclpro);
			$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projects);
				
			$counter = $counterpro;
			if($counter == 0){
				$content .= $this->pi_getLL('list.noentries');
			}
			else{
			$content .= $counterpro.' '. $this->pi_getLL('list.proentries') .' <br />';
						//Anzahl der gefundenen Einträge wird ausgegeben
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($projects) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projects)) 
				$entry[] = $row;  
			}
			
			if ($counter > 0)			
			$content .= $this->showList($entry);
			
			return $content;
			
		}
		
		//Kein Mode Gesetzt (Wie All)
		/*else{
			$parameter['mode'] = 'all';
			/* $whereclpub = 'tx_foportal_publikationen.deleted="0"'; //ansonsten ist Where-Klausel nur where deleted = 0
			$whereclres = 'tx_foportal_profile.deleted="0"';
			$whereclpro = 'tx_foportal_projekte.deleted="0"'; 
             
			//WHERE Klausel für Suchstring wird erstellt
			if ($this->piVars['search'] != '') {
		  	    
				$whereclpub .= ' AND MATCH(tx_foportal_publikationen.titel,tx_foportal_publikationen.abstract) AGAINST ("'.$this->piVars['search'].'") ';
				$whereclres .= ' AND MATCH(tx_foportal_profile.name,tx_foportal_profile.ind_forschungsschwerpunkte,tx_foportal_profile.austattung,tx_foportal_profile.referenzprojekte,tx_foportal_profile.kooperationen) AGAINST ("'.$this->piVars['search'].'") ';
				$whereclpro .= ' AND MATCH(tx_foportal_projekte.projekttitel,tx_foportal_projekte.kurzbeschreibung,tx_foportal_projekte.foerdermittelgeber) AGAINST ("'.$this->piVars['search'].'") ';
			
				
            } 
			 if ($this->piVars['name'] != '0'){
				$whereclpub .= ' AND tx_foportal_publikationen.autor LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_publikationen.deleted="0"';
				$whereclres .= ' AND tx_foportal_profile.name LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_profile.deleted="0"';
			}
			
			$research = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_profile',$whereclres);
			$publ = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_publikationen ',$whereclpub);
			$projects = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_projekte ',$whereclpro);
			
			$counterpub += $GLOBALS['TYPO3_DB']->sql_num_rows($publ);
			$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($research);
			$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projects);	
			$counter = $counterpub + $counterres + $counterpro;
			if($counter == 0){
				$content .= $this->pi_getLL('list.noentries');
			}
			else{
			$content .= $counterpub.' '. $this->pi_getLL('list.pubentries') .' <br />';
			$content .= $counterres.' '. $this->pi_getLL('list.resentries') .' <br />';
			$content .= $counterpro.' '. $this->pi_getLL('list.proentries') .' <br />';			//Anzahl der gefundenen Einträge wird ausgegeben
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($publ) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($publ)) 
				$entry[] = $row;  
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($research) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($research)) 
				$entry[] = $row;  
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($projects) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projects)) 
				$entry[] = $row;  
			}
	        
			//Array mit Publikationen werden an Funktion showList übergeben. Ergebnis von showList wird an content gehängt
			if ($counter > 0)			
			$content .= $this->showList($entry); 
			
			return $content; 
		
		}*/
      
		
        // check if to show from search -- Überprüfung ob "search" in POST/GET Seitenvariablen
		if (isset($this->piVars['search'])) { 
			
			
			
             
			//WHERE Klausel für Suchstring wird erstellt
			if ($this->piVars['search'] != '') {
		  	    
				$whereclpub = 'MATCH(tx_foportal_publikationen.titel,tx_foportal_publikationen.abstract) AGAINST ("'.$this->piVars['search'].'") ';
				$whereclres = 'MATCH(tx_foportal_profile.name,tx_foportal_profile.ind_forschungsschwerpunkte,tx_foportal_profile.austattung,tx_foportal_profile.referenzprojekte,tx_foportal_profile.kooperationen) AGAINST ("'.$this->piVars['search'].'") ';
				$whereclpro = 'MATCH(tx_foportal_projekte.projekttitel,tx_foportal_projekte.kurzbeschreibung,tx_foportal_projekte.foerdermittelgeber) AGAINST ("'.$this->piVars['search'].'") ';
				
				if ($this->piVars['name'] != '0'){
				$whereclpub .= ' AND tx_foportal_publikationen.autor LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_publikationen.deleted="0"';
				$whereclres .= ' AND tx_foportal_profile.name LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_profile.deleted="0"';
				}
				
				if ($this->piVars['fb'] != '0'){
				$whereclpro .= ' AND tx_foportal_projekte.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_projekte.deleted="0"';
				$whereclres .= ' AND tx_foportal_profile.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_profile.deleted="0"';
				
				debug($whereclpro,'whereclpro mit S: ');
				debug($whereclpub,'whereclpub mit S: ');
				debug($whereclres,'whereclres mit S: ');
				}
				
				
            } 
			
			 elseif ($this->piVars['search'] == ''){
				
				if ($this->piVars['name'] != '0'){
				$whereclpub .= 'tx_foportal_publikationen.autor LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_publikationen.deleted="0"';
				$whereclres .= 'tx_foportal_profile.name LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_profile.deleted="0"';
				}
				
				if ($this->piVars['fb'] != '0' && $this->piVars['name'] != '0') {
				$whereclpro .= ' tx_foportal_projekte.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_projekte.deleted="0"';
				$whereclres .= ' OR tx_foportal_profile.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_profile.deleted="0"';
				}
				
				debug($whereclpro,'whereclpro ohne S: ');
				debug($whereclpub,'whereclpub ohne S: ');
				debug($whereclres,'whereclres ohne S: ');
				//return $content;
			
			} 
			 /* if ($this->piVars['name'] != '0'){
				$whereclpub .= ' AND tx_foportal_publikationen.autor LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_publikationen.deleted="0"';
				$whereclres .= ' AND tx_foportal_profile.name LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_profile.deleted="0"';
			}  */
			
			/*if ($this->piVars['fb'] != '0'){
				$whereclpro .= ' AND tx_foportal_projekte.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_projekte.deleted="0"';
				$whereclres .= ' AND tx_foportal_profile.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_profile.deleted="0"';
			} */
			
			/* if ($this->piVars['name'] != '0'){
				$whereclpub .= 'tx_foportal_publikationen.autor LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_publikationen.deleted="0"';
				$whereclres .= 'tx_foportal_profile.name LIKE "%'.$this->piVars['name'].'%" AND tx_foportal_profile.deleted="0"';
			}
			
			if ($this->piVars['fb'] != '0' && $this->piVars['name'] != '0') {
				$whereclpro .= ' tx_foportal_projekte.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_projekte.deleted="0"';
				$whereclres .= ' OR tx_foportal_profile.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_profile.deleted="0"';
			}
			
			if ($this->piVars['fb'] != '0' && $this->piVars['search'] != '0') {
				$whereclpro .= ' OR tx_foportal_projekte.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_projekte.deleted="0"';
				$whereclres .= ' OR tx_foportal_profile.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_profile.deleted="0"';
			}
			
			elseif ($this->piVars['fb'] != '0'){
				$whereclpro .= 'tx_foportal_projekte.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_projekte.deleted="0"';
				$whereclres .= 'tx_foportal_profile.fachbereich LIKE "%'.$this->piVars['fb'].'%" AND tx_foportal_profile.deleted="0"';
			}   */
			
			 /* else {
				
				$whereclpub = 'tx_foportal_publikationen.deleted="0"'; //ansonsten ist Where-Klausel nur where deleted = 0
				$whereclres = 'tx_foportal_profile.deleted="0"';
				$whereclpro = 'tx_foportal_projekte.deleted="0"';
            }   */
			 
			/*debug($whereclpro,'Pro Klausel:');
			debug($this->piVars['name'],'NAME:');
			debug($this->piVars['fb'],'FB:');
			 */
			//Engültige Abfrage (Noch ohne Kategorie nur auf Publikationen)
			
			$research = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_profile',$whereclres);
			$publ = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_publikationen ',$whereclpub);
			$projects = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_projekte ',$whereclpro); 
			
			//debug($GLOBALS['TYPO3_DB']->SELECTquery('*','tx_foportal_profile',$whereclres),'Select Query');
			
			
			
			
			
			$counterpub += $GLOBALS['TYPO3_DB']->sql_num_rows($publ);
			$counterres += $GLOBALS['TYPO3_DB']->sql_num_rows($research);
			$counterpro += $GLOBALS['TYPO3_DB']->sql_num_rows($projects);	
			$counter = $counterpub + $counterres + $counterpro;
			if($counter == 0){
				$content .= $this->pi_getLL('list.noentries');
			}
			else{
			$content .= $counterpub.' '. $this->pi_getLL('list.pubentries') .' <br />';
			$content .= $counterres.' '. $this->pi_getLL('list.resentries') .' <br />';
			$content .= $counterpro.' '. $this->pi_getLL('list.proentries') .' <br />';			//Anzahl der gefundenen Einträge wird ausgegeben
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($publ) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($publ)) 
				$entry[] = $row;  
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($research) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($research)) 
				$entry[] = $row;  
			}
			
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($projects) > 0) {            
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($projects)) 
				$entry[] = $row;  
			}
	        
			//Array mit Publikationen werden an Funktion showList übergeben. Ergebnis von showList wird an content gehängt
			if ($counter > 0)			
			$content .= $this->showList($entry); 
			
	
			
        } 
		
		
                 return($content); 
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
	
	function showList($entry) {
        // Einzelansicht Seite aus Flexform holen
		$singlepid = $this->pi_getFFValue($this->ffdata,'singlepid','sOtherSettings'); //Angabe der Seite für die Einzelansich aus dem Flexform
        //$orderpid =  $this->pi_getFFValue($this->ffdata,'orderpid','sOtherSettings');
		
		// Template laden
        if (isset($this->conf["templateFile"]))
            $template = $this->cObj->fileResource($this->conf["templateFile"]);
       
		else
            $template = $this->cObj->fileResource($this->standardTemplate);
		

		// Subparts aus Template holen
		$subpart = $this->cObj->getSubpart($template,"###LIST_ITEM_TEMPLATE###");
        $subpart_browser = $this->cObj->getSubpart($template,"###LIST_BROWSE_TEMPLATE###");
        
		
        $pagebrowser = '';
        $start = 0;
		
		//maximale Anzahl der Einträge aus Flexform holen
        $max = $this->pi_getFFValue($this->ffdata,'resultnum','sDEF'); 
        $end = $max;
        
		if ($end > sizeof($entry)) $end = sizeof($entry); //Falls es in einer Zeile, Falls es weniger Publikationen als Max mögliche Seiteneinträge gibt
            
        if (!isset($this->piVars['pnum'])) $this->piVars['pnum'] = 1; //Seitennummer wird auf 1 gestzt falls noch keine gesetzt
               
        if (sizeof($entry) > $max && $max>0) { //Falls Anzahl der Publikationen größer max Seiteneinträg & Max Seiteneinträge größer 0
                   
            $next = $this->piVars['pnum'] + 1; //Nächste Seitennummer +1
            $prev = $this->piVars['pnum'] - 1; //Vorherige Seitennummer -1
            $last = sizeof($entry)/$max; //Letzte Seite = Anz. Publikationen / max Seiteneinträge
                 
            if (isset($this->piVars['pnum']) && $this->piVars['pnum'] > 1) //Falls Seitenummer gesetzt und Seitennummer größer 1
                $pagebrowser .= $this->pi_linkTP_keepPIvars('<<',array('pnum' => $prev)).'&nbsp;';     // erstellt einen Link "<<" zurück. erhält aber die Page Variablen             

            for ($i=0; $i <=  $last; $i++)  { //bis zur letzen Seite
				$c = $i+1; 
				if ($this->piVars['pnum'] == $c || ( !isset($this->piVars['pnum']) && $c==1) ) //Falls auf aktueller Seiter oder nicht auf Seite 1
					$link = $c; //Wird der Link für die aktuelle Seite der Inhalt von c
					
				else 
					$link = $this->pi_linkTP_keepPIvars($c,array('pnum' => $c)); //Ansonsten wird ein neuer Link mit der entsprechenden Nummer (2,3,4,...) angelegt
					
				$pagebrowser .= $link.' ';
            }
            
			if ($this->piVars['pnum'] < $last)
				$pagebrowser .= '&nbsp;'.$this->pi_linkTP_keepPIvars('>>',array('pnum' => $next));   //Link auf letzte Seite "<<" wird angelegt               
		}
		
        // Page Browser an Marker (oben) anlegen
        $bmarkerARRAY['###LIST_PAGEBROWSER###']=$pagebrowser;
		$content .= $this->cObj->substituteMarkerArray($subpart_browser,$bmarkerARRAY); //Pagebwoser wird an seiner Marke abgelegt


              // compute indizes -- Anfang und ende werden bestimmt mal debuggen und schauen was rauskommt
        if (isset($this->piVars['pnum'])) {
                      
            $start = $max* ( $this->piVars['pnum'] - 1);
            $end = $max* $this->piVars['pnum'];
            if ($end > sizeof($entry))
                $end = sizeof($entry);
		} 

		// --------------------------- Eintragsliste rendern ------------------------------------
                  
        if (sizeof($entry) == 0)
            $content .= "<p>".$this->pi_getLL('list.noentries')."<\p>";     //Falls keine Einträge existieren wird no entries ausgegeben             
                  
		for ($i=$start; $i<$end; $i++) { //Für alle Einträge
            $row = $entry[$i];
         
			$p_title = '';
			$p_author = '';
			$p_file = '';
			$p_more = '';
            $p_order = '';
            $num_year = '';
            $p_year = '';
            $p_publisher = '';
            $p_subtitle = '';
            $p_location = '';
                        
			
			if ($row['titel'] != ''){
				$params = array( $this->prefixId => array( 'entryid' => $row['uid'], ppid => $GLOBALS['TSFE']->id, 'type' => 'pub'));
				
				}
			if ($row['projekttitel'] != ''){
				$params = array( $this->prefixId => array( 'entryid' => $row['uid'], ppid => $GLOBALS['TSFE']->id, 'type' => 'proj'));
				}
			if ($row['name'] != ''){
				$params = array( $this->prefixId => array( 'entryid' => $row['uid'], ppid => $GLOBALS['TSFE']->id, 'type' => 'prof'));
				}
			
			//debug($params,'More Link:');
          
			//Marc Edit
			//$params = array( $this->prefixId => array( 'entryid' => $row['uid'], ppid => $GLOBALS['TSFE']->id)); //Hier Link mit Marker für Typ Versehen?!
			
			$p_more = $this->pi_linkToPage($this->pi_getLL('more'),$singlepid,'',$params);

            if ($row['jahr'] != '') $p_year=',&nbsp;'.$row['year'];

            
			
			if ($row['titel'] != '') $p_title = ',&nbsp;'.$row['titel'];
			//if ($row['name'] != '') $p_title = ',&nbsp;'.$row['name']; //vllt mit else
			//if ($row['verlag'] != '') $p_publisher = ',&nbsp;'.$row['verlag'];
			//if ($row['ort'] != '') $p_location = ',&nbsp;'.$row['ort'];
			
            //if ($row['number'] != '') $p_number = ',&nbsp;'.$row['number'];
 
            //if ($p_file!='' || $p_order!='') $p_order.='<br />'; 
    
			// get content and define substitution -- Marker werden ersetzt durch zuvor definierte Variablenwerte
			$markerARRAY['###LIST_TITLE###']=$row['name'];
            $markerARRAY['###LIST_SUBTITLE###']=$row['titel'];
			// $markerARRAY['###LIST_AUTHOR###']=$row['autor'];
		    // $markerARRAY['###LIST_FILE###']=$p_file;
			 $markerARRAY['###LIST_MORE###']=$p_more;
			// $markerARRAY['###LIST_ORDER###']=$p_order;
       		// $markerARRAY['###LIST_YEAR###']=$p_year;
			// $markerARRAY['###LIST_PUBLISHER###']=$p_publisher;
			// $markerARRAY['###LIST_LOCATION###']=$p_location;
 			// $markerARRAY['###LIST_NUMBER###']=$p_number;
			// substitute
			$content .= $this->cObj->substituteMarkerArray($subpart,$markerARRAY);
			}
             // show page browser on the bottom of list
			$content .= $this->cObj->substituteMarkerArray($subpart_browser,$bmarkerARRAY); //Pagebrowser wird unten nocheinmal angezeigt
            return($content);



        }
		
	/**
	 * generateSingleView
	 *
	 * Generiert eine Einzelansicht eines bestimmten 
	 * Eintrags
	 */
		
	function generateSingleView() {
	
		$params = t3lib_div::_GET($this->prefixId);//  Ermittelt Eintrag ID aus Seitenparameter
		$entryid = $params['entryid']; //wird von der Listenansicht übergeben
		$type = $params['type'];
		
		if (isset($this->conf["templateFile"]))
            $template = $this->cObj->fileResource($this->conf["templateFile"]);
        else
            $template = $this->cObj->fileResource($this->standardTemplate);
		
		
		
		if (type == 'pub'){
			
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_publikationen','uid='.$entryid);
			$pub = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
			// Subpart aus Template laden
			$subpart = $this->cObj->getSubpart($template,"###SINGLE_TEMPLATE###");


			// Ersetzungen anlegen
			$markerARRAY['###SINGLE_TITLE###']=$type; //TEST
			//$markerARRAY['###SINGLE_TITLE###']=$pub['name'];
			//$markerARRAY['###SINGLE_SUBTITLE###']=$pub['subtitle'];
			//$markerARRAY['###SINGLE_AUTHOR###']=$pub['autor'];
			//$markerARRAY['###SINGLE_INFO###']=$p_info;
			// $markerARRAY['###SINGLE_PUBLISHER###']=$pub['verlag'];
			// $markerARRAY['###SINGLE_PLACE###']=$pub['ort'];
			// $markerARRAY['###SINGLE_YEAR###']=$pub['jahr'];
			// $markerARRAY['###SINGLE_ISBN###']=$pub['isbn'];
			//$markerARRAY['###SINGLE_FILE###']=$p_file;
			// $markerARRAY['###SINGLE_ABSTRACT###']=$pub['abstract'];
			$markerARRAY['###SINGLE_RETURNLINK###']=$returnlink;
			//$markerARRAY['###SINGLE_ORDERLINK###'] = $p_order;
		}
		
		if (type == 'prof'){
			
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_profile','uid='.$entryid);
			$pub = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
			// Subpart aus Template laden
			$subpart = $this->cObj->getSubpart($template,"###SINGLE_TEMPLATE###");


			// Ersetzungen anlegen
			$markerARRAY['###SINGLE_TITLE###']=$type; //TEST
			//$markerARRAY['###SINGLE_TITLE###']=$pub['name'];
			//$markerARRAY['###SINGLE_SUBTITLE###']=$pub['subtitle'];
			//$markerARRAY['###SINGLE_AUTHOR###']=$pub['autor'];
			//$markerARRAY['###SINGLE_INFO###']=$p_info;
			// $markerARRAY['###SINGLE_PUBLISHER###']=$pub['verlag'];
			// $markerARRAY['###SINGLE_PLACE###']=$pub['ort'];
			// $markerARRAY['###SINGLE_YEAR###']=$pub['jahr'];
			// $markerARRAY['###SINGLE_ISBN###']=$pub['isbn'];
			//$markerARRAY['###SINGLE_FILE###']=$p_file;
			// $markerARRAY['###SINGLE_ABSTRACT###']=$pub['abstract'];
			$markerARRAY['###SINGLE_RETURNLINK###']=$returnlink;
			//$markerARRAY['###SINGLE_ORDERLINK###'] = $p_order;
		}
		
		if (type == 'proj'){
			
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_projekte','uid='.$entryid);
			$pub = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
			// Subpart aus Template laden
			$subpart = $this->cObj->getSubpart($template,"###SINGLE_TEMPLATE###");


			// Ersetzungen anlegen
			$markerARRAY['###SINGLE_TITLE###']=$type; //TEST
			//$markerARRAY['###SINGLE_TITLE###']=$pub['name'];
			//$markerARRAY['###SINGLE_SUBTITLE###']=$pub['subtitle'];
			//$markerARRAY['###SINGLE_AUTHOR###']=$pub['autor'];
			//$markerARRAY['###SINGLE_INFO###']=$p_info;
			// $markerARRAY['###SINGLE_PUBLISHER###']=$pub['verlag'];
			// $markerARRAY['###SINGLE_PLACE###']=$pub['ort'];
			// $markerARRAY['###SINGLE_YEAR###']=$pub['jahr'];
			// $markerARRAY['###SINGLE_ISBN###']=$pub['isbn'];
			//$markerARRAY['###SINGLE_FILE###']=$p_file;
			// $markerARRAY['###SINGLE_ABSTRACT###']=$pub['abstract'];
			$markerARRAY['###SINGLE_RETURNLINK###']=$returnlink;
			//$markerARRAY['###SINGLE_ORDERLINK###'] = $p_order;
		}
	
		$returnlink =  $this->pi_linkToPage('<< '.$this->pi_getLL('back'),$params['ppid']);// Zurück Link
		//debug($params['ppid'],'Params in BACK Link:');
		$orderpid =  $this->pi_getFFValue($this->ffdata,'orderpid','sOtherSettings'); // Ordnen nach aus Flexform holen
		
		/* $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_foportal_publikationen','uid='.$entryid);
		$pub = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result); */
	
        

		
		

		// Subpart aus Template laden
		$subpart = $this->cObj->getSubpart($template,"###SINGLE_TEMPLATE###");


		// Ersetzungen anlegen
		$markerARRAY['###SINGLE_TITLE###']=$type; //TEST
		//$markerARRAY['###SINGLE_TITLE###']=$pub['name'];
		//$markerARRAY['###SINGLE_SUBTITLE###']=$pub['subtitle'];
  		//$markerARRAY['###SINGLE_AUTHOR###']=$pub['autor'];
		//$markerARRAY['###SINGLE_INFO###']=$p_info;
		// $markerARRAY['###SINGLE_PUBLISHER###']=$pub['verlag'];
		// $markerARRAY['###SINGLE_PLACE###']=$pub['ort'];
		// $markerARRAY['###SINGLE_YEAR###']=$pub['jahr'];
		// $markerARRAY['###SINGLE_ISBN###']=$pub['isbn'];
		//$markerARRAY['###SINGLE_FILE###']=$p_file;
		// $markerARRAY['###SINGLE_ABSTRACT###']=$pub['abstract'];
	    $markerARRAY['###SINGLE_RETURNLINK###']=$returnlink;
        //$markerARRAY['###SINGLE_ORDERLINK###'] = $p_order;

		// substitute
		$singlepage .= $this->cObj->substituteMarkerArray($subpart,$markerARRAY);

        return $singlepage;
	}


}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/foportal/pi1/class.tx_foportal_pi1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/foportal/pi1/class.tx_foportal_pi1.php']);
}

?>
