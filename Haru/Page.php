<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Haru;

use Osf\Exception\ArchException;

/**
 * Aggregate HaruPage
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 30 sept. 2013
 * @package osf
 * @subpackage pdf
 * @deprecated since version 0.1
 */
class Page extends PageProxy
{
    protected $debug = false;
    
    public function displayGrid()
    {
        $width = $this->page->getWidth();
        $height = $this->page->getHeight();
        $courier = $this->doc->getFont('Courier');
        $this->page->setFontAndSize($courier, 6);
        $strokeFirst = 0.8;
        $strokeSecond = 0.9;
        
        $this->page->setLineWidth(0);
        $this->page->setGrayStroke($strokeSecond);
        for ($x = 5; $x < $width; $x += 10) {
            $this->page->moveTo($x, 0);
            $this->page->lineTo($x, $height);
        }
        $this->page->stroke();
        $this->page->setGrayStroke($strokeSecond);
        for ($y = 5; $y < $height; $y += 10) {
            $this->page->moveTo(0, $y);
            $this->page->lineTo($width, $y);
        }
        $this->page->stroke();
        $this->page->setGrayStroke($strokeFirst);
        for ($x = 0; $x < $width; $x += 10) {
            $this->page->moveTo($x, 0);
            $this->page->lineTo($x, $height);
        }
        $this->page->stroke();
        $this->page->setGrayStroke($strokeFirst);
        for ($y = 0; $y < $height; $y += 10) {
            $this->page->moveTo(0, $y);
            $this->page->lineTo($width, $y);
        }
        $this->page->fillStroke();
        
        $this->page->beginText();
        for ($y = 20; $y < $height - 10; $y += 20) {
            $this->page->textOut(2, $y - 2, $y);
        }
        $this->page->endText();
        
        $this->page->beginText();
        $angle = 90 / 180 * pi();
        $a = cos($angle);
        $b = sin($angle);
        $c = -sin($angle);
        $d = $a;
        for ($x = 20; $x < $width - 10; $x += 20) {
            $this->page->setTextMatrix($a, $b, $c, $d, $x + 2, 2);
            $this->page->showText($x);
            $this->page->setTextMatrix($a, $b, $c, $d, $x + 2, $height - 15);
            $this->page->showText($x);
        }
        $this->page->endText();
    }

    /**
     * Affiche une image, maintient les proportions de l'image
     * @param \HaruImage $image
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     */
    public function drawImage(\HaruImage $image, $x = null, $y = null,
            $width = null, $height = null,
            $horizontalAlign = self::IMG_HALIGN_LEFT, $verticalAlign = self::IMG_VALIGN_TOP)
    {
        $x = $x === null ? $this->getCurrentPos()['x'] : $x;
        $y = $y === null ? $this->getCurrentPos()['y'] : $y;
        $oh = (float) $height;
        $ow = (float) $width;
        $this->debugDisplayZone($x, $y, $ow, $oh);
        if (!$width && !$height) {
            $width = $image->getWidth();
            $height = $image->getHeight();
        } else {
            $iWidth = $image->getWidth();
            $iHeight = $image->getHeight();
            if ($height == null || ($iWidth / $iHeight > $width / $height)) {
                $height = $iHeight * ($width / $iWidth);
            } else {
                $width = $iWidth * ($height / $iHeight);
            }
        }
        if ($oh > $height && $verticalAlign != self::IMG_VALIGN_BOTTOM) {
            switch ($verticalAlign) {
                case self::IMG_VALIGN_TOP :
                    $y = ($oh - $height) + $y;
                    break;
                case self::IMG_VALIGN_CENTER :
                    $y = (($oh - $height) / 2) + $y;
                    break;
                default :
                    throw new ArchException('Bad image valign value');
            }
        }
        if ($ow > $width && $horizontalAlign != self::IMG_HALIGN_LEFT) {
            switch ($horizontalAlign) {
                case self::IMG_HALIGN_CENTER :
                    $x = (($ow - $width) / 2) + $x;
                    break;
                case self::IMG_HALIGN_RIGHT :
                    $x = ($ow - $width) + $x;
                    break;
                default :
                    throw new ArchException('Bad image halign value');
            }
        }
        return $this->page->drawImage($image, $x, $y, $width, $height);
    }
    
    public function getTextX()
    {
        return $this->page->getCurrentTextPos()['x'];
    }
    
    public function getTextY()
    {
        return $this->page->getCurrentTextPos()['y'];
    }
    
    public function setDebug($debug = true)
    {
        $this->debug = (bool) $debug;
    }
    
    /**
     * Display a zone (rectangle) for debugging 
     * @param float $left
     * @param float $bottom
     * @param float $width
     * @param float $height
     * @return \Osf\Pdf\Haru\Page
     */
    public function debugDisplayZone($left, $bottom, $width, $height)
    {
        if ($this->debug) {
            $textMode = $this->getGMode() & \HaruPage::GMODE_TEXT_OBJECT;
            if ($textMode) {
                $this->endText();
            }
            $initialStroke = $this->getRGBStroke();
            $this->setRGBStroke(1, 0, 0);
            $this->rectangle($left, $bottom, $width, $height);
            $this->stroke();
            $this->setRGBStroke($initialStroke['r'], $initialStroke['g'], $initialStroke['b']);
            if ($textMode) {
                $this->beginText();
            }
        }
        return $this;
    }
}
