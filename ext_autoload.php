<?php

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

if (
	class_exists($emClass) &&
	method_exists($emClass, 'extPath')
) {
	// nothing
} else {
	$emClass = 't3lib_extMgm';
}

$key = 'tt_guest';
$extensionPath = call_user_func($emClass . '::extPath', $key, $script);

return array(
	'tx_ttguest' => $extensionPath . 'pi/class.tx_ttguest.php',
	'tx_ttguest_hooks_cms' => $extensionPath . 'hooks/class.tx_ttguest_hooks_cms.php',
	'tx_ttguest_language' => $extensionPath . 'model/class.tx_ttguest_language.php',
	'tx_ttguest_recordnavigator' => $extensionPath . 'pi/class.tx_ttguest_RecordNavigator.php',
	'tx_ttguest_wizicon' => $extensionPath . 'class.tx_ttguest_wizicon.php',
);

