<?php
if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

call_user_func($emClass . '::addStaticFile', $_EXTKEY, 'Configuration/TypoScript/Default/', 'Guestbook Setup');

call_user_func($emClass . '::allowTableOnStandardPages', 'tt_guest');
call_user_func($emClass . '::addToInsertRecords', 'tt_guest');
call_user_func($emClass . '::addLLrefForTCAdescr', 'tt_guest', 'EXT:' . $_EXTKEY . '/locallang_csh_ttguest.xlf');


if (TYPO3_MODE == 'BE') {
    $GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses']['JambageCom\\TtGuest\\Controller\\WizardIcon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Controller/WizardIcon.php';
}

