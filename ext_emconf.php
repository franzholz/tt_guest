<?php

########################################################################
# Extension Manager/Repository config file for ext: "tt_guest"
#
# Auto generated 14-05-2007 07:37
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Guestbook',
	'description' => 'Simple guestbook with subject, comment, name, email and www.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.1.5',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Franz Holzinger',
	'author_email' => 'kontakt@fholzinger.com',
	'author_company' => 'Freelancer',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.8.0-0.0.0',
			'fh_library' => '0.0.18-'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:28:{s:9:"ChangeLog";s:4:"fc86";s:28:"class.tx_ttguest_wizicon.php";s:4:"a538";s:21:"ext_conf_template.txt";s:4:"dbec";s:12:"ext_icon.gif";s:4:"6754";s:15:"ext_icon__h.gif";s:4:"e999";s:17:"ext_localconf.php";s:4:"d209";s:14:"ext_tables.php";s:4:"d3d3";s:14:"ext_tables.sql";s:4:"594a";s:18:"flexform_ds_pi.xml";s:4:"d8f8";s:13:"guestbook.gif";s:4:"b513";s:13:"locallang.php";s:4:"7a32";s:25:"locallang_csh_ttguest.php";s:4:"ad4b";s:17:"locallang_tca.php";s:4:"7286";s:7:"tca.php";s:4:"31b5";s:23:"pi/class.tx_ttguest.php";s:4:"ef40";s:39:"pi/class.tx_ttguest_RecordNavigator.php";s:4:"0650";s:18:"pi/guest_help.tmpl";s:4:"ab19";s:18:"pi/guest_help1.gif";s:4:"817b";s:19:"pi/guest_submit.inc";s:4:"9a68";s:23:"pi/guest_template1.tmpl";s:4:"29f4";s:23:"pi/guest_template2.tmpl";s:4:"dedf";s:12:"doc/TODO.txt";s:4:"d602";s:14:"doc/manual.sxw";s:4:"e2f5";s:26:"static/share/constants.txt";s:4:"9fe5";s:30:"static/css_style/constants.txt";s:4:"61f9";s:26:"static/css_style/setup.txt";s:4:"7555";s:30:"static/old_style/constants.txt";s:4:"13b4";s:26:"static/old_style/setup.txt";s:4:"18c0";}',
	'suggests' => array(
	),
);

?>