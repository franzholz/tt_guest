<?php


// ******************************************************************
// This is the standard guestbook
// ******************************************************************
$result = array(
	'ctrl' => array (
		'label' => 'title',
		'default_sortby' => 'ORDER BY crdate DESC',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'delete' => 'deleted',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
		'enablecolumns' => array (
			'disabled' => 'hidden'
		),
		'title' => 'LLL:EXT:' . TT_GUEST_EXT . '/locallang_tca.xlf:tt_content.list_type_pi',
		'iconfile' => PATH_BE_TTGUEST_REL . 'ext_icon.gif',
	),
	'interface' => array (
		'showRecordFieldList' => 'title,cr_name,cr_email,note,www,cr_ip,hidden'
	),
	'columns' => array (
		'title' => array (
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.title',
			'config' => array (
				'type' => 'input',
				'size' => '40',
				'max' => '256',
				'eval' => 'null',
				'default' => NULL,
			)
		),
		'note' => array (
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.note',
			'config' => array (
				'type' => 'text',
				'cols' => '40',
				'rows' => '5',
				'eval' => 'null',
				'default' => NULL,
			)
		),
		'cr_name' => array (
			'label' => 'LLL:EXT:tt_guest/locallang_tca.xlf:tt_guest.cr_name',
			'config' => array (
				'type' => 'input',
				'size' => '40',
				'eval' => 'trim',
				'max' => '80'
			)
		),
		'cr_email' => array (
			'label' => 'LLL:EXT:tt_guest/locallang_tca.xlf:tt_guest.cr_email',
			'config' => array (
				'type' => 'input',
				'size' => '40',
				'eval' => 'trim',
				'max' => '80'
			)
		),
		'www' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.www',
			'config' => array (
				'type' => 'input',
				'eval' => 'trim',
				'size' => '20',
				'max' => '256',
				'eval' => 'null',
				'default' => NULL,
			)
		),
		'cr_ip' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:tt_guest/locallang_tca.xlf:tt_guest.cr_ip',
			'config' => array (
				'type' => 'input',
				'size' => '15',
				'max' => '15',
			)
		),
		'hidden' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => array (
				'type' => 'check',
				'default' => '1'
			)
		)
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;;;1-1-1, title;;;;3-3-3, note, cr_name, cr_email, www, cr_ip')
	)
);


return $result;


