<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Helper;

use Osf\Pdf\Haru\HaruDoc;

use Osf\Pdf\Haru\Page;

/**
 * Génère un pied de page
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2 nov. 2013
 * @package osf
 * @subpackage pdf
 */
class Footer extends BaseHelper
{
    protected $content;
    protected $warning;
    protected $madeby;
    
    protected $marginLeft;
    protected $footerWidth;
    protected $y;
      
    public function getContent()
    {
        return $this->content;
    }

    public function getMadeby()
    {
        return $this->madeby;
    }

    /**
     * @param string $madeby
     * @return \Osf\Pdf\Document\Helper\Footer
     */
    public function setMadeby($madeby)
    {
        $this->madeby = $madeby;
        return $this;
    }

    public function getWarning()
    {
        return $this->warning;
    }

    /**
     * @param string $content
     * @return \Osf\Pdf\Document\Helper\Footer
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $warning
     * @return \Osf\Pdf\Document\Helper\Footer
     */
    public function setWarning($warning)
    {
        $this->warning = $warning;
        return $this;
    }
    
    public function getY()
    {
        return $this->y;
    }
    
    /**
     * @param string $text
     * @param int $font
     * @param float $fontSize
     */
    protected function displayText(Page $page, $text, $fontType, $fontSize)
    {
        $doc = $this->document;
        $font = $doc->getFontType($fontType);
        $lines = explode("\n", trim($text));
        $lines = array_reverse($lines);
        $page->setFontAndSize($font, $fontSize);
        $leading = $fontSize + ($fontSize / 3) + 1;
        foreach ($lines as $line) {
            $page->textRect($this->marginLeft, $this->y + $leading, $this->marginLeft + $this->footerWidth, $this->y, trim($line), \HaruPage::TALIGN_CENTER);
            $this->y += $leading;
        }
    }

    /**
     * @see \Osf\Pdf\Document\Helper\BaseHelper::render()
     * @return \Osf\Pdf\Document\Helper\Footer
     */
    public function render(Page $page = null)
    {
        $doc = $this->document;
        $page = $page === null ? $doc->getCurrentPage() : $page;
        $docConfig = $doc->getBean()->getConfig();
        $this->y = $docConfig->getMarginBottom();
        $this->marginLeft = $docConfig->getMarginLeft();
        $this->footerWidth = $page->getWidth() - $docConfig->getMarginLeft() - $docConfig->getMarginRight();
        if ($this->madeby) {
            $this->displayText($page, $this->madeby, HaruDoc::FONT_LIGHT, 6.5);
        }
        if ($this->warning) {
            $this->displayText($page, $this->warning, HaruDoc::FONT_REGULAR, 8.5);
        }
        if ($this->content) {
            $this->displayText($page, $this->content, HaruDoc::FONT_LIGHT, 8.5);
        }
        $this->y += 3;
        $page->endText();
        $page->setGrayStroke(0);
        $page->setLineWidth(0);
        $page->moveTo($this->marginLeft, $this->y);
         $page->lineTo($this->marginLeft + $this->footerWidth, $this->y);
        $page->stroke(false);
        $page->beginText();
        return $this;
    }
}
