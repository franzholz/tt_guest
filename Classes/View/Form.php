<?php

namespace JambageCom\TtGuest\View;

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
 * Display of a post form
 *
 * @author  Kasper Skårhøj  <kasperYYYY@typo3.com>
 * @author  Franz Holzinger <franz@ttproducts.de>
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

use JambageCom\TtGuest\Constants\Field;


class Form implements \TYPO3\CMS\Core\SingletonInterface
{
    public function getPrivacyJavaScript ($checkId, $buttonId)
    {
        $result = '
function addListeners() {
    if(window.addEventListener) {
        document.getElementById("' . $checkId . '").addEventListener("click", enableSubmitButtonFunc,false);
    } else if (window.attachEvent) { // Added For Internet Explorer versions previous to IE9
        document.getElementById("' . $checkId . '").attachEvent("onclick", enableSubmitButtonFunc);
    }

    function enableSubmitButtonFunc() {
        document.getElementById("' . $buttonId . '").disabled = !this.checked;
    }
}
window.onload = addListeners; 
        ';

        return $result;
    }

    /**
    * Creates a post form for a forum
    */
    public function render (
        $languageObj,
        $theCode,
        $pid,
        $pagePrivacy,
        $captchaType,
        $lConf
    )
    {
        $xhtmlFix = \JambageCom\Div2007\Utility\HtmlUtility::determineXhtmlFix();
        $useXhtml = \JambageCom\Div2007\Utility\HtmlUtility::useXHTML();
        $idPrefix = 'mailform';
        $table = 'tt_guest';
        $local_cObj = \JambageCom\Div2007\Utility\FrontendUtility::getContentObjectRenderer();  // Initiate new cObj, because we're loading the data-array
        $originalData = array();
        
        if (
            isset($GLOBALS['TSFE']->applicationData) &&
            is_array($GLOBALS['TSFE']->applicationData) &&
            isset($GLOBALS['TSFE']->applicationData[TT_GUEST_EXT]) &&
            is_array($GLOBALS['TSFE']->applicationData[TT_GUEST_EXT])
        ) {
            $originalData = $GLOBALS['TSFE']->applicationData[TT_GUEST_EXT];
        }

        $spamWord = '';
        $LLkey = $languageObj->getLocalLangKey();
        $setupArray =
            array(
                '10' => 'title',
                '20' => 'note',
                '30' => 'cr_name',
                '40' => 'cr_email',
                '50' => 'www',
                '300' => 'post'
            );

        foreach ($setupArray as $k => $type) {
            if ($k == '300') {
                $field = 'value';
            } else {
                $field = 'label';
            }

            if (is_array($lConf['dataArray.'][$k . '.'])) {
                if (
                    (
                        !$LLkey ||
                        $LLkey == 'en'
                    ) &&
                    !$lConf['dataArray.'][$k . '.'][$field] ||
                    (
                        $LLkey != 'en' &&
                        !is_array($lConf['dataArray.'][$k . '.'][$field . '.']) ||
                        !is_array($lConf['dataArray.'][$k . '.'][$field . '.']['lang.']) ||
                        !is_array($lConf['dataArray.'][$k . '.'][$field . '.']['lang.'][$LLkey . '.'])
                    )
                ) {
                    $lConf['dataArray.'][$k . '.'][$field] = $languageObj->getLabel($type);
                }
            }
        }
        $wrongCaptcha = false;

        if (
            isset($originalData['error']) &&
            is_array($originalData['error'])
        ) {
            if ($originalData['error']['captcha'] == true) {
                $origRow = $originalData['row'];
                unset($origRow['doublePostCheck']);
                $wrongCaptcha = true;
                $word = $originalData['word'];
            }
            if ($originalData['error']['spam'] == true) {
                $spamWord = $originalData['word'];
                $origRow = $originalData['row'];
            }
        }

        if ($spamWord != '') {
            $out =
                sprintf(
                    $languageObj->getLabel(
                        'spam_detected'
                    ),
                    $spamWord
                );
            $lConf['dataArray.']['1.'] = array(
                'label' => 'ERROR !',
                'type' => 'label',
                'value' => $out,
            );
        }
        $lConf['dataArray.']['9995.'] = array(
            'type' => '*data[' . $table . '][NEW][prefixid]=hidden',
            'value' => $this->prefixId
        );

        if (
            is_object(
                $captcha = \JambageCom\Div2007\Captcha\CaptchaManager::getCaptcha(
                    TT_GUEST_EXT,
                    $captchaType
                )
            )
        ) {
            $captchaMarker = array();
            $textLabelWrap = '';
            $markerFilled = $captcha->addGlobalMarkers(
                $captchaMarker,
                true
            );
            $textLabel =
                $languageObj->getLabel(
                    'captcha'
                );

            if ($wrongCaptcha) {
                $textLabelWrap = '<strong>' .
                    sprintf(
                        $languageObj->getLabel(
                            'wrong_captcha'
                        ),
                        $word
                    ) .
                    '</strong><br' . $xhtmlFix . '>';
            }

            if (
                $markerFilled
            ) {
                $additionalText = '';
                if ($captchaType == 'freecap') {
                    $additionalText =
                        $captchaMarker['###CAPTCHA_CANT_READ###'] . '<br' . $xhtmlFix . '>' .
                        $captchaMarker['###CAPTCHA_ACCESSIBLE###'];
                }

                $lConf['dataArray.']['55.'] = array(
                    'label' => $textLabel,
                    'label.' =>
                        array(
                            'wrap' =>
                            '<span class="'. TT_GUEST_CSS_PREFIX . 'captcha">|' . 
                            $textLabelWrap .
                            $captchaMarker['###CAPTCHA_IMAGE###'] . '<br' . $xhtmlFix . '>' .
                            $captchaMarker['###CAPTCHA_NOTICE###'] . '<br' . $xhtmlFix . '>' .
                            $additionalText . '</span>'
                        ),
                    'type' => '*data[' . $table . '][NEW][' . Field::CAPTCHA . ']=input,20'
                );
            }
        } else if (
            isset($lConf['dataArray.']['55.']) &&
            $lConf['dataArray.']['55.']['label'] == ''
        ) {
            unset($lConf['dataArray.']['55.']);
        }

        if (
            $pagePrivacy
        ) {
            $labelMap = array(
                'title' => 'privacy_policy.title',
                'acknowledgement' => 'privacy_policy.acknowledgement',
                'approval_required' => 'privacy_policy.approval_required',
                'acknowledged' => 'privacy_policy.acknowledged',
                'acknowledged_2' => 'privacy_policy.acknowledged_2',
                'hint' => 'privacy_policy.hint',
                'hint_1' => 'privacy_policy.hint_1'
            );

            foreach ($labelMap as $key => $languageKey) {
                $labels[$key] = $languageObj->getLabel($languageKey);
            }
            $piVars = array();

            $privacyUrl = $local_cObj->getTypoLink_URL($pagePrivacy, $piVars);
            $privacyUrl = str_replace(array('[', ']'), array('%5B', '%5D'), $privacyUrl);

            $textLabelWrap = '<a href="' . htmlspecialchars($privacyUrl) . '">' . $labels['title'] . '</a><br' . $xhtmlFix . '>'. chr(13);

            $lConf['dataArray.']['60.'] = array(
                'label' => $labels['title'] . ':',
                'label.' =>
                    array(
                        'wrap' =>
                        '<div class="'. TT_GUEST_CSS_PREFIX . 'privacy_policy"><strong>|</strong><br' . $xhtmlFix .'>' . 
                        $textLabelWrap .
                        $labels['acknowledged_2'] . '<br' . $xhtmlFix .'>' .
                        '<strong>' . $labels['hint'] . '</strong><br' . $xhtmlFix . '>' .
                        $labels['hint_1'] . '</div>'
                    ),
                'type' => 'label',
                'value' =>  $labels['approval_required'],
            );
            if (!$_REQUEST['privacy_policy']) {
                $lConf['params.']['submit'] .=
                    ($useXhtml ? ' disabled="disabled" ' : ' disabled ');
            }

            $lConf['dataArray.']['61.']['label'] = $labels['acknowledgement'];
            $lConf['dataArray.']['61.']['label.'] =
                array(
                    'wrap' => 
                        '<span class="'. TT_GUEST_CSS_PREFIX . 'privacy_policy_checkbox">' . 
                        $labels['acknowledged'] .
                        '</span>'
                    );
            $privacyJavaScript =
                $this->getPrivacyJavaScript(
                    $idPrefix . 'privacypolicy',
                    'mailformformtypedb'
                );

            $GLOBALS['TSFE']->setJS(
                TT_GUEST_EXT . '-privacy_policy',
                $privacyJavaScript
            );
        } else {
            if (
                isset($lConf['dataArray.']['60.']) &&
                $lConf['dataArray.']['60.']['label'] == ''
            ) {
                unset($lConf['dataArray.']['60.']);
            }
            if (
                isset($lConf['dataArray.']['61.']) &&
                $lConf['dataArray.']['61.']['label'] == ''
            ) {
                unset($lConf['dataArray.']['61.']);
            }
        }

        foreach ($setupArray as $k => $theField) {
            if ($k == '300') {
                $type = 'value';
            } else {
                $type = 'label';
            }

            if (is_array($lConf['dataArray.'][$k . '.'])) {
                if (
                    (
                        !$languageObj->getLocalLangKey() ||
                        $languageObj->getLocalLangKey() == 'en'
                    ) &&
                    !$lConf['dataArray.'][$k . '.'][$type] ||

                    (
                        $languageObj->getLocalLangKey() != 'en' &&
                        (
                            !is_array($lConf['dataArray.'][$k . '.'][$type . '.']) ||
                            !is_array($lConf['dataArray.'][$k . '.'][$type . '.']['lang.']) ||
                            !is_array($lConf['dataArray.'][$k . '.'][$type . '.']['lang.'][$languageObj->getLocalLangKey() . '.'])
                        )
                    )
                ) {
                    $lConf['dataArray.'][$k . '.'][$type] =
                        $languageObj->getLabel(
                            $theField
                        );

                    if (
                        ($type == 'label') &&
                        isset($origRow[$theField])
                    ) {
                        $lConf['dataArray.'][$k . '.']['value'] = $origRow[$theField];
                    }
                }
            }
        }
        ksort($lConf['dataArray.']);

        $result = $local_cObj->FORM($lConf);
        return $result;
    }
}

