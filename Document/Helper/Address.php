<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Helper;

use Osf\Pdf\Haru\HaruDoc;
use Osf\Pdf\Haru\Page;
use Osf\Pdf\Document\Bean\AddressBeanInterface;

/**
 * Affichage d'une adresse postale
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 31 oct. 2013
 * @package osf
 * @subpackage pdf
 */
class Address extends BaseHelper
{
    protected $position = array('x' => null, 'y' => null);
    protected $titleFont;
    protected $titleSize;
    protected $bodyFont;
    protected $bodySize;
    protected $address;
    protected $libeles; 
    
    /**
     * @var \Osf\Pdf\Document\Bean\ImageBean
     */
    protected $logo;
    
    /**
     * @return AddressBeanInterface
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param AddressBeanInterface $address
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setAddress(AddressBeanInterface $address)
    {
        $this->address = $address;
        return $this;
    }
    
    /**
     * Complète l'adresse avec téléphone, fax, mail
     * @param unknown_type $tel
     * @param unknown_type $fax
     * @param unknown_type $mail
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setTelFaxMail($tel = null, $fax = null, $mail = null)
    {
       if ($tel) {
            $this->addLibele('tél.', $tel);
        } 
        if ($fax) {
            $this->addLibele('fax.', $fax);
        }
        if ($mail) {
            $this->addLibele('mél.', $mail);
        }
        return $this;
    }
    
    public function addLibele($key, $value)
    {
        $this->libeles[$key] = trim($value);
        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getTitleFont()
    {
        return $this->titleFont;
    }

    public function getTitleSize()
    {
        return $this->titleSize;
    }

    public function getBodyFont()
    {
        return $this->bodyFont;
    }

    public function getBodySize()
    {
        return $this->bodySize;
    }

    /**
     * @return \Osf\Pdf\Document\Bean\ImageBean
     */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
     * @param unknown_type $x
     * @param unknown_type $y
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setPosition($left, $top, $width = null, $height = null)
    {
        $this->position = array(
                'x' => (int) $left, 
                'y' => (int) $top, 
                'w' => (int) $width, 
                'h' => (int) $height);
        return $this;
    }

    /**
     * @param unknown_type $titleFont
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setTitleFont($titleFont)
    {
        $this->titleFont = $titleFont;
        return $this;
    }

    /**
     * @param unknown_type $titleSize
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setTitleSize($titleSize)
    {
        $this->titleSize = $titleSize;
        return $this;
    }

    /**
     * @param unknown_type $bodyFont
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setBodyFont($bodyFont)
    {
        $this->bodyFont = $bodyFont;
        return $this;
    }

    /**
     * @param unknown_type $bodySize
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setBodySize($bodySize)
    {
        $this->bodySize = $bodySize;
        return $this;
    }
    
    /**
     * Set the logo image to display
     * @param \Osf\Pdf\Document\Bean\ImageBean $logoImage
     * @return \Osf\Pdf\Document\Helper\Address
     */
    public function setLogo(\Osf\Pdf\Document\Bean\ImageBean $logoImage)
    {
        $this->logo = $logoImage;
        return $this;
    }
    
