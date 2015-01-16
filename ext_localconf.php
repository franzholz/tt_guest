<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:

if (!defined ('TT_GUEST_EXT')) {
	define('TT_GUEST_EXT', $_EXTKEY);
}

if (!defined ('PATH_BE_TTGUEST')) {
	define('PATH_BE_TTGUEST', t3lib_extMgm::extPath(TT_GUEST_EXT));
}

if (!defined ('PATH_BE_TTGUEST_REL')) {
	define('PATH_BE_TTGUEST_REL', t3lib_extMgm::extRelPath(TT_GUEST_EXT));
}

if (!defined ('PATH_FE_TTGUEST_REL')) {
	define('PATH_FE_TTGUEST_REL', t3lib_extMgm::siteRelPath(TT_GUEST_EXT));
}


if (isset($_EXTCONF) && is_array($_EXTCONF)) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXT] = $_EXTCONF;
	if (isset($tmpArray) && is_array($tmpArray)) {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXT] =
			array_merge($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXT], $tmpArray);
	}
} else {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXT] = array();
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXT]['useFlexforms'] = 1;
}


if (TYPO3_MODE == 'BE' && $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXT]['useFlexforms'] && defined('PATH_BE_div2007')) {
	// replace the output of the former CODE field with the flexform
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][3][] = 'EXT:' . TT_GUEST_EXT . '/hooks/class.tx_ttguest_hooks_cms.php:&tx_ttguest_hooks_cms->pmDrawItem';
}


if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addUserTSConfig('options.saveDocNew.tt_guest=1');

	## Extending TypoScript from static template uid=43 to set up userdefined tag:
	t3lib_extMgm::addTypoScript($_EXTKEY, 'editorcfg', 'tt_content.CSS_editor.ch.tt_board_guest = < plugin.tt_guest.CSS_editor ', 43);
}


if (isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch']) && is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch'])) {
	// TYPO3 4.5 with livesearch
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch'] = array_merge(
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['livesearch'],
		array(
			'tt_guest' => 'tt_guest'
		)
	);
}


// support for new Caching Framework


// Register cache 'tt_guest_cache'
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['tt_guest_cache'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['tt_guest_cache'] = array();
}

// Define string frontend as default frontend, this must be set with TYPO3 4.5 and below
// and overrides the default variable frontend of 4.6
if (!isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['tt_guest_cache']['frontend'])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['tt_guest_cache']['frontend'] = 't3lib_cache_frontend_StringFrontend';
}

// add missing setup for the tt_content "list_type = 3" which is used by tt_guest
$addLine = 'tt_content.list.20.3 = < plugin.tt_guest';
t3lib_extMgm::addTypoScript(TT_GUEST_EXT, 'setup', '
# Setting ' . TT_GUEST_EXT . ' plugin TypoScript
' . $addLine . '
', 43);

?>