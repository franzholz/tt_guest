<?php

namespace JambageCom\TtGuest\Controller;


/***************************************************************
*  Copyright notice
*
*  (c) 2018 Kasper Skårhøj (kasperYYYY@typo3.com)
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

use JambageCom\TslibFetce\Controller\TypoScriptFrontendDataController;
use JambageCom\Div2007\Utility\MailUtility;
use JambageCom\TtGuest\Constants\Field;

class Submit implements \TYPO3\CMS\Core\SingletonInterface
{
    static public function execute (TypoScriptFrontendDataController $pObj, $conf)
    {
        $result = true;
        $table = 'tt_guest';

        $localCharset = $GLOBALS['TSFE']->localeCharset;
        $row = $pObj->newData[$table]['NEW'];
        $prefixId = $row['prefixid'];
        unset($row['prefixid']);
        $pid = intval($row['pid']);

        $email = $row['cr_email'];
        $name = $row['cr_name'];
        $title = $row['title'];
        $allowed = true;
        $message = '';

        if (
            !$conf['emailCheck'] ||
            ($allowed = \JambageCom\Div2007\Utility\MailUtility::checkMXRecord($email))
        ) { // Added 02.06.2006 Nicolas Liaudat [mailing (at) pompiers-chatel.ch]
            if (is_array($row)) {
                do {
                    $spamArray = GeneralUtility::trimExplode(',', $conf['spamWords']);
                    $spamFound = false;
                    $internalFieldArray = array('hidden', 'pid', 'doublePostCheck', Field::CAPTCHA);
                    $captchaError = false;

                    if (
                        isset($row[Field::CAPTCHA]) &&
                        $captcha =
                            \JambageCom\Div2007\Captcha\CaptchaManager::getCaptcha(
                                TT_GUEST_EXT,
                                $conf['captcha']
                            )
                    ) {
                        if (
                            !$captcha->evalValues(
                                $row[Field::CAPTCHA],
                                $conf['captcha']
                            )
                        ) {
                            $captchaError = true;
                        }
                    } else if ($conf['captcha']) { // wrong captcha configuration or manipulation of the submit form
                        $captchaError = true;                        
                    }
                    if ($captchaError) {
                        $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT]['error']['captcha'] = true;
                        $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT]['row'] = $row;
                        $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT]['word'] = $row[Field::CAPTCHA];
                        $result = false;
                        break;
                    }

                    foreach ($row as $field => $value) {
                        if (!in_array($field, $internalFieldArray)) {
                            if (version_compare(phpversion(), '5.0.0', '>=')) {
                                foreach ($spamArray as $k => $word) {
                                    if ($word && stripos($value, $word) !== false) {
                                        $spamFound = true;
                                        break;
                                    }
                                }
                            } else {
                                foreach ($spamArray as $k => $word) {
                                    $lWord = strtolower($word);
                                    $lValue = strtolower($value);
                                    if ($lWord && strpos($lValue, $lWord) !== false) {
                                        $spamFound = true;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($spamFound) {
                            break;
                        }
                        $row[$field] = ($localCharset ? $GLOBALS['TSFE']->csConvObj->conv($value, $GLOBALS['TSFE']->renderCharset, $localCharset) : $value);
                    }

                    if ($spamFound) {
                        $allowed = false;
                        $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT]['error']['spam'] = true;
                        $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT]['row'] = $row;
                        $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT]['word'] = $word;
                        $result = false;
                        break;
                    } else {
                        $excludeArray = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_GUEST_EXT]['exclude.'];
                        if (
                            !GeneralUtility::inList(
                                $excludeArray[$table],
                                'cr_ip'
                            )
                        ) {                     
                            $row['cr_ip'] = GeneralUtility::getIndpEnv('REMOTE_ADDR');
                        }

                        if (isset($row[Field::CAPTCHA])) {
                            unset($row[Field::CAPTCHA]);
                        }

                            // Plain insert of record:
                        $pObj->execNEWinsert($table, $row);
                        $newId = $GLOBALS['TYPO3_DB']->sql_insert_id();
                        $pObj->clear_cacheCmd($pid);

                        if ($conf['notifyEmail']) {
                            $message =  chr(10) . 'Page-id, tt_guest: ' . $pid . chr(10) . 'Current page uid/title: ' . $GLOBALS['TSFE']->page['title'] . '/' . $pid .  chr(10) . 'Name: ' . $name . chr(10) . 'Email: ' . $email . chr(10) . 'IP Address: ###CR_IP###' .  chr(10) . 'Message: ' . $title .  chr(10) . '"' . $row['note'] . '"' . chr(10) . 'From: ' . $name . ' <' . $email . '>';
                            $messageTitle = 'tt_guest item submitted at ' . GeneralUtility::getIndpEnv('HTTP_HOST');

                            $markersArray['###CR_IP###'] = $row['cr_ip'];

                            foreach($markersArray as $marker => $markContent) {
                                $message = str_replace($marker, $markContent, $message);
                            }
 
                            MailUtility::send(
                                $conf['notifyEmail'],
                                $messageTitle,
                                $message,
                                '',
                                $email,
                                $name
                            );
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

        if ($result) {
            // delete any formerly stored values
            $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT] = array();
        }
        return $result;
    }
}

