<?php
if (!defined ('TYPO3_MODE'))	die ('Access denied.');

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


	// turn the use of flexforms on:

if (!defined ('FH_LIBRARY_EXTkey')) {
	define('FH_LIBRARY_EXTkey','fh_library');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXTkey]['useFlexforms'] = $_EXTCONF['useFlexforms'];
if (t3lib_div::int_from_ver($GLOBALS['TYPO_VERSION']) < 3007000 || !t3lib_extMgm::isLoaded(FH_LIBRARY_EXTkey)) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXTkey]['useFlexforms'] = 0;
}

if (t3lib_extMgm::isLoaded(FH_LIBRARY_EXTkey)) {
	if (!defined ('PATH_BE_fh_library')) {
		define('PATH_BE_fh_library', t3lib_extMgm::extPath(FH_LIBRARY_EXTkey));
	}
}


?>
