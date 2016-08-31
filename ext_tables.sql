#
# Table structure for table 'tt_guest'
#
CREATE TABLE tt_guest (
  uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
  pid int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(3) unsigned DEFAULT '0' NOT NULL,
  title tinytext,
  note text,
  cr_name varchar(80) DEFAULT '' NOT NULL,
  cr_email varchar(80) DEFAULT '' NOT NULL,
  www tinytext,
  cr_ip varchar(15) DEFAULT '' NOT NULL,
  doublePostCheck int(11) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);

