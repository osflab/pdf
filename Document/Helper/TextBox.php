<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Helper;

use Osf\Exception\ArchException;

use Osf\Pdf\Haru\Page;

/**
 * Advanced text box
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2 nov. 2013
 * @package osf
 * @subpackage pdf
 */
class TextBox extends BaseHelper
{
    const VALIGN_TOP          = 0;
    const VALIGN_CENTER       = 1;
    const VALIGN_BOTTOM       = 2;
    const VALIGN_CENTERTOP    = 3;
    const VALIGN_CENTERBOTTOM = 4;
    
    const TALIGN_LEFT    = \HaruPage::TALIGN_LEFT;
    const TALIGN_CENTER  = \HaruPage::TALIGN_CENTER;
    const TALIGN_RIGHT   = \HaruPage::TALIGN_RIGHT;
    const TALIGN_JUSTIFY = \HaruPage::TALIGN_JUSTIFY;
    
    protected $x1;
    protected $y1;
    protected $x2;
    protected $y2; 
    protected $content;
    protected $verticalAlign = self::VALIGN_TOP;
    protected $textAlign = self::TALIGN_LEFT;
    protected $maxFontSize = 14;
    protected $minFontSize = 6;
    protected $size = null;
    
    /**
     * @param integer $x1
     * @param integer $y1
     * @param integer $x2
     * @param integer $y2
     * @return \Osf\Pdf\Document\Helper\TextBox
     */
    public function setCoordinates($x1, $y1, $x2, $y2)
    {
        $this->x1 = min($x1, $x2);
        $this->y1 = min($y1, $y2);
        $this->x2 = max($x1, $x2);
        $this->y2 = max($y1, $y2);
        return $this;
    }
    
    /**
     * @param string $content
     * @return \Osf\Pdf\Document\Helper\TextBox
     */
    public function setContent($content)
    {
        $this->content = (string) $content;
        return $this;
    }
    
    public function getContent()
    {
        return $this->content;
    }

    public function getVerticalAlign()
    {
        return $this->verticalAlign;
    }

    public function getTextAlign()
    {
        return $this->textAlign;
    }

    public function getMaxFontSize()
    {
        return $this->maxFontSize;
    }

    public function getMinFontSize()
    {
        return $this->minFontSize;
    }

    /**
     * @param integer $verticalAlign
     * @return \Osf\Pdf\Document\Helper\TextBox
     */
    public function setVerticalAlign($verticalAlign)
    {
        $this->verticalAlign = $verticalAlign;
        return $this;
    }

    /**
     * @param integer $textAlign
     * @return \Osf\Pdf\Document\Helper\TextBox
     */
    public function setTextAlign($textAlign)
    {
        $this->textAlign = $textAlign;
        return $this;
    }

    /**
     * @param float $maxFontSize
     * @return \Osf\Pdf\Document\Helper\TextBox
     */
    public function setMaxFontSize($maxFontSize)
    {
        $this->maxFontSize = $maxFontSize;
        return $this;
    }

    /**
     * @param float $minFontSize
     * @return \Osf\Pdf\Document\Helper\TextBox
     */
    public function setMinFontSize($minFontSize)
    {
        $this->minFontSize = $minFontSize;
        return $this;
    }
    
    /**
     * Y Top after processing
     */
    public function getTop()
    {
        return $this->y2;
    }
    
    /**
     * Y Bottom after processing
     */
    public function getBottom()
    {
        return $this->y1;
    }
    
    /**
     * Font size after processing
     */
    public function getFontSize()
    {
        return $this->size;
    }

    /**
     * @see \Osf\Pdf\Document\Helper\BaseHelper::render()
     * @todo [PDF] recherche de taille de police dicotomique
     * @return \Osf\Pdf\Document\Helper\TextBox
     */
    public function render(Page $page = null)
    {
        $doc = $this->document;
        $page = $page === null ? $doc->getCurrentPage() : $page;
        $mockDoc = $doc->getMock();
        $cpage = $mockDoc->getCurrentPage();
        $cpage->beginText();
        $cpage->setFontAndSize($mockDoc->getFont($page->getCurrentFont()->getFontName()), $page->getCurrentFontSize());
        $size = $this->getMaxFontSize();
        $resized = false;
        while (true) {
            try {
                $cpage->setFontAndSize($cpage->getCurrentFont(), $size);
                $cpage->setTextLeading($cpage->getCurrentFontSize() + ($cpage->getCurrentFontSize() / 3));
                $cpage->textRect($this->x1, $this->y2, $this->x2, $this->y1, $this->getContent(), $this->getTextAlign());
                break;
            } catch (\HaruException $e) {
                if ($e->getMessage() != 'Insufficient space for text') {
                    throw $e;
                }
                $resized = true;
                $size -= 0.5;
            }
        }
        $page->setFontAndSize($page->getCurrentFont(), $size);
        $page->setTextLeading($page->getCurrentFontSize() + ($page->getCurrentFontSize() / 3));
        if ($this->getVerticalAlign() != self::VALIGN_TOP) {
            
            $cpage->textRect($this->x1, $this->y2, $this->x2, $this->y1, $this->getContent(), $this->getTextAlign());
            $deplacement = ceil($cpage->getTextY() - $this->y1);
            if ($deplacement) {
                $y1 = $this->y1;
                $y2 = $this->y2;
                while (1) {
                    try {
                        switch ($this->getVerticalAlign()) {
                            case self::VALIGN_CENTER :
                                $halfDep = floor($deplacement / 2);
                                $y2 = $this->y2 - $halfDep;
                                $y1 = $this->y1 + $halfDep;
                                break;
                            case self::VALIGN_CENTERTOP :
                                $unitDep = $deplacement / 4;
                                $y2 = $this->y2 - floor($unitDep);
                                $y1 = $this->y1 + floor($unitDep * 3);
                                break;
                            case self::VALIGN_CENTERBOTTOM :
                                $unitDep = $deplacement / 4;
                                $y2 = $this->y2 - floor($unitDep * 3);
                                $y1 = $this->y1 + floor($unitDep);
                                break;
                            case self::VALIGN_BOTTOM :
                                $y2 = $this->y2 - $deplacement;
                                break;
                            default : 
                                throw new ArchException('Bad valign value');
                        }
                        $cpage->textRect($this->x1, $y2, $this->x2, $y1, $this->getContent(), $this->getTextAlign());
                        break;
                    } catch (\HaruException $e) {
                        if ($e->getMessage() != 'Insufficient space for text') {
                            throw $e;
                        }
                        $deplacement -= 1;
                    }
                }
                $this->y1 = $y1;
                $this->y2 = $y2;
            }
        }
        $cpage->endText();
        
        $page->debugDisplayZone($this->x1, $this->y1, $this->x2 - $this->x1, $this->y2 - $this->y1);
        $page->textRect($this->x1, $this->y2, $this->x2, $this->y1, $this->getContent(), $this->getTextAlign());
        $this->size = $size;
        
        return $this;
    }
}
