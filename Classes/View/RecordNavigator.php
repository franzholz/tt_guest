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
 * RecordNavigator.php
 *
 * Name: Class.RecordNavigator.php
 * Type: Class
 * Purpose: Provide interface for creating next/previous and page # links
 * Usage:
 *
 * $RN = new RecordNavigator(
 * 	"SELECT COUNT(*) FROM yourtable",
 * 	$passedOffset,
 * 	20,
 * 	"yourscript.php?catid=1"
 * );
 * $RN->createSequence();
 * $RN->createPrevNext('previous', 'next');
 * echo($RN->getNavigator());
 *
 * @author	Kasper Skårhøj <kasperYYYY@typo3.com>
 * @author	Franz Holzinger <franz@ttproducts.de>
 */

use \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;


class RecordNavigator {
    protected $queryCount;
    protected $offset;
    protected $limiter;
    protected $seqStr;

    protected $cObj = null; // for making typo3 links

    /* constructor */
    public function __construct ($queryCount, $offset, $limiter)
    {
        $this->queryCount 	= $queryCount;
        $this->offset 		= $offset;
        $this->limiter		= $limiter;

        $this->cObj = new ContentObjectRenderer();
    }

    /* create page # sequence */
    public function createSequence ()
    {
        $numPages = ceil($this->queryCount / $this->limiter);
        $nextOffset = 0;

        /* if there are more records than currently counted, generate sequence */
        if($this->queryCount > $this->limiter) {
            for($i = 1; $i <= $numPages; $i++) {
                if($this->offset != $nextOffset) {
                    $this->seqStr .= $this->createOffsetLink($nextOffset, $i, '');
                }
                else {
                    $this->seqStr .= '<li class="current">' . $i . '</li>';
                }
                $nextOffset += $this->limiter;
            }
        }
    }

    /* create offset link */
    public function createOffsetLink ($newOffset, $label, $class)
    {
        $pA = array();
        $useCache = true;
        $target = '';
        $addQueryParams = '&offset=' . $newOffset . $GLOBALS['TSFE']->linkVars;
        $linkConf = array();
        $linkConf = array_merge( array('useCacheHash' => $useCache), $linkConf);

        $pageLink = tx_div2007_alpha5::getTypoLink_URL_fh003(
            $this->cObj,
            $GLOBALS['TSFE']->id,
            $addQueryParams,
            $target,
            $linkConf
        );

        $result = '<li' . ($class ? ' class="' . $class . '"': '') . '>' .
            '<a href="' . htmlspecialchars($pageLink) . '"' . '>' .
            htmlspecialchars($label) . '</a>' .
            '</li>';

        return $result;
    }

    /* create previous/next links */
    public function createPrevNext ($prevLabel, $nextLabel)
    {
        if ((int) $this->offset != 0) {
            $this->seqStr =
                $this->createOffsetLink(
                    $this->offset - $this->limiter,
                    $prevLabel,
                    'prev'
                ) .
                $this->seqStr;
        }

        if ($this->queryCount > ($this->offset + $this->limiter)) {
            $this->seqStr =
                $this->seqStr .
                $this->createOffsetLink(
                    $this->offset + $this->limiter,
                    $nextLabel,
                    'next'
                );
        }
    }

    /* return full navigation string */
    public function getNavigator ()
    {
        return '<ul class="prevnext">' . $this->seqStr . '</ul>';
    }
}


