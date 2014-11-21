<?php

$extensionPath = t3lib_extMgm::extPath('tt_guest');
return array(
	'tx_ttguest' => $extensionPath . 'pi/class.tx_ttguest.php',
	'tx_ttguest_hooks_cms' => $extensionPath . 'hooks/class.tx_ttguest_hooks_cms.php',
	'tx_ttguest_language' => $extensionPath . 'model/class.tx_ttguest_language.php',
	'tx_ttguest_recordnavigator' => $extensionPath . 'pi/class.tx_ttguest_RecordNavigator.php',
	'tx_ttguest_wizicon' => $extensionPath . 'class.tx_ttguest_wizicon.php',
);
?>