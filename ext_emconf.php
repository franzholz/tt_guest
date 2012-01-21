<?php

########################################################################
# Extension Manager/Repository config file for ext: "tt_guest"
#
# Auto generated 28-04-2008 07:32
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
	'version' => '1.2.3',
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
	'author_email' => 'franz@ttproducts.de',
	'author_company' => 'jambage.com',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.2.0-0.0.0',
			'typo3' => '3.8.0-4.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'div2007' => '0.7.2-',
		),
	),
	'_md5_values_when_last_written' => 'a:30:{s:9:"ChangeLog";s:4:"8a48";s:28:"class.tx_ttguest_wizicon.php";s:4:"45cb";s:21:"ext_conf_template.txt";s:4:"4023";s:12:"ext_icon.gif";s:4:"6754";s:15:"ext_icon__h.gif";s:4:"e999";s:17:"ext_localconf.php";s:4:"23a4";s:14:"ext_tables.php";s:4:"d3d3";s:14:"ext_tables.sql";s:4:"594a";s:18:"flexform_ds_pi.xml";s:4:"d8f8";s:13:"guestbook.gif";s:4:"b513";s:13:"locallang.php";s:4:"7a32";s:25:"locallang_csh_ttguest.php";s:4:"ad4b";s:17:"locallang_tca.php";s:4:"7286";s:7:"tca.php";s:4:"31b5";s:23:"pi/class.tx_ttguest.php";s:4:"352c";s:39:"pi/class.tx_ttguest_RecordNavigator.php";s:4:"0650";s:18:"pi/guest_help.tmpl";s:4:"ab19";s:18:"pi/guest_help1.gif";s:4:"817b";s:19:"pi/guest_submit.inc";s:4:"5c48";s:23:"pi/guest_template1.tmpl";s:4:"7bd4";s:23:"pi/guest_template2.tmpl";s:4:"74eb";s:16:"pi/locallang.xml";s:4:"a14d";s:12:"doc/TODO.txt";s:4:"d602";s:14:"doc/manual.sxw";s:4:"877c";s:36:"hooks/class.tx_ttguest_hooks_cms.php";s:4:"05ef";s:26:"static/share/constants.txt";s:4:"7a97";s:30:"static/css_style/constants.txt";s:4:"d7da";s:26:"static/css_style/setup.txt";s:4:"b877";s:30:"static/old_style/constants.txt";s:4:"14e6";s:26:"static/old_style/setup.txt";s:4:"7e17";}',
);

?>