#
# Table structure for table 'tx_aoedbsequenzer_sequenz'
#
CREATE TABLE tx_aoedbsequenzer_sequenz (
	tablename varchar(100) DEFAULT '' NOT NULL,
	current int(30) DEFAULT 0 NOT NULL,
	offset int(30) DEFAULT 1 NOT NULL,
	timestamp int(30) DEFAULT 0 NOT NULL,
	changed int(11) DEFAULT 0 NOT NULL,
	UNIQUE KEY tablename (tablename)
) ENGINE=InnoDB;
