<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2006 Kasper Skårhøj <kasperYYYY@typo3.com>
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
 * 'guest_submit.inc' is used for submission of the guest book entries to the database. This is done through the FEData TypoScript object. See the file 'ext_typoscript_setup.txt' for an example of how to set this up.
 * 
 * $Id$
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Franz Holzinger <kontakt@fholzinger.com>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_BE_ttguest.'pi/class.tx_ttguest_RecordNavigator.php');


class tx_ttguest extends tslib_pibase {
	var $prefixId = 'tx_ttguest';	// Same as class name
	var $scriptRelPath = 'pi/class.tx_ttguest.php';	// Path to this script relative to the extension dir.
	var $extKey = TT_GUEST_EXTkey;	// The extension key.

	var $cObj;		// The backReference to the mother cObj object set at call time

	var $enableFields ='';		// The enablefields of the tt_board table.
	var $dontParseContent=0;

	var $orig_templateCode='';

	var $config=array();				// updated configuration

	var $pid_list;			// list of page ids
	var $recordCount; 		// number of records

	/**
	 * Class Constructor (true constructor)
	 * Initializes $this->piVars if $this->prefixId is set to any value
	 * Will also set $this->LLkey based on the config.language setting.
	 *
	 * @return	void
	 */
	function tslib_pibase()	{
		global $TSFE;
		
		$TSFE->makeCacheHash();
		parent::tslib_pibase();
	}

