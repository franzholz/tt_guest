<?php

########################################################################
# Extension Manager/Repository config file for ext "tt_guest".
#
# Auto generated 21-03-2012 14:50
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Guestbook',
	'description' => 'Simple guestbook with subject, comment, name, email and www.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.4.1',
	'dependencies' => 'div2007,tslib_fetce',
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
			'php' => '5.3.0-5.6.99',
			'typo3' => '6.1.0-6.2.99',
			'div2007' => '1.0.2-0.0.0',
			'tslib_fetce' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:31:{s:9:"ChangeLog";s:4:"6a17";s:28:"class.tx_ttguest_wizicon.php";s:4:"f085";s:21:"ext_conf_template.txt";s:4:"8d43";s:12:"ext_icon.gif";s:4:"6754";s:15:"ext_icon__h.gif";s:4:"e999";s:17:"ext_localconf.php";s:4:"2729";s:14:"ext_tables.php";s:4:"72d2";s:14:"ext_tables.sql";s:4:"eaa6";s:18:"flexform_ds_pi.xml";s:4:"d8f8";s:13:"guestbook.gif";s:4:"b513";s:13:"locallang.php";s:4:"7854";s:25:"locallang_csh_ttguest.php";s:4:"77be";s:17:"locallang_tca.php";s:4:"2c40";s:7:"tca.php";s:4:"9bc1";s:12:"doc/TODO.txt";s:4:"d602";s:14:"doc/manual.sxw";s:4:"c073";s:36:"hooks/class.tx_ttguest_hooks_cms.php";s:4:"e2d7";s:35:"model/class.tx_ttguest_language.php";s:4:"d17c";s:23:"pi/class.tx_ttguest.php";s:4:"df81";s:39:"pi/class.tx_ttguest_RecordNavigator.php";s:4:"339f";s:18:"pi/guest_help.tmpl";s:4:"ab19";s:18:"pi/guest_help1.gif";s:4:"817b";s:19:"pi/guest_submit.php";s:4:"a67d";s:23:"pi/guest_template1.tmpl";s:4:"7bd4";s:23:"pi/guest_template2.tmpl";s:4:"74eb";s:16:"pi/locallang.xml";s:4:"74aa";s:30:"static/css_style/constants.txt";s:4:"d7da";s:26:"static/css_style/setup.txt";s:4:"8777";s:30:"static/old_style/constants.txt";s:4:"14e6";s:26:"static/old_style/setup.txt";s:4:"fa20";s:26:"static/share/constants.txt";s:4:"e397";}',
	'suggests' => array(
	),
);

?>