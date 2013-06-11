#
# Table structure for table 'tx_foportal_profile_institute_mm'
# 
#
CREATE TABLE tx_foportal_profile_institute_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_profile_fachbereich_mm'
# 
#
CREATE TABLE tx_foportal_profile_fachbereich_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_profile_forschungschwerpunkte_mm'
# 
#
CREATE TABLE tx_foportal_profile_forschungschwerpunkte_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_profile_tag_mm'
# 
#
CREATE TABLE tx_foportal_profile_tag_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_foportal_profile'
#
CREATE TABLE tx_foportal_profile (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	personenelement text,
	funktion tinytext,
	institute int(11) DEFAULT '0' NOT NULL,
	fachbereich int(11) DEFAULT '0' NOT NULL,
	forschungschwerpunkte text,
	ind_forschungsschwerpunkte text,
	mitgliedschaften text,
	austattung text,
	referenzprojekte text,
	kooperationen text,
	preise text,
	downloads text,
	links text,
	zusatzinfos text,
	tag int(11) DEFAULT '0' NOT NULL,
	klicks int(11) NOT NULL,
	FULLTEXT (name,ind_forschungsschwerpunkte,austattung,referenzprojekte,kooperationen),
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=MyISAM;



#
# Table structure for table 'tx_foportal_fachbereiche'
#
CREATE TABLE tx_foportal_fachbereiche (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fachbereich tinytext,
	link tinytext,
	kennziffer tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=MyISAM;



#
# Table structure for table 'tx_foportal_institute'
#
CREATE TABLE tx_foportal_institute (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext,
	link tinytext,
	short tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=MyISAM;



#
# Table structure for table 'tx_foportal_forschungsschwerpunkte'
#
CREATE TABLE tx_foportal_forschungsschwerpunkte (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	forschungsschwerpunkt tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=MyISAM;




#
# Table structure for table 'tx_foportal_projekte_projektleiter_mm'
# 
#
CREATE TABLE tx_foportal_projekte_projektleiter_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_projekte_mitarbeiter_mm'
# 
#
CREATE TABLE tx_foportal_projekte_mitarbeiter_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_projekte_fachbereich_mm'
# 
#
CREATE TABLE tx_foportal_projekte_fachbereich_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_projekte_institut_mm'
# 
#
CREATE TABLE tx_foportal_projekte_institut_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_projekte_forschungsschwerpunkt_mm'
# 
#
CREATE TABLE tx_foportal_projekte_forschungsschwerpunkt_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_foportal_projekte_tag_mm'
# 
#
CREATE TABLE tx_foportal_projekte_tag_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_foportal_projekte'
#
CREATE TABLE tx_foportal_projekte (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	projekttitel tinytext,
	projekttyp int(11) DEFAULT '0' NOT NULL,
	kurzbeschreibung text,
	projektleiteranzeige tinytext,
	projektmitarbeiteranzeige tinytext,
	projektleiter text,
	mitarbeiter text,
	fachbereich int(11) DEFAULT '0' NOT NULL,
	institut int(11) DEFAULT '0' NOT NULL,
	jahr tinytext,
	projektbeginn int(11) DEFAULT '0' NOT NULL,
	projektende int(11) DEFAULT '0' NOT NULL,
	projektvolumen tinytext,
	foerdervolumen tinytext,
	foerdermittelgeber text,
	forschungsschwerpunkt int(11) DEFAULT '0' NOT NULL,
	webseite text,
	downloads text,
	tag int(11) DEFAULT '0' NOT NULL,
	klicks int(11) NOT NULL,
	FULLTEXT(projekttitel,kurzbeschreibung,foerdermittelgeber,projektleiteranzeige),
	FULLTEXT(projektleiteranzeige),
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=MyISAM;




#
# Table structure for table 'tx_foportal_publikationen_tag_mm'
# 
#
CREATE TABLE tx_foportal_publikationen_tag_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_foportal_publikationen'
#
CREATE TABLE tx_foportal_publikationen (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	titel tinytext,
	personenelement text,
	typ int(11) DEFAULT '0' NOT NULL,
	autor tinytext,
	beitrag_in tinytext,
	verlag tinytext,
	ort tinytext,
	isbn tinytext,
	jahr tinytext,
	ausgabe tinytext,
	seiten tinytext,
	link tinytext,
	abstract text,
	tag int(11) DEFAULT '0' NOT NULL,
	klicks int(11) NOT NULL,
	FULLTEXT(titel,abstract),
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=MyISAM;



#
# Table structure for table 'tx_foportal_tags'
#
CREATE TABLE tx_foportal_tags (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	tag tinytext,
	frequenz int(11) DEFAULT '0',
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=MyISAM;