<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2016 Kasper Skårhøj <kasperYYYY@typo3.com>
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
 * guestLib.inc
 *
 * Creates a guestbook/comment-list.
 *
 * TypoScript config:
 * - See static_template 'plugin.tt_guest'
 * - See TS_ref.pdf
 *
 * Other resources:
 * 'guest_submit.inc' is used for submission of the guest book entries to the database. This is done through the FEData TypoScript object. See the file 'tt_guest/Configuration/TypoScript/Default/setup.txt' for an example of how to set this up.
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Franz Holzinger <franz@ttproducts.de>
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


use JambageCom\Div2007\Utility\TableUtility;



class tx_ttguest extends tslib_pibase {
	public $prefixId = 'tx_ttguest';	// Same as class name
	public $scriptRelPath = 'pi/class.tx_ttguest.php';	// Path to this script relative to the extension dir.
	public $extKey = TT_GUEST_EXT;	// The extension key.
	public $cObj;			// The backReference to the mother cObj object set at call time
	public $enableFields ='';		// The enablefields of the tt_guest table.
	public $dontParseContent = 0;
	public $orig_templateCode = '';
	public $config = array();		// updated configuration
	public $pid_list;			// list of page ids
	public $recordCount; 		// number of records
	public $freeCap;


	/**
	 * Main guestbook function.
	 */
	public function main ($content, $conf) {

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
	public function init ($conf, &$config, &$errorCode) {

		$langObj = GeneralUtility::getUserObj('&tx_ttguest_language');
		$langObj->init1($this, $this->cObj, $conf, 'pi/class.tx_ttguest.php');

			// pid_list is the pid/list of pids from where to fetch the guest items.
		$tmp = trim($this->cObj->stdWrap($conf['pid_list'], $conf['pid_list.']));
		$pid_list = $config['pid_list'] = ($conf['pid_list'] ? $conf['pid_list'] : trim($this->cObj->stdWrap($conf['pid_list'], $conf['pid_list.'])));
		$this->pid_list = ($pid_list ? $pid_list : $GLOBALS['TSFE']->id);

			// template is read.
		$this->orig_templateCode = $this->cObj->fileResource($conf['templateFile']);

			// Static Methods for Extensions for flexform functions
			// check the flexform
		$this->pi_initPIflexForm();
		$config['code'] = tx_div2007_alpha5::getSetupOrFFvalue_fh004(
			$this,
			$conf['code'],
			$conf['code.'],
			$this->conf['defaultCode'],
			$this->cObj->data['pi_flexform'],
			'display_mode',
			TRUE
		);

		tx_div2007_alpha5::loadLL_fh002($langObj, 'EXT:' . TT_GUEST_EXT . '/pi/locallang.xlf');

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

		if (
			$this->conf['captcha'] == 'freecap' && ExtensionManagementUtility::isLoaded('sr_freecap')
		) {
			require_once(ExtensionManagementUtility::extPath('sr_freecap') . 'pi2/class.tx_srfreecap_pi2.php');
			$this->freeCap = GeneralUtility::getUserObj('&tx_srfreecap_pi2');
		}

		return TRUE;
	}


	public function run ($pibaseClass, $conf, $config, &$errorCode, $content = '') {

		$cObj = GeneralUtility::makeInstance('tslib_cObj');	// Initiate new cObj, because we're loading the data-array
		$alternativeLayouts = intval($conf['alternatingLayouts']) > 0 ? intval($conf['alternatingLayouts']) : 2;
		$codes = GeneralUtility::trimExplode(',', $config['code'], 1);
		if (!count($codes)) {
			$codes = array('');
		}

		$langObj = GeneralUtility::getUserObj('&tx_ttguest_language');

		if ($errorCode[0]) {
			$content .= tx_div2007_error::getMessage($langObj, $errorCode);
			return $content;
		}

		foreach($codes as $theCode) {
			$theCode = (string) strtoupper(trim($theCode));
			switch($theCode) {
				case 'GUESTBOOK':
					$lConf = $conf;

					if (!$lConf['subpartMarker']) {
						$lConf['subpartMarker'] = 'TEMPLATE_GUESTBOOK';
					}

						// Getting template subpart from file.
					$templateCode = $cObj->getSubpart($this->orig_templateCode, '###' . $lConf['subpartMarker'] . '###');

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
							$cObj->start($recentPost);		// Set this->data to the current record tt_guest record.
							$markerArray = array();
							$markerArray['###POST_TITLE###'] =
								$cObj->stdWrap(
									$this->formatStr($recentPost['title']),
									$conf['title_stdWrap.']
								);
							$markerArray['###POST_CONTENT###'] =
								$cObj->stdWrap(
									$this->formatStr($recentPost['note']),
									$conf['note_stdWrap.']
								);
							$markerArray['###POST_AUTHOR###'] =
								$cObj->stdWrap(
									$this->formatStr($recentPost['cr_name']),
									$conf['author_stdWrap.']
								);
							$markerArray['###POST_EMAIL###'] =
								$cObj->stdWrap(
									$this->formatStr($recentPost['cr_email']),
									$conf['email_stdWrap.']
								);
							$markerArray['###POST_WWW###'] =
								$cObj->stdWrap(
									$this->formatStr($recentPost['www']),
									$conf['www_stdWrap.']
								);
							$markerArray['###POST_DATE###'] =
								$cObj->stdWrap(
									$recentPost['crdate'],
									$conf['date_stdWrap.']
								);
							$markerArray['###POST_TIME###'] =
								$cObj->stdWrap(
									$recentPost['crdate'],
									$conf['time_stdWrap.']
								);
							$markerArray['###POST_AGE###'] =
								$cObj->stdWrap(
									$recentPost['crdate'],
									$conf['age_stdWrap.']
								);
								// Substitute the markerArray in the proper template code (POST subparts, alternating)
							$out=$postHeader[$c_post % count($postHeader)];
							$c_post++;
							$subpartContent .=
								$cObj->substituteMarkerArrayCached(
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
							$markerArray['###COMMENTS###'] = tx_div2007_alpha5::getLL_fh003($langObj, 'comments');
							$content .=
								$cObj->substituteMarkerArrayCached(
									$templateCode,
									$markerArray,
									$subpartArray
								);
						}
					} else {
						debug('No template code for the subpart maker ###' . $lConf['subpartMarker'] . '###');
					}

				break;
				case 'POSTFORM':
					$lConf = $conf['postform.'];
					$setupArray =
						array(
							'10' => 'title',
							'20' => 'note',
							'30' => 'cr_name',
							'40' => 'cr_email',
							'50' => 'www',
							'60' => 'post'
						);

					foreach ($setupArray as $k => $type) {
						if ($k == '60') {
							$field = 'value';
						} else {
							$field = 'label';
						}

						if (is_array($lConf['dataArray.'][$k . '.'])) {
							if (
								(!$this->LLkey || $this->LLkey == 'en') && !$lConf['dataArray.'][$k . '.'][$field] ||
								($this->LLkey != 'en' &&
									!is_array($lConf['dataArray.'][$k . '.'][$field . '.']) ||  !is_array($lConf['dataArray.'][$k . '.'][$field . '.']['lang.']) || !is_array($lConf['dataArray.'][$k . '.'][$field . '.']['lang.'][$this->LLkey . '.'])
								)
							) {
								$lConf['dataArray.'][$k . '.'][$field] = tx_div2007_alpha5::getLL_fh003($langObj, $type);
							}
						}
					}

					if (is_object($this->freeCap)) {
						$freecapMarker = $this->freeCap->makeCaptcha();
						$lConf['dataArray.']['55.'] = array(
							'label' => $freecapMarker['###SR_FREECAP_IMAGE###'] . '<br>' . $freecapMarker['###SR_FREECAP_NOTICE###'] . '<br>' . $freecapMarker['###SR_FREECAP_CANT_READ###'],
							'type' => '*data[tt_guest][NEW][captcha]=input,60'
						);
					}
					$tmp = $cObj->FORM($lConf);
					$content .= $tmp;
				break;
				default:	// 'HELP'
					$GLOBALS['TSFE']->set_no_cache();
					$contentTmp = 'error';
				break;
			}

			if ($contentTmp == 'error') {
				if (ExtensionManagementUtility::isLoaded(DIV2007_EXT)) {
					$content .= tx_div2007_alpha5::displayHelpPage_fh003(
						$langObj,
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
	public function getLayouts ($templateCode, $alternativeLayouts, $marker) {
		$out = array();
		for($a = 0; $a < $alternativeLayouts; $a++) {
			$m = '###' . $marker . ($a ? '_' . $a : '') . '###';
			if(strstr($templateCode,$m)) {
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
	public function getItems ($pid) {
		if(!isset($_REQUEST['offset'])) {
			$offset = 0;
		}
		else {
			$offset = (int) $_REQUEST['offset'];
		}

		$out = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tt_guest',
			'pid IN (' . $pid . ')' . $this->enableFields,
			'',
			'crdate DESC',
			$offset . ', ' . $this->conf['limit']
		);

		return $out;
	}


	/**
	 * Main guestbook function.
	 */
	public function formatStr ($str) {
		if (!$this->dontParseContent) {
			return nl2br(htmlspecialchars($str));
		} else {
			return $str;
		}
	}


	public function getRecordCount ($pid) {
		$thecount = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
			'*',
			'tt_guest',
			'pid IN (' . $pid . ')' . $this->enableFields
		);

		return $thecount;
	}

	public function getPrevNext () {
		$langObj = GeneralUtility::getUserObj('&tx_ttguest_language');

		$nav = GeneralUtility::makeInstance(
			tx_ttguest_RecordNavigator::class,
			$this->recordCount,
			$_REQUEST['offset'],
			$this->conf['limit'],
			'?pid=' . $GLOBALS['TSFE']->id
		);
		$nav->createSequence();

		$setupArray = array(0 => 'previousLabel', 1 => 'nextLabel');
		$labelArray =  array();
		foreach ($setupArray as $k => $labelKey) {
			if ($this->conf[$labelKey]) {
				$labelArray[$k] = $this->conf[$labelKey];
			} else {
				$labelArray[$k] = tx_div2007_alpha5::getLL_fh003($langObj, $labelKey);
			}
		}
		$previousLabel = $this->conf['previousLabel'];
		$nav->createPrevNext($labelArray[0], $labelArray[1]);
		$result = $nav->getNavigator();
		return $result;
	}
}