	/**
	 * Main guestbook function.
	 */
	function main_guestbook($content,$conf)	{
		global $TSFE;
		
		$this->conf = $conf;

		$this->init ($content, $conf, $this->config);	
		$cObj = t3lib_div::makeInstance('tslib_cObj');	// Initiate new cObj, because we're loading the data-array
		// list($pid) = t3lib_div::trimExplode(',',$this->pid_list);
		
		$alternativeLayouts = intval($conf['alternatingLayouts'])>0 ? intval($conf['alternatingLayouts']) : 2;
		$codes=t3lib_div::trimExplode(',', $this->config['code'],1);
		if (!count($codes))	$codes=array('');

		while(list(,$theCode)=each($codes))	{
			$theCode = (string)strtoupper(trim($theCode));
			switch($theCode)	{
				case 'GUESTBOOK':
					$lConf=$conf;

					if (!$lConf['subpartMarker'])	{
						$lConf['subpartMarker']='TEMPLATE_GUESTBOOK';
					}

						// Getting template subpart from file.
					$templateCode = $cObj->getSubpart($this->orig_templateCode, '###'.$lConf['subpartMarker'].'###');
					if ($templateCode)	{
							// Getting the specific parts of the template
						$postHeader=$this->getLayouts($templateCode,$alternativeLayouts,'POST');

							// Fetching the guest book item(s) to display:
						if ($config['displayCurrentRecord'])	{
							$recentPosts=array();
							$recentPosts[] = $this->cObj->data;
						} else {
							$recentPosts = $this->getItems($this->pid_list);
						}
							// Traverse the items and display them:
						reset($recentPosts);
						$c_post=0;
						$subpartContent='';
						while(list(,$recentPost)=each($recentPosts))	{
								// Passing data through stdWrap and into the markerArray
							$cObj->start($recentPost);		// Set this->data to the current record tt_guest record.
							$markerArray=array();
							$markerArray['###POST_TITLE###'] = $cObj->stdWrap($this->formatStr($recentPost['title']), $conf['title_stdWrap.']);
							$markerArray['###POST_CONTENT###'] = $cObj->stdWrap($this->formatStr($recentPost['note']), $conf['note_stdWrap.']);
							$markerArray['###POST_AUTHOR###'] = $cObj->stdWrap($this->formatStr($recentPost['cr_name']), $conf['author_stdWrap.']);
							$markerArray['###POST_EMAIL###'] = $cObj->stdWrap($this->formatStr($recentPost['cr_email']), $conf['email_stdWrap.']);
							$markerArray['###POST_WWW###'] = $cObj->stdWrap($this->formatStr($recentPost['www']), $conf['www_stdWrap.']);
							$markerArray['###POST_DATE###'] = $cObj->stdWrap($recentPost['crdate'],$conf['date_stdWrap.']);
							$markerArray['###POST_TIME###'] = $cObj->stdWrap($recentPost['crdate'],$conf['time_stdWrap.']);
							$markerArray['###POST_AGE###'] = $cObj->stdWrap($recentPost['crdate'],$conf['age_stdWrap.']);
								// Substitute the markerArray in the proper template code (POST subparts, alternating)
							$out=$postHeader[$c_post%count($postHeader)];
							$c_post++;
							$subpartContent.=$cObj->substituteMarkerArrayCached($out,$markerArray);
						}

							// Total Substitution:
						if ($lConf['requireRecords'] && !count($recentPosts))	{
							$content.= '';
						} else {
							$content.= $cObj->substituteSubpart($templateCode,'###CONTENT###',$subpartContent) ;
						}
					} else {
						debug('No template code for the subpart maker ###'.$lConf['subpartMarker'].'###');
					}

				break;
				case 'POSTFORM':
					$lConf = $conf['postform.'];
					$tmp = $cObj->FORM($lConf);
					$content.=$tmp;
				break;
				default:	// 'HELP'
					$TSFE->set_no_cache();
					$contentTmp = 'error';
				break;
			}
			if ($contentTmp == 'error') {
				if (t3lib_extMgm::isLoaded(FH_LIBRARY_EXTkey))	{
					$content .= tx_fhlibrary_view::displayHelpPage(
						$this,
						$this->cObj->fileResource('EXT:'.TT_GUEST_EXTkey.'/pi/guest_help.tmpl'),
						TT_GUEST_EXTkey,
						$this->errorMessage
					);
					unset($this->errorMessage);
				} else {
					$langKey = strtoupper($TSFE->config['config']['language']);
					$helpTemplate = $this->cObj->fileResource('EXT:'.TT_GUEST_EXTkey.'/pi/guest_help.tmpl');

						// Get language version
					$helpTemplate_lang='';
					if ($langKey)	{$helpTemplate_lang = $this->cObj->getSubpart($helpTemplate,'###TEMPLATE_'.$langKey.'###');}
					$helpTemplate = $helpTemplate_lang ? $helpTemplate_lang : $this->cObj->getSubpart($helpTemplate,'###TEMPLATE_DEFAULT###');

						// Markers and substitution:
					$markerArray['###CODE###'] = $theCode;
					$markerArray['###PATH###'] = PATH_BE_ttguest;
					$content.=$this->cObj->substituteMarkerArray($helpTemplate,$markerArray);					
				}
				break; // while
			}		

		}

		$rc = $this->pi_wrapInBaseClass($content);
		return $rc;
	}


	/**
	 * does the initialization stuff
	 *
	 * @param		string		  content string
	 * @param		string		  configuration array
	 * @param		string		  modified configuration array
	 * @return	  void
 	 */
	function init (&$content,&$conf,&$config) {
		global $TSFE;
		
			// pid_list is the pid/list of pids from where to fetch the guest items.
		$tmp = trim($this->cObj->stdWrap($conf['pid_list'],$conf['pid_list.']));
		// $config['pid_list'] = $config['pid_list'] ? implode(t3lib_div::intExplode(',',$config['pid_list']),',') : $TSFE->id;
		
		$pid_list = $config['pid_list'] = ($conf['pid_list'] ? $conf['pid_list'] :trim($this->cObj->stdWrap($conf['pid_list'],$conf['pid_list.'])));
		$this->pid_list = ($pid_list ? $pid_list : $TSFE->id);

			// template is read.
		$this->orig_templateCode = $this->cObj->fileResource($conf['templateFile']);

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['useFlexforms'] && t3lib_extMgm::isLoaded(FH_LIBRARY_EXTkey)) {
		 		// FE BE library for flexform functions
			require_once(PATH_BE_fh_library.'lib/class.tx_fhlibrary_flexform.php');
				// check the flexform
			$this->pi_initPIflexForm();
			$config['code'] = tx_fhlibrary_flexform::getSetupOrFFvalue(
				$this, 
				$conf['code'], 
				$conf['code.'],
				$this->conf['defaultCode'], 
				$this->cObj->data['pi_flexform'], 
				'display_mode',
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['useFlexforms']
			);
		}else {
				// 'CODE' decides what is rendered:
			$config['code'] = $this->cObj->stdWrap($conf['code'],$conf['code.']);
		}

