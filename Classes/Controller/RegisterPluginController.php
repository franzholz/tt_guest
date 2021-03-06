<?php

namespace JambageCom\TtGuest\Controller;

/***************************************************************
*  Copyright notice
*
*  (c) 2018 Kasper Skårhøj <kasperYYYY@typo3.com>
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
 * Creates a guestbook/comment-list.
 *
 * TypoScript config:
 * - See static_template 'plugin.tt_guest'
 * - See TS_ref.pdf
 *
 * Other resources:
 * 'Contoller/Submit.php' is used for submission of the guest book entries to the database. This is done through the FEData TypoScript object. See the file 'tt_guest/Configuration/TypoScript/Default/setup.txt' for an example of how to set this up.
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Franz Holzinger <franz@ttproducts.de>
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


use JambageCom\Div2007\Utility\TableUtility;

class RegisterPluginController extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
{
    /**
     * The backReference to the mother cObj object set at call time
     *
     * @var ContentObjectRenderer
     */
    public $cObj;

    /**
     * Should be same as classname of the plugin, used for CSS classes, variables
     *
     * @var string
     */
    public $prefixId = 'tt_guest';

    /**
     * Should normally be set in the main function with the TypoScript content passed to the method.
     *
     * $conf[LOCAL_LANG][_key_] is reserved for Local Language overrides.
     * $conf[userFunc] / $conf[includeLibs]  reserved for setting up the USER / USER_INT object. See TSref
     *
     * @var array
     */
    public $conf = array();

    public $extKey = TT_GUEST_EXT;	// The extension key.
    public $enableFields = '';		// The enablefields of the tt_guest table.
    public $dontParseContent = 0;
    public $orig_templateCode = '';
    public $config = array();	// updated configuration
    public $pid_list;			// list of page ids
    public $recordCount; 		// number of records


    /**
    * Main guestbook function.
    */
    public function main ($content, $conf)
    {
        $this->conf = $conf;

        if (ExtensionManagementUtility::isLoaded(DIV2007_EXT)) {

            $errorCode = array();
            $config = array();
            $bDoProcessing = $this->init($conf, $config, $errorCode);

            if ($bDoProcessing || count($errorCode)) {
                $content = $this->run(get_class($this), $conf, $config, $errorCode, $content);
            }
        } else {
            $content .= 'Error in Guestbook: Extension ' . DIV2007_EXT . ' has not been loaded.';
        }
        return $content;
    }

    /**
    * does the initialization stuff
    *
    * @param		string		  content string
    * @param		string		  configuration array
    * @param		string		  modified configuration array
    * @return	  void
    */
    public function init ($conf, &$config, &$errorCode)
    {

        $languageObj = GeneralUtility::makeInstance(\JambageCom\TtGuest\Api\Localization::class);
        $languageObj->init(
            TT_GUEST_EXT,
            $conf['_LOCAL_LANG.'],
            'Classes/RegisterPluginController.php'
        );

        $languageObj->loadLocalLang('EXT:' . TT_GUEST_EXT . '/pi/locallang.xlf', false);

            // pid_list is the pid/list of pids from where to fetch the guest items.
        $tmp = trim($this->cObj->stdWrap($conf['pid_list'], $conf['pid_list.']));
        $pid_list = $config['pid_list'] = ($conf['pid_list'] ? $conf['pid_list'] : trim($this->cObj->stdWrap($conf['pid_list'], $conf['pid_list.'])));
        $this->pid_list = ($pid_list ? $pid_list : $GLOBALS['TSFE']->id);

            // template is read.
        $this->orig_templateCode = $this->cObj->fileResource($conf['templateFile']);

            // Static Methods for Extensions for flexform functions
            // check the flexform
        $this->pi_initPIflexForm();
        $config['code'] = \tx_div2007_alpha5::getSetupOrFFvalue_fh004(
            $this,
            $conf['code'],
            $conf['code.'],
            $this->conf['defaultCode'],
            $this->cObj->data['pi_flexform'],
            'display_mode',
            true
        );

            // globally substituted markers, fonts and colors.
        $splitMark = md5(microtime());
        $globalMarkerArray = array();
        list($globalMarkerArray['###GW1B###'], $globalMarkerArray['###GW1E###']) = explode($splitMark, $this->cObj->stdWrap($splitMark, $conf['wrap1.']));
        list($globalMarkerArray['###GW2B###'], $globalMarkerArray['###GW2E###']) = explode($splitMark, $this->cObj->stdWrap($splitMark, $conf['wrap2.']));
        $globalMarkerArray['###GC1###'] = $this->cObj->stdWrap($conf['color1'], $conf['color1.']);
        $globalMarkerArray['###GC2###'] = $this->cObj->stdWrap($conf['color2'], $conf['color2.']);
        $globalMarkerArray['###GC3###'] = $this->cObj->stdWrap($conf['color3'], $conf['color3.']);

            // If the current record should be displayed.
        $config['displayCurrentRecord'] = $conf['displayCurrentRecord'];
        if ($config['displayCurrentRecord']) {
            $config['code'] = 'GUESTBOOK';
        }

        // *************************************
        // *** doing the things...:
        // *************************************
        $this->enableFields = TableUtility::enableFields('tt_guest');
        $this->dontParseContent = $conf['dontParseContent'];
        $this->recordCount = $this->getRecordCount($this->pid_list);
        $globalMarkerArray['###PREVNEXT###'] = $this->getPrevNext();

            // Substitute Global Marker Array
        $this->orig_templateCode =
            $this->cObj->substituteMarkerArray(
                $this->orig_templateCode,
                $globalMarkerArray
            );

        return true;
    }

    public function run ($pibaseClass, $conf, $config, &$errorCode, $content = '')
    {
        $alternativeLayouts = intval($conf['alternatingLayouts']) > 0 ? intval($conf['alternatingLayouts']) : 2;
        $codes = GeneralUtility::trimExplode(',', $config['code'], 1);
        if (!count($codes)) {
            $codes = array('');
        }

        $languageObj = GeneralUtility::makeInstance(\JambageCom\TtGuest\Api\Localization::class);

        if ($errorCode[0]) {
            $content .= \tx_div2007_error::getMessage($languageObj, $errorCode);
            return $content;
        }
        $table = 'tt_guest';

        foreach($codes as $theCode) {
            $theCode = (string) strtoupper(trim($theCode));
            switch($theCode) {
                case 'GUESTBOOK':
                    $local_cObj = \JambageCom\Div2007\Utility\FrontendUtility::getContentObjectRenderer();  // Initiate new cObj, because we're loading the data-array
                    $lConf = $conf;

                    if (!$lConf['subpartMarker']) {
                        $lConf['subpartMarker'] = 'TEMPLATE_GUESTBOOK';
                    }

                        // Getting template subpart from file.
                    $templateCode = $local_cObj->getSubpart($this->orig_templateCode, '###' . $lConf['subpartMarker'] . '###');

                    if ($templateCode) {
                            // Getting the specific parts of the template
                        $postHeader = $this->getLayouts($templateCode, $alternativeLayouts, 'POST');

                            // Fetching the guest book item(s) to display:
                        if ($config['displayCurrentRecord']) {
                            $recentPosts = array();
                            $recentPosts[] = $this->cObj->data;
                        } else {
                            $recentPosts = $this->getItems($this->pid_list);
                        }
                            // Traverse the items and display them:
                        reset($recentPosts);
                        $c_post = 0;
                        $subpartContent = '';
                        foreach($recentPosts as $recentPost) {
                                // Passing data through stdWrap and into the markerArray
                            $local_cObj->start($recentPost);		// Set this->data to the current record tt_guest record.
                            $markerArray = array();
                            $markerArray['###POST_TITLE###'] =
                                $local_cObj->stdWrap(
                                    $this->formatStr($recentPost['title']),
                                    $conf['title_stdWrap.']
                                );
                            $markerArray['###POST_CONTENT###'] =
                                $local_cObj->stdWrap(
                                    $this->formatStr($recentPost['note']),
                                    $conf['note_stdWrap.']
                                );
                            $markerArray['###POST_AUTHOR###'] =
                                $local_cObj->stdWrap(
                                    $this->formatStr($recentPost['cr_name']),
                                    $conf['author_stdWrap.']
                                );
                            $markerArray['###POST_EMAIL###'] =
                                $local_cObj->stdWrap(
                                    $this->formatStr($recentPost['cr_email']),
                                    $conf['email_stdWrap.']
                                );
                            $markerArray['###POST_WWW###'] =
                                $local_cObj->stdWrap(
                                    $this->formatStr($recentPost['www']),
                                    $conf['www_stdWrap.']
                                );
                            $markerArray['###POST_DATE###'] =
                                $local_cObj->stdWrap(
                                    $recentPost['crdate'],
                                    $conf['date_stdWrap.']
                                );
                            $markerArray['###POST_TIME###'] =
                                $local_cObj->stdWrap(
                                    $recentPost['crdate'],
                                    $conf['time_stdWrap.']
                                );
                            $markerArray['###POST_AGE###'] =
                                $local_cObj->stdWrap(
                                    $recentPost['crdate'],
                                    $conf['age_stdWrap.']
                                );
                                // Substitute the markerArray in the proper template code (POST subparts, alternating)
                            $out=$postHeader[$c_post % count($postHeader)];
                            $c_post++;
                            $subpartContent .=
                                $local_cObj->substituteMarkerArrayCached(
                                    $out,
                                    $markerArray
                                );
                        }

                            // Total Substitution:
                        if ($lConf['requireRecords'] && !count($recentPosts)) {
                            $content .= '';
                        } else {
                            $subpartArray = array();
                            $subpartArray['###CONTENT###'] = $subpartContent;
                            $markerArray = array();
                            $markerArray['###COMMENTS###'] = $languageObj->getLabel('comments');
                            $content .=
                                $local_cObj->substituteMarkerArrayCached(
                                    $templateCode,
                                    $markerArray,
                                    $subpartArray
                                );
                        }
                    } else {
                        debug('No template code for the subpart maker ###' . $lConf['subpartMarker'] . '###'); // keep this
                    }

                break;
                case 'POSTFORM':
                    $pidArray =
                        GeneralUtility::trimExplode(
                            ',',
                            $this->pid_list
                        );
                    $pid = $pidArray[0];
                    $form =
                        GeneralUtility::makeInstance(
                            \JambageCom\TtGuest\View\Form::class
                        );
                    $newContent =
                        $form->render(
                            $languageObj,
                            $theCode,
                            $pid,
                            intval($conf['PIDprivacyPolicy']),
                            $conf['captcha'],
                            $conf['postform.']
                        );

                    $content .= $newContent;
                break;
                default:	// 'HELP'
                    $GLOBALS['TSFE']->set_no_cache();
                    $contentTmp = 'error';
                break;
            }

            if ($contentTmp == 'error') {
                if (ExtensionManagementUtility::isLoaded(DIV2007_EXT)) {
                    $content .= \tx_div2007_alpha5::displayHelpPage_fh003(
                        $languageObj,
                        $this->cObj,
                        $this->cObj->fileResource('EXT:' . TT_GUEST_EXT . '/pi/guest_help.tmpl'),
                        TT_GUEST_EXT,
                        $this->errorMessage,
                        $theCode
                    );
                    unset($this->errorMessage);
                } else {
                    $langKey = strtoupper($GLOBALS['TSFE']->config['config']['language']);
                    $helpTemplate =
                        $this->cObj->fileResource(
                            'EXT:' . TT_GUEST_EXT . '/pi/guest_help.tmpl'
                        );

                        // Get language version
                    $helpTemplate_lang = '';
                    if ($langKey) {
                        $helpTemplate_lang =
                            $this->cObj->getSubpart(
                                $helpTemplate,
                                '###TEMPLATE_' . $langKey . '###'
                            );
                    }

                    $helpTemplate =
                        $helpTemplate_lang ?
                            $helpTemplate_lang :
                            $this->cObj->getSubpart(
                                $helpTemplate,
                                '###TEMPLATE_DEFAULT###'
                            );

                        // Markers and substitution:
                    $markerArray['###CODE###'] = $theCode;
                    $markerArray['###PATH###'] = PATH_BE_TTGUEST;
                    $content .=
                        $this->cObj->substituteMarkerArray(
                            $helpTemplate,
                            $markerArray
                        );
                }
                break; // while
            }
        }
        $result = $this->pi_wrapInBaseClass($content);
        return $result;
    }

    /**
    * Main guestbook function.
    */
    public function getLayouts ($templateCode, $alternativeLayouts, $marker)
    {
        $out = array();
        for($a = 0; $a < $alternativeLayouts; $a++) {
            $m = '###' . $marker . ($a ? '_' . $a : '') . '###';
            if(strstr($templateCode, $m)) {
                $out[] = $GLOBALS['TSFE']->cObj->getSubpart($templateCode, $m);
            } else {
                break;
            }
        }
        return $out;
    }


    /**
    * Main guestbook function.
    */
    public function getItems ($pid_list)
    {
        if(!isset($_REQUEST['offset'])) {
            $offset = 0;
        }
        else {
            $offset = (int) $_REQUEST['offset'];
        }

        $out = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'tt_guest',
            'pid IN (' . $pid_list . ')' . $this->enableFields,
            '',
            'crdate DESC',
            $offset . ', ' . $this->conf['limit']
        );

        return $out;
    }


    /**
    * Main guestbook function.
    */
    public function formatStr ($str)
    {
        if (!$this->dontParseContent) {
            return nl2br(htmlspecialchars($str));
        } else {
            return $str;
        }
    }


    public function getRecordCount ($pid_list)
    {
        $thecount = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
            '*',
            'tt_guest',
            'pid_list IN (' . $pid_list . ')' . $this->enableFields
        );

        return $thecount;
    }

    public function getPrevNext ()
    {
        $languageObj = GeneralUtility::makeInstance(\JambageCom\TtGuest\Api\Localization::class);

        $nav = GeneralUtility::makeInstance(
            \JambageCom\TtGuest\View\RecordNavigator::class,
            $this->recordCount,
            intval($_REQUEST['offset']),
            $this->conf['limit']
        );
        $nav->createSequence();

        $setupArray = array(0 => 'previousLabel', 1 => 'nextLabel');
        $labelArray =  array();
        foreach ($setupArray as $k => $labelKey) {
            if ($this->conf[$labelKey]) {
                $labelArray[$k] = $this->conf[$labelKey];
            } else {
                $labelArray[$k] =
                    $languageObj->getLabel(
                        $labelKey
                    );
            }
        }
        $previousLabel = $this->conf['previousLabel'];
        $nav->createPrevNext($labelArray[0], $labelArray[1]);
        $result = $nav->getNavigator();
        return $result;
    }
}

