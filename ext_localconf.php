<?php

if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:

if (!defined ('TT_GUEST_EXT')) {
    define('TT_GUEST_EXT', $_EXTKEY);
}

if (!defined ('PATH_BE_TTGUEST')) {
    define('PATH_BE_TTGUEST', call_user_func($emClass . '::extPath', $_EXTKEY));
}

if (!defined ('PATH_BE_TTGUEST_REL')) {
    define('PATH_BE_TTGUEST_REL', call_user_func($emClass . '::extRelPath', $_EXTKEY));
}

if (!defined ('PATH_FE_TTGUEST_REL')) {
    define('PATH_FE_TTGUEST_REL', call_user_func($emClass . '::siteRelPath', $_EXTKEY));
}

if (!defined ('TT_GUEST_CSS_PREFIX')) {
    define('TT_GUEST_CSS_PREFIX', 'tx-ttguest-');
}


if (isset($_EXTCONF) && is_array($_EXTCONF)) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY] = $_EXTCONF;
    if (isset($tmpArray) && is_array($tmpArray)) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY] =
            array_merge($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY], $tmpArray);
    }
} else {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY] = array();
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['useFlexforms'] = 1;
}


if (
    TYPO3_MODE == 'BE'
) {
    // replace the output of the former CODE field with the flexform
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][3][] = 'JambageCom\\TtGuest\\Hooks\\CmsBackend->pmDrawItem';
    call_user_func($emClass . '::addUserTSConfig', 'options.saveDocNew.tt_guest=1');
}


// add missing setup for the tt_content "list_type = 3" which is used by tt_guest
$addLine = 'tt_content.list.20.3 = < plugin.tt_guest';

call_user_func(
    $emClass . '::addTypoScript',
    $_EXTKEY,
    'setup', '
# Setting ' . $_EXTKEY . ' plugin TypoScript
' . $addLine . '
',
    43
);


// Configure captcha hooks
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'] = [];
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'][] = 'JambageCom\\Div2007\\Captcha\\Captcha';
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'][] = 'JambageCom\\Div2007\\Captcha\\Freecap';
}