			// globally substituted markers, fonts and colors.
		$splitMark = md5(microtime());
		$globalMarkerArray=array();
		list($globalMarkerArray['###GW1B###'],$globalMarkerArray['###GW1E###']) = explode($splitMark,$this->cObj->stdWrap($splitMark,$conf['wrap1.']));
		list($globalMarkerArray['###GW2B###'],$globalMarkerArray['###GW2E###']) = explode($splitMark,$this->cObj->stdWrap($splitMark,$conf['wrap2.']));
		$globalMarkerArray['###GC1###'] = $this->cObj->stdWrap($conf['color1'],$conf['color1.']);
		$globalMarkerArray['###GC2###'] = $this->cObj->stdWrap($conf['color2'],$conf['color2.']);
		$globalMarkerArray['###GC3###'] = $this->cObj->stdWrap($conf['color3'],$conf['color3.']);

			// If the current record should be displayed.
		$config['displayCurrentRecord'] = $conf['displayCurrentRecord'];
		if ($config['displayCurrentRecord'])	{
			$config['code']='GUESTBOOK';
		}


		// *************************************
		// *** doing the things...:
		// *************************************
		$this->enableFields = $this->cObj->enableFields('tt_guest');
		$this->dontParseContent = $conf['dontParseContent'];
		$this->recordCount = $this->getRecordCount($this->pid_list);
		$globalMarkerArray['###PREVNEXT###'] = $this->getPrevNext();

			// Substitute Global Marker Array
		$this->orig_templateCode= $this->cObj->substituteMarkerArray($this->orig_templateCode, $globalMarkerArray);		
	}


	/**
	 * Main guestbook function.
	 */
	function getLayouts($templateCode,$alternativeLayouts,$marker)	{
		$out=array();
		for($a=0;$a<$alternativeLayouts;$a++)	{
			$m= '###'.$marker.($a?'_'.$a:'').'###';
			if(strstr($templateCode,$m))	{
				$out[]=$GLOBALS['TSFE']->cObj->getSubpart($templateCode, $m);
			} else {
				break;
			}
		}
		return $out;
	}

	/**
	 * Main guestbook function.
	 */
	function getItems($pid)	{
		global $TYPO3_DB;

		if(!isset($_REQUEST['offset']))
		{
			$offset = 0;
		}
		else
		{
			$offset = (int) $_REQUEST['offset'];
		}

		$res = $TYPO3_DB->exec_SELECTquery(
			'*',
			'tt_guest',
			'pid IN ('.$pid.')'.$this->enableFields,
			'',
			'crdate DESC',
			$offset.', '.$this->conf['limit']
		);

		$out = array();
		while($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			$out[] = $row;
		}
		return $out;
	}

	/**
	 * Main guestbook function.
	 */
	function formatStr($str)	{
		if (!$this->dontParseContent)	{
			return nl2br(htmlspecialchars($str));
		} else {
			return $str;
		}
	}

	function getRecordCount($pid)
	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) AS thecount',
			'tt_guest',
			'pid IN ('.$pid.')'.$this->enableFields,
			'',
			'',
			''
		);

		$row = mysql_fetch_row($res);
		list($thecount) = $row;

		return $thecount;
	}

	function getPrevNext()
	{
		$nav = new tx_ttguest_RecordNavigator(
			$this->recordCount, 
			$_REQUEST['offset'], 
			$this->conf['limit'], 
			'?pid='.$GLOBALS['TSFE']->id
		);
		$nav->createSequence();
		$nav->createPrevNext($this->conf['previousLabel'], $this->conf['nextLabel']);
		return $nav->getNavigator();
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_guest/pi/class.tx_ttguest.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tt_guest/pi/class.tx_ttguest.php']);
}

?>
