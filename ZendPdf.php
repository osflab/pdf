<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf;

use ZendPdf\Color\GrayScale;
use ZendPdf\Color\Rgb;
use ZendPdf\PdfDocument;

/**
 * ZendPdf simple access library
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 26 sept. 2013
 * @package osf
 * @subpackage pdf
 * @deprecated since version 3.0.0 - Zend PDF is now deprecated...
 */
class ZendPdf extends PdfDocument
{
    /**
     * @param float $r
     * @param float $g
     * @param float $b
     * @return \ZendPdf\Color\Rgb
     */
    public function getColorRgb($r, $g, $b)
    {
        static $colors = array();
        
        if (!isset($colors[$r][$g][$b])) {
            $colors[$r][$g][$b] = new Rgb($r, $g, $b);
        }
        return $colors[$r][$g][$b];
    }

    /**
     * @param float $r
     * @param float $g
     * @param float $b
     * @return \ZendPdf\Color\GrayScale
     */
    public function getColorGrayscale($grayLevel)
    {
        static $colors = array();
    
        if (!isset($colors[$grayLevel])) {
            $colors[$grayLevel] = new GrayScale($grayLevel);
        }
        return $colors[$grayLevel];
    }
}
