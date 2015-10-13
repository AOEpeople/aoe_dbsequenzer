#
# Table structure for table 'tx_aoedbsequenzer_sequenz'
#
CREATE TABLE tx_aoedbsequenzer_sequenz (
	tablename varchar(100) DEFAULT '' NOT NULL,
	current int(30) DEFAULT '0' NOT NULL,
	offset int(30) DEFAULT '1' NOT NULL,
	timestamp int(30) DEFAULT '0' NOT NULL,
	changed int(11) DEFAULT '0' NOT NULL,
	UNIQUE KEY tablename (tablename)
) ENGINE=InnoDB;
#
# Table structure for table 'tx_aoedbsequenzer_overwriteprotection'
#
CREATE TABLE tx_aoedbsequenzer_domain_model_overwriteprotection (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    sorting int(10) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    protected_uid int(11) DEFAULT '0' NOT NULL,
    protected_tablename  varchar(100) DEFAULT '' NOT NULL,
    protected_time int(11) DEFAULT '0' NOT NULL,
    protected_mode int(11) DEFAULT '0' NOT NULL,
    PRIMARY KEY (uid),
    KEY parent (pid)
) ENGINE=InnoDB;