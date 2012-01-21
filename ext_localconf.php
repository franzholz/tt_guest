<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$typoVersion = t3lib_div::int_from_ver($GLOBALS['TYPO_VERSION']);
$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:

if (!defined ('TT_GUEST_EXTkey')) {
	define('TT_GUEST_EXTkey',$_EXTKEY);
}

if (!defined ('PATH_BE_ttguest')) {
	define('PATH_BE_ttguest', t3lib_extMgm::extPath(TT_GUEST_EXTkey));
}

if (!defined ('PATH_BE_ttguest_rel')) {
	define('PATH_BE_ttguest_rel', t3lib_extMgm::extRelPath(TT_GUEST_EXTkey));
}

if (!defined ('PATH_FE_ttguest_rel')) {
	define('PATH_FE_ttguest_rel', t3lib_extMgm::siteRelPath(TT_GUEST_EXTkey));
}

if (!defined ('DIV2007_EXTkey')) {
	define('DIV2007_EXTkey','div2007');
}

	// turn the use of flexforms on:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXTkey]['useFlexforms'] = $_EXTCONF['useFlexforms'];

if (t3lib_extMgm::isLoaded(DIV2007_EXTkey)) {
	if (!defined ('PATH_BE_div2007')) {
		define('PATH_BE_div2007', t3lib_extMgm::extPath(DIV2007_EXTkey));
	}
} else {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXTkey]['useFlexforms'] = 0;
}

if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXTkey]['useFlexforms'] && defined('PATH_BE_div2007'))	{
	// replace the output of the former CODE field with the flexform
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][3][] = 'EXT:' . TT_GUEST_EXTkey . '/hooks/class.tx_ttguest_hooks_cms.php:&tx_ttguest_hooks_cms->pmDrawItem';
}

?>
