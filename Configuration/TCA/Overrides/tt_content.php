<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['3'] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['3'] = 'pi_flexform';

