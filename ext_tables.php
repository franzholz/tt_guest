<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

call_user_func($emClass . '::addStaticFile', $_EXTKEY, 'Configuration/TypoScript/Default/', 'Guestbook Setup');

call_user_func($emClass . '::addPiFlexFormValue', '3', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi.xml');
call_user_func($emClass . '::addPlugin', array('LLL:EXT:' . $_EXTKEY . '/locallang_tca.xlf:tt_content.list_type_pi', '3'), 'list_type');
call_user_func($emClass . '::allowTableOnStandardPages', 'tt_guest');
call_user_func($emClass . '::addToInsertRecords', 'tt_guest');

if (TYPO3_MODE == 'BE') {
	$GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses']['tx_ttguest_wizicon'] =
		PATH_BE_TTGUEST . 'class.tx_ttguest_wizicon.php';
}

call_user_func($emClass . '::addLLrefForTCAdescr', 'tt_guest', 'EXT:' . $_EXTKEY . '/locallang_csh_ttguest.xlf');