    /**
     * @see \Osf\Pdf\Document\Helper\BaseHelper::render()
     */
    public function render(Page $page = null, $fontAutoSize = true)
    {
        $doc = $this->document;
        $config = $doc->getBean()->getConfig();
        $page = $page === null ? $doc->getCurrentPage() : $page;
        $this->startRender($page, __CLASS__);
        if ($this->getTitleFont() === null) {
            $this->setTitleFont(HaruDoc::FONT_REGULAR);
        }
        if ($this->getBodyFont() === null) {
            $this->setBodyFont(HaruDoc::FONT_LIGHT);
        }
        if ($this->getBodySize() === null) {
            $this->setBodySize($doc->getFontSize());
        }
        if ($this->getTitleSize() === null) {
            $this->setTitleSize($this->getBodySize() + 3);
        }
        $x = !$this->getPosition()['x'] ? $page->getCurrentTextPos()['x'] : $this->getPosition()['x'];
        $y = !$this->getPosition()['y'] ? $page->getCurrentTextPos()['y'] : $this->getPosition()['y'];
        $width  = !$this->getPosition()['w'] ? $page->getWidth() - $config->getMarginRight() - $x : $this->getPosition()['w'];
        $height = !$this->getPosition()['h'] ? $y - $config->getMarginBottom() : $this->getPosition()['h'];
        if ($this->logo) {
            $image = $doc->loadPNG($this->logo->getImageFile());
            $logoDisplay = $image->getWidth() / $image->getHeight() > 1.3 ? 'h' : 'v';
            $page->endText();
            if ($logoDisplay == 'v') {
                $page->drawImage($image, $x, $y - 170, 50, 170);
                $x += 60;
                $width -= 60;
                $y -= 2;
                $height -= 2;
            } else {
                $page->drawImage($image, $x, $y - 40, 200, 40);
                $y -= 45;
                $height -= 45;
            }
            $page->beginText();
        } else {
            $logoDisplay = false;
        }
        
        $page->debugDisplayZone($x, $y - $height, $width, $height);
        if ($this->getAddress()->getTitle()) {
            $page->setFontAndSize($doc->getFontType($this->getTitleFont()), $this->getTitleSize());
            if ($this->logo) {
                $colors = $this->logo->getColors();
                if (isset($colors[0])) {
                    $page->setRGBFill($colors[0]['r'] / 255, $colors[0]['g'] / 255, $colors[0]['b'] / 255);
                } else {
                    $page->setGrayFill(0);
                }
            }
            $page->textOut($x, $y - $this->getTitleSize() + 3, $this->getAddress()->getTitle());
            if ($this->logo) {
                $page->setRGBFill(0, 0, 0);
            }
            $page->setFontAndSize($doc->getFontType($this->getBodyFont()), $this->getBodySize());
        }
        $address = trim($this->getAddress()->getAddress());
        $address .= "\n" . trim($this->getAddress()->getPostalCode() . ' ' . $this->getAddress()->getCity());
        if ($fontAutoSize) {
            $y = ceil($page->getCurrentTextPos()['y'] - ($this->getTitleSize() / 2.5));
            if (count($this->libeles)) {
                $address .= "\n";
                foreach ($this->libeles as $key => $value) {
                    $address .= "\n" . trim($key . ' ' . $value);
                }
            }
            $fontSize = $page->getCurrentFontSize();
            $textBox = $doc->textBox()
                ->setContent($address)
                ->setCoordinates($x, $y, $x + $width, $y - $height)
                ->setMaxFontSize($page->getCurrentFontSize())
                ->setTextAlign(TextBox::TALIGN_LEFT)
                ->setVerticalAlign(TextBox::VALIGN_TOP)
                ->render($page);
        }
        else {
            $cpt = 0;
            $leading = $this->getBodySize() / 2.5;
            foreach (explode("\n", $address) as $line) {
                $page->moveTextPos(0, -$this->getBodySize() - $leading);
                $page->textOut($x, $page->getCurrentTextPos()['y'], $line);
                $cpt++;
            }
            if (count($this->libeles)) {
                $page->moveTextPos(0, -$this->getBodySize() - $leading);
                $margin = 22;
                foreach ($this->libeles as $key => $value) {
                    $page->moveTextPos(0, -$this->getBodySize() - $leading);
                    $page->textOut($x, $page->getCurrentTextPos()['y'], trim($key) . ' ');
                    $page->textOut(max($x + $margin, $page->getCurrentTextPos()['x']), $page->getCurrentTextPos()['y'], trim($value));
                }
            }
        }
            
        $this->stopRender($page, __CLASS__);
    }
}
