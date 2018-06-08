<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2016 Kasper Skårhøj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * guest_submit.php
 *
 * .notifyEmail =	email address that should be notified of submissions.
 * See TSref document / FEDATA section for details on how to use this script.
 * The static template 'plugin.tt_guest' provides a working example of configuration.
 * $Id: guest_submit.php 86950 2014-11-22 12:01:23Z franzholz $*
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Nicolas Liaudat <mailing (at) pompiers-chatel.ch>
 */


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\ErrorpageMessage;


if (is_object($this)) {

	$localCharset = $GLOBALS['TSFE']->localeCharset;
	$conf = $this->getConf('tt_guest');
	$row = $this->newData['tt_guest']['NEW'];
	$email = $row['cr_email'];
	$allowed = TRUE;
	$message = '';

	if (
		!$conf['emailCheck'] ||
		($allowed = \JambageCom\Div2007\Utility\MailUtility::checkMXRecord($email))
	) { // Added 02.06.2006 Nicolas Liaudat [mailing (at) pompiers-chatel.ch]
		if (is_array($row)) {
			do {
				$spamArray = GeneralUtility::trimExplode(',', $conf['spamWords']);
				$bSpamFound = FALSE;
				$internalFieldArray = array('hidden', 'pid', 'doublePostCheck', 'captcha');

				if ($conf['captcha'] == 'freecap' && ExtensionManagementUtility::isLoaded('sr_freecap')) {
					require_once(ExtensionManagementUtility::extPath('sr_freecap') . 'pi2/class.tx_srfreecap_pi2.php');
					$freeCapObj = GeneralUtility::getUserObj('&tx_srfreecap_pi2');
					if (!$freeCapObj->checkWord($row['captcha'])) {
						$allowed = FALSE;
						$message = 'Wrong captcha word entered';
						break;
					}
				}

				foreach ($row as $field => $value) {
					if (!in_array($field, $internalFieldArray)) {
						if (version_compare(phpversion(), '5.0.0', '>=')) {
							foreach ($spamArray as $k => $word) {
								if ($word && stripos($value, $word) !== FALSE) {
									$bSpamFound = TRUE;
									break;
								}
							}
						} else {
							foreach ($spamArray as $k => $word) {
								$lWord = strtolower($word);
								$lValue = strtolower($value);
								if ($lWord && strpos($lValue, $lWord) !== FALSE) {
									$bSpamFound = TRUE;
									break;
								}
							}
						}
					}
					if ($bSpamFound) {
						break;
					}
					$row[$field] = ($localCharset ? $GLOBALS['TSFE']->csConvObj->conv($value, $GLOBALS['TSFE']->renderCharset, $localCharset) : $value);
				}
				if ($bSpamFound) {
					$allowed = FALSE;
					$message = 'The spam word "' . $word . '" has been detected.';
					break;
				} else {
					$this->newData['tt_guest']['NEW']['cr_ip'] = GeneralUtility::getIndpEnv('REMOTE_ADDR');
					$this->execNEWinsert('tt_guest', $this->newData['tt_guest']['NEW']);
					$this->clear_cacheCmd(intval($this->newData['tt_guest']['NEW']['pid']));

					if ($conf['notifyEmail']) {
						$name = $this->newData['tt_guest']['NEW']['cr_name'];
						$name = ($localCharset ? $GLOBALS['TSFE']->csConvObj->conv($name, $GLOBALS['TSFE']->renderCharset, $localCharset) : $name);
						$email = $this->newData['tt_guest']['NEW']['cr_email'];

						mail ($conf['notifyEmail'], 'tt_guest item submitted at ' . GeneralUtility::getIndpEnv('HTTP_HOST'), '
			Page-id, tt_guest: ' . $this->newData['tt_guest']['NEW'][pid] . '
			Current page uid/title: ' . $GLOBALS['TSFE']->page[title] . '/' . $GLOBALS['TSFE']->page[uid] . '
			Name: ' . $name . '
			Email: ' . $email . '
			IP Address: ' . GeneralUtility::getIndpEnv('REMOTE_ADDR') . '
			Message: ' . $this->newData['tt_guest']['NEW']['title'] . '
			' . $this->newData['tt_guest']['NEW']['note'] . '

						','From: ' . $name . ' <' . $email . '>');
					}
				}
			} while (1 == 0);	// only once
		}
	}

	if (
		!$allowed
	) {
		if ($message == '') {
			$message = $email . ' is not a valid email address.';
		}

		$title = 'Entry denied!';
		$messagePage = GeneralUtility::makeInstance(ErrorpageMessage::class, $message, $title);
		$messagePage->output();
	}
}


