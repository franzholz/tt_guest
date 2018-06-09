<?php

########################################################################
# Extension Manager/Repository config file for ext "tt_guest".
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Guestbook',
	'description' => 'Simple guestbook with subject, comment, name, email and www.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.7.0',
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
			'php' => '5.5.0-7.99.99',
			'typo3' => '7.6.0-8.99.99',
			'div2007' => '1.10.1-0.0.0',
			'tslib_fetce' => '0.3.0-0.9.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

