<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with TYPO3 source code.
 *
 * The TYPO3 project - inspiring people to share!
 */


namespace JambageCom\TtGuest\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class that adds the wizard icon.
 *
 * @category    Plugin
 * @package     TYPO3
 * @subpackage  tt_guest
 * @author      Franz Holzinger <franz@ttproducts.de>
 * @copyright   Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class WizardIcon
{

    /**
     * Processes the wizard items array.
     *
     * @param array $wizardItems The wizard items
     * @return array Modified array with wizard items
     */
    public function proc (array $wizardItems)
    {
        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
        $iconRegistry = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\IconRegistry');
        $iconPath = 'Resources/Public/Icons/';

        $params = '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=3&defVals[tt_content][select_key]=' . rawurlencode('GUESTBOOK, POSTFORM');
        $wizardItem = array(
            'title' => $GLOBALS['LANG']->sL('LLL:EXT:' . TT_GUEST_EXT . '/locallang.xlf:plugins_title'),
            'description' => $GLOBALS['LANG']->sL('LLL:EXT:' . TT_GUEST_EXT . '/locallang.xlf:plugins_description'),
            'params' => $params
        );

        $iconIdentifier = 'extensions-tt_guest-wizard';
        $iconRegistry->registerIcon(
            $iconIdentifier,
            'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider',
            array(
                'source' => 'EXT:' . TT_GUEST_EXT . '/' . $iconPath . 'guestbook.gif',
            )
        );
        $wizardItem['iconIdentifier'] = $iconIdentifier;

        $wizardItems['plugins_ttguest'] = $wizardItem;

        return $wizardItems;
    }
}

