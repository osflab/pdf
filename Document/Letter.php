<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document;

use Osf\Pdf\Document\Helper\TextBox;
use Osf\Pdf\Haru\Page;
use Osf\Pdf\Document\Bean\LetterBeanInterface;

/**
 * Paper letter
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 31 oct. 2013
 * @package osf
 * @subpackage pdf
 * @deprecated because Haru PDF lib is no longer maintained
 */
class Letter extends BaseDocument
{
    protected $yTop;
    protected $yBottom;
    
    /**
     * @var \Osf\Pdf\Document\Bean\LetterBeanInterface
     */
    protected $bean;
    
    public function __construct(LetterBeanInterface $bean)
    {
        //$this->setPageMode(HaruDoc::PAGE_MODE_USE_NONE);
        parent::__construct($bean);
    }
    
    /**
     * Letter rendering
     * @return $this
     */
    public function render()
    {
        $this->setHeaders();
        $page = $this->newPage();
        //$page->setDebug();
        //$page->displayGrid();
        
        $page->beginText();
        //$page->setRGBFill($colors[0]['r'] / 255, $colors[0]['g'] / 255, $colors[0]['b'] / 255);
        $this->maxFontSize = $page->getDocument()->getFontSize();
        $this->renderHeader($page);
        $this->renderFooter($page);
        $this->renderLetter($page);
        $page->endText();
        $this->output();
        return $this;
    }
    
    /**
     * Header until wordings (object, etc.)
     * @param Page $page
     * @return $this
     */
    protected function renderHeader(Page $page)
    {
        $config   = $this->bean->getConfig();
        $provider = $this->bean->getProvider();
        $receiver = $this->bean->getReceiver();
        
        $x = $config->getMarginLeft();
        $x = $config->getMarginLeft();
        $y = $page->getHeight() - $config->getMarginTop();
        $doc = $page->getDocument();
        $address = $this->address()
            ->setAddress($provider->getAddress())
            ->setTelFaxMail($provider->getTel(), 
                            $provider->getFax(), 
                            $provider->getEmail())
            ->setTitleSize(11)
            ->setBodySize(9)
            ->setPosition($x, $y, $page->getWidth() - $config->getMarginRight() - 300, 150);
        if ($provider->getCompanyLogo()) {
            $address->setLogo($provider->getCompanyLogo());
        }
        $address->render(null, false);
        $dateFormatter = \IntlDateFormatter::create('fr-FR', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, 'Europe/Paris', \IntlDateFormatter::GREGORIAN);
        $page->setFontTypeLight($doc->getFontSize());
        $page->textRect($x, $y, $page->getWidth() - $config->getMarginRight(), $y - 20, ($provider->getAddress()->getCity(true) ? $provider->getAddress()->getCity(true) . ', ' : '') . 'le ' . $dateFormatter->format($this->bean->getDate()), \HaruPage::TALIGN_RIGHT);
        $this->address()
            ->setAddress($receiver->getAddress())
            ->setPosition(300, 710, null, 70)
            ->setTitleSize(11)
            ->setBodySize(11)
            ->render();
        if (count($this->bean->getLibs())) {
            $page->moveTextPos(0, -40);
            $page->setFontAndSize(null, $doc->getFontSize());
            $currentFont = $page->getCurrentFont();
            $leading = ceil($page->getCurrentFontSize() * 1.6);
            $margin = 20;
            foreach ($this->bean->getLibs() as $libelle => $line) {
                $page->setFontTypeRegular();
                $page->textOut($x + 20, $page->getTextY() - $leading, trim($libelle) . ' ');
                $page->setFontTypeLight();
                $page->textOut($page->getTextX(), $page->getTextY(), trim($line));
            }
            $page->setFontAndSize($currentFont);
        }
        $this->yTop = $page->getTextY();
        return $this;
    }
    protected function renderFooter(Page $page)
    {
        $provider = $this->bean->getProvider();
        $content  = $provider->getAddress()->getTitle();
        $content .= trim($provider->getAddress()->getAddress()) ? ' - ' . str_replace("\n", " - ", $provider->getAddress()->getAddress()) . ', ' : '';
        $city = trim($provider->getAddress()->getPostalCode() . ' ' . $provider->getAddress()->getCity());
        $content .= $city ?: ' - ' . $city;
        $content .= "\ntél. " . $provider->getTel() . " - fax. " . $provider->getFax();
        //$content .= "\nmél. " . $this->fromContact->getEmail();
        $footer = $this->footer()
            ->setContent($content)
            ->setWarning("document confidentiel")
            //->setMadeby("www.simplemanager.fr")
            ->render();
        $this->yBottom = $footer->getY();
    }
    
    protected function renderLetter(Page $page)
    {
        $config = $this->bean->getConfig();
        
        $marginTopBottom = 30;
        $dearSignMargin = ($config->getMaxFontSize() * 2.5) + $marginTopBottom;
        $left = $config->getMarginLeft();
        $bottom = $this->yBottom + ($this->bean->getSignature() ? $dearSignMargin : $marginTopBottom) + 30;
        $width = $page->getWidth() - $config->getMarginLeft() - $config->getMarginRight();
        $height = $this->yTop - $bottom - ($this->bean->getDear() ? $dearSignMargin : $marginTopBottom);

        $page->debugDisplayZone($left, $bottom, $width, $height);
        $regFrom = array("/\n- /");
        $regTo = array("\n     - ");
        $content = rtrim(preg_replace($regFrom, $regTo, htmlspecialchars($this->bean->getBody())));
        $textBox = $this->textBox()
            ->setContent($content)
            ->setCoordinates($left, $bottom, $left + $width, $bottom + $height)
            ->setMaxFontSize($config->getMaxFontSize())
            ->setMinFontSize($config->getMinFontSize())
            ->setTextAlign(TextBox::TALIGN_JUSTIFY)
            ->setVerticalAlign(TextBox::VALIGN_CENTERTOP)
            ->render();
        if ($this->bean->getDear()) {
            $page->setFontAndSize(null, $textBox->getFontSize());
            $page->textRect(
                    $config->getMarginLeft() + 100, 
                    $textBox->getTop() + $dearSignMargin - $marginTopBottom, 
                    $page->getWidth() - $config->getMarginRight(), 
                    $textBox->getTop(), 
                    $this->bean->getDear(), 
                    \HaruPage::TALIGN_LEFT);
        }
        if ($this->bean->getSignature()) {
            $page->setFontAndSize(null, $textBox->getFontSize());
            $right = $page->getWidth() - $config->getMarginRight();
            $page->textRect(
                    $right - 250, 
                    $textBox->getBottom() - ($textBox->getFontSize() * 1.5), 
                    $right, 
                    $textBox->getBottom() - $marginTopBottom - $dearSignMargin, 
                    $this->bean->getSignature(), 
                    \HaruPage::TALIGN_LEFT);
        }
    }
    

    /**
     * @return \Osf\Pdf\Document\Bean\LetterBeanInterface
     */
    public function getBean()
    {
        return parent::getBean();
    }
}
