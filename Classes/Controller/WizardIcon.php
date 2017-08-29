<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
* Class, containing function for adding an element to the content element wizard.
*
* @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
*/


/**
* Class, containing function for adding an element to the content element wizard.
*
* @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
* @package TYPO3
* @subpackage tt_guest
*/
class tx_ttguest_wizicon {

    /**
    * Processing the wizard-item array from sysext/cms/layout/db_new_content_el.php
    *
    * @param	array		Wizard item array
    * @return	array		Wizard item array, processed (adding a plugin for tt_guest extension)
    * @see SC_db_new_content_el::wizardArray()
    */
    public function proc($wizardItems) {
            // Adding the item:
        $wizardItems['plugins_ttguest'] = array(
            'icon' => PATH_BE_TTGUEST_REL . 'guestbook.gif',
            'title' => $GLOBALS['LANG']->sL('LLL:EXT:' . TT_GUEST_EXT . '/locallang.php:plugins_title'),
            'description' => $GLOBALS['LANG']->sL('LLL:EXT:' . TT_GUEST_EXT . '/locallang.php:plugins_description'),
            'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=3&defVals[tt_content][select_key]=' . rawurlencode('GUESTBOOK, POSTFORM')
        );
        return $wizardItems;
    }
}
