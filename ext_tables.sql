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
  title tinytext NOT NULL,
  note text NOT NULL,
  cr_name varchar(80) DEFAULT '' NOT NULL,
  cr_email varchar(80) DEFAULT '' NOT NULL,
  www tinytext NOT NULL,
  cr_ip varchar(15) DEFAULT '' NOT NULL,
  doublePostCheck int(11) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid)
);


### cache tables needed only for TYPO3 4.3 - 4.5

#
# TABLE structure FOR TABLE 'tt_guest_cache'
#
CREATE TABLE tt_guest_cache (
    id int(11) unsigned NOT NULL auto_increment,
    identifier varchar(250) DEFAULT '' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    content mediumblob,
    lifetime int(11) unsigned DEFAULT '0' NOT NULL,
    PRIMARY KEY (id),
    KEY cache_id (identifier)
) ENGINE=InnoDB;



#
# TABLE structure FOR TABLE 'tt_guest_cache_tags'
#
CREATE TABLE tt_guest_cache_tags (
    id int(11) unsigned NOT NULL auto_increment,
    identifier varchar(250) DEFAULT '' NOT NULL,
    tag varchar(250) DEFAULT '' NOT NULL,
    PRIMARY KEY (id),
    KEY cache_id (identifier),
    KEY cache_tag (tag)
) ENGINE=InnoDB;
