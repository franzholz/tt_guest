<?php

if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$table = 'tt_content';

$GLOBALS['TCA'][$table]['types']['list']['subtypes_excludelist']['3'] = 'layout,select_key';
$GLOBALS['TCA'][$table]['types']['list']['subtypes_addlist']['3'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('3', 'FILE:EXT:' . TT_GUEST_EXT . '/flexform_ds_pi.xml');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    array(
        'LLL:EXT:' . TT_GUEST_EXT . '/locallang_tca.xlf:' . $table . '.list_type_pi',
        '3',
        'EXT:' . TT_GUEST_EXT . '/ext_icon.gif'
    ),
    'list_type',
    TT_GUEST_EXT
);





