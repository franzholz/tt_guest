<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2007 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * guest_submit.inc
 *
 * .notifyEmail =	email address that should be notified of submissions.
 * See TSref document / FEDATA section for details on how to use this script.
 * The static template 'plugin.tt_guest' provides a working example of configuration.
 * $Id$*
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Nicolas Liaudat <mailing (at) pompiers-chatel.ch>
 */

if (is_object($this))	{
	$conf = $this->getConf('tt_guest');

	$email = $this->newData['tt_guest']['NEW']['cr_email'];
	if (!$conf['emailCheck'] || checkEmail($email)) { // Added 02.06.2006 Nicolas Liaudat [mailing (at) pompiers-chatel.ch]
		if (is_array($this->newData['tt_guest']['NEW']))	{

			$spamArray = t3lib_div::trimExplode(',',$conf['spamWords']);
			$bSpamFound = false;
			$internalFieldArray = array('hidden','pid','doublePostCheck');

			foreach ($this->newData['tt_guest']['NEW'] as $field => $value)	{
				if (!in_array($field, $internalFieldArray))	{
					if (version_compare(phpversion(), '5.0.0', '>='))	{
						foreach ($spamArray as $k => $word)	{
							if ($word && stripos($value, $word) !== false)	{
								$bSpamFound = true;
								break;
							}
						}
					} else {
						foreach ($spamArray as $k => $word)	{
							$lWord = strtolower($word);
							$lValue = strtolower($value);
							if ($lWord && strpos($lValue, $lWord) !== false)	{
								$bSpamFound = true;
								break;
							}							
						}
					}
				}
				if ($bSpamFound)	{
					break;
				}
			}
			if ($bSpamFound)	{
		  		$content = 'The spam word "'.$word.'" has been detected.';
		  		$GLOBALS['TSFE']->printError($content);				
			} else {
				$this->newData['tt_guest']['NEW']['cr_ip'] = t3lib_div::getIndpEnv('REMOTE_ADDR');
				$this->execNEWinsert('tt_guest', $this->newData['tt_guest']['NEW']);
				$this->clear_cacheCmd(intval($this->newData['tt_guest']['NEW']['pid']));
		
				if ($conf['notifyEmail'])	{
					$name = $this->newData['tt_guest']['NEW']['cr_name'];
					$email = $this->newData['tt_guest']['NEW']['cr_email'];
		
					mail ($conf['notifyEmail'], 'tt_guest item submitted at '.t3lib_div::getIndpEnv('HTTP_HOST'), '
		Page-id, tt_guest: '.$this->newData['tt_guest']['NEW'][pid].'
		Current page uid/title: '.$GLOBALS['TSFE']->page[title].'/'.$GLOBALS['TSFE']->page[uid].'
		Name: '.$name.'
		Email: '.$email.'
		IP Address: '.t3lib_div::getIndpEnv('REMOTE_ADDR').'
		Message: '.$this->newData['tt_guest']['NEW']['title'].'
		'.$this->newData['tt_guest']['NEW']['note'].'
		
					','From: '.$name.' <'.$email.'>');
				}
			}
		}
	} else {
  		$content = $email . ' is not a valid email address.';
  		$GLOBALS['TSFE']->printError($content);
	}
}


// Added from Nicolas Liaudat
function checkEmail($email)	{

	if (!ereg('^[^@]{1,64}@[^@]{1,255}$', $email)) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}
	
	// gets domain name
	list($username,$domain)=split('@',$email);
	// checks for if MX records in the DNS
	$mxhosts = array();
	if(!getmxrr($domain, $mxhosts))	{
		// no mx records, ok to check domain
		if (@fsockopen($domain,25,$errno,$errstr,30))	{
			return true;
		} else {
			return false;
		}
	} else {
		// mx records found
		foreach ($mxhosts as $host)	{
			if (@fsockopen($host,25,$errno,$errstr,30))	{
				return true;
			}
		}
		return false;
	}
}

?>