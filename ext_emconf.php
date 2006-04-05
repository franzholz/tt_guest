<?php

########################################################################
# Extension Manager/Repository config file for ext: "tt_guest"
#
# Auto generated 02-04-2006 10:02
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
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Franz Holzinger',
	'author_email' => 'kontakt@fholzinger.com',
	'author_company' => 'Freelancer',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.1.0',
	'_md5_values_when_last_written' => 'a:25:{s:9:"ChangeLog";s:4:"c31b";s:28:"class.tx_ttguest_wizicon.php";s:4:"2847";s:21:"ext_conf_template.txt";s:4:"dbec";s:12:"ext_icon.gif";s:4:"6754";s:15:"ext_icon__h.gif";s:4:"e999";s:17:"ext_localconf.php";s:4:"7f93";s:14:"ext_tables.php";s:4:"a1ff";s:14:"ext_tables.sql";s:4:"515e";s:28:"ext_typoscript_constants.txt";s:4:"a432";s:24:"ext_typoscript_setup.txt";s:4:"1451";s:18:"flexform_ds_pi.xml";s:4:"d8f8";s:13:"guestbook.gif";s:4:"b513";s:13:"locallang.php";s:4:"7a32";s:25:"locallang_csh_ttguest.php";s:4:"644c";s:17:"locallang_tca.php";s:4:"a271";s:7:"tca.php";s:4:"9066";s:12:"doc/TODO.txt";s:4:"d602";s:14:"doc/manual.sxw";s:4:"7d25";s:23:"pi/class.tx_ttguest.php";s:4:"7447";s:39:"pi/class.tx_ttguest_RecordNavigator.php";s:4:"8a30";s:18:"pi/guest_help.tmpl";s:4:"a7e0";s:18:"pi/guest_help1.gif";s:4:"817b";s:19:"pi/guest_submit.inc";s:4:"ba89";s:23:"pi/guest_template1.tmpl";s:4:"29f4";s:23:"pi/guest_template2.tmpl";s:4:"dedf";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '3.0.0-',
			'typo3' => '3.5.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>