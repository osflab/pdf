<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Haru;

use Osf\Exception\ArchException;

/**
 * HaruPage proxy
 * 
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 1 nov. 2013
 * @package osf
 * @subpackage pdf
 * @deprecated since version 0.1
 */
abstract class PageProxy
{
    const IMG_VALIGN_TOP    = 0;
    const IMG_VALIGN_CENTER = 1;
    const IMG_VALIGN_BOTTOM = 2;
    const IMG_HALIGN_LEFT   = 0;
    const IMG_HALIGN_CENTER = 1;
    const IMG_HALIGN_RIGHT  = 2;
    
    /**
     * @var \HaruPage
     */
    protected $page;
    
    /**
     * @var Doc
     */
    protected $doc;
    
    protected $pageFormat;
    protected $pageOrientation;

    public function __construct(HaruDoc $doc, \HaruPage $page)
    {
        $this->doc = $doc;
        $this->page = $page;
    }

    public function __call($method, $properties)
    {
        return call_user_func_array(array(
                $this->page,
                $method
        ), $properties);
    }

    /**
     * @return \Osf\Pdf\Haru\HaruDoc
     */
    public function getDocument()
    {
        return $this->doc;
    }

    public function textOut($x, $y, $text)
    {
        return $this->page->textOut($x, $y, $this->doc->encode($text));
    }

    public function textRect($left, $top, $right, $bottom, $text, $align = \HaruPage::TALIGN_LEFT)
    {
        return $this->page->textRect($left, $top, $right, $bottom, 
                $this->doc->encode($text), $align);
    }

    public function setLineWidth($width)
    {
        return $this->page->setLineWidth($width);
    }

    public function setLineCap($cap)
    {
        return $this->page->setLineCap($cap);
    }

    public function setLineJoin($join)
    {
        return $this->page->setLineJoin($join);
    }

    public function setMiterLimit($limit)
    {
        return $this->Page->setMiterLimit($limit);
    }

    public function setFlatness($flatness)
    {
        return $this->page->setFlatness($flatness);
    }

    public function setDash($pattern, $phase)
    {
        return $this->page->setDash($pattern, $phase);
    }

    public function concat($a, $b, $c, $d, $x, $y)
    {
        return $this->page->Concat($a, $b, $c, $d, $x, $y);
    }

    public function getTransMatrix()
    {
        return $this->page->getTransMatrix();
    }

    public function setTextMatrix($a, $b, $c, $d, $x, $y)
    {
        return $this->page->setTextMatrix($a, $b, $c, $d, $x, $y);
    }

    public function getTextMatrix()
    {
        return $this->page->getTextMatrix();
    }

    /**
     * Déplace le curseur à la position (pour le mode path)
     * @param float $x
     * @param float $y
     */
    public function moveTo($x, $y)
    {
        return $this->page->moveTo($x, $y);
    }

    /**
     * Dessine les contours
     * @param bool $closePath
     */
    public function stroke($closePath = false)
    {
        return $this->page->stroke($closePath);
    }

    public function fill()
    {
        return $this->page->fill();
    }

    public function eofill()
    {
        return $this->page->eofill();
    }

    public function lineTo($x, $y)
    {
        return $this->page->lineTo($x, $y);
    }

    public function curveTo($x1, $y1, $x2, $y2, $x3, $y3)
    {
        return $this->page->curveTo($x1, $y1, $x2, $y2, $x3, $y3);
    }

    public function curveTo2($x2, $y2, $x3, $y3)
    {
        return $this->page->curveTo2($x2, $y2, $x3, $y3);
    }

    public function curveTo3($x1, $y1, $x3, $y3)
    {
        return $this->page->curveTo3($x1, $y1, $x3, $y3);
    }

    public function rectangle($x, $y, $width, $height)
    {
        return $this->page->rectangle($x, $y, $width, $height);
    }

    public function arc($x, $y, $ray, $ang1, $ang2)
    {
        return $this->page->arc($x, $y, $ray, $ang1, $ang2);
    }

    public function circle($x, $y, $ray)
    {
        return $this->page->circle($x, $y, $ray);
    }

    public function showText($text)
    {
        return $this->page->showText($text);
    }

    public function showTextNextLine($text)
    {
        return $this->page->showTextNextLine($text);
    }

    public function beginText()
    {
        return $this->page->beginText();
    }

    public function endText()
    {
        return $this->page->endText();
    }

    /**
     * Fonte préalablement déclarée + taille (nullable)
     * @param mixed $font
     * @param string $size
     */
    public function setFontAndSize($font = null, $size = null)
    {
        if ($size && !is_numeric($size)) {
            throw new ArchException('Bad font size type'); 
        }
        $font = $font === null ? $this->page->getCurrentFont() : $font;
        $size = $size === null ? $this->page->getCurrentFontSize() : (float) $size;
        if (!($font instanceof \HaruFont)) {
            $font = $this->doc->getFont($font);
        }
        return $this->page->setFontAndSize($font, $size);
    }
    
    /**
     * Type de fonte à charger + taille
     * @param string $fontType
     */
    public function setFontType($fontType, $size = null)
    {
        if (!in_array($fontType, array(
                HaruDoc::FONT_LIGHT,
                HaruDoc::FONT_LIGHT_ITALIC,
                HaruDoc::FONT_REGULAR,
                HaruDoc::FONT_REGULAR_ITALIC,
                HaruDoc::FONT_CONDENSED,
                HaruDoc::FONT_CONDENSED_BOLD,
                HaruDoc::FONT_BOLD,
                HaruDoc::FONT_BOLD_ITALIC,
                HaruDoc::FONT_MONO,
                HaruDoc::FONT_MONO_ITALIC,
                HaruDoc::FONT_MONO_BOLD,
                HaruDoc::FONT_MONO_BOLD_ITALIC
        ))) {
            throw new ArchException('Font type not found. Sea Doc::FONT_*');
        }
        return $this->setFontAndSize($this->getDocument()->getFontType($fontType), $size);
    }
    
    public function setFontTypeLight($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_LIGHT, $size);
    }
    
    public function setFontTypeLightItalic($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_LIGHT_ITALIC, $size);
    }
    
    public function setFontTypeRegular($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_REGULAR, $size);
    }
    
    public function setFontTypeRegularItalic($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_REGULAR_ITALIC, $size);
    }
    
    public function setFontTypeCondensed($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_CONDENSED, $size);
    }
    
    public function setFontTypeBold($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_BOLD, $size);
    }
    
    public function setFontTypeBoldItalic($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_BOLD_ITALIC, $size);
    }
    
    public function setFontTypeMono($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_MONO, $size);
    }
    
    public function setFontTypeMonoItalic($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_MONO_ITALIC, $size);
    }
    
    public function setFontTypeMonoBold($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_MONO_BOLD, $size);
    }
    
    public function setFontTypeMonoBoldItalic($size = null)
    {
        return $this->setFontType(HaruDoc::FONT_MONO_BOLD_ITALIC, $size);
    }
    
    
    public function setCharSpace($char_space)
    {
        return $this->page->setCharSpace($char_space);
    }

    public function setWordSpace($word_space)
    {
        return $this->page->setWordSpace($word_space);
    }

    public function setHorizontalScaling($scaling)
    {
        return $this->page->setHorizontalScaling($scaling);
    }

    /**
     * Interligne
     * @param float $textLeading
     */
    public function setTextLeading($textLeading)
    {
        return $this->page->setTextLeading($textLeading);
    }

    public function setTextRenderingMode($mode)
    {
        return $this->page->setTextRenderingMode($mode);
    }

    public function setTextRise($rise)
    {
        return $this->page->setTextRise($rise);
    }

    /**
     * Déplace la position du texte (depuis la position courante)
     * @param float $x
     * @param float $y
     * @param float $setLeading
     */
    public function moveTextPos($x, $y, $setLeading = 0)
    {
        return $this->page->moveTextPos($x, $y, $setLeading);
    }

    public function fillStroke($close_path)
    {
        return $this->page->fillStroke($close_path);
    }

    public function eoFillStroke($close_path)
    {
        return $this->page->eoFillStroke($close_path);
    }

    public function closePath()
    {
        return $this->page->closePath();
    }

    public function endPath()
    {
        return $this->page->endPath();
    }

    public function ellipse($x, $y, $xray, $yray)
    {
        return $this->page->ellipse($x, $y, $xray, $yray);
    }

    public function moveToNextLine()
    {
        return $this->page->moveToNextLine();
    }

    public function setGrayFill($value)
    {
        return $this->page->setGrayFill($value);
    }

    public function setGrayStroke($value)
    {
        return $this->page->setGrayStroke($value);
    }

    public function setRGBFill($r, $g, $b)
    {
        return $this->page->setRGBFill($r, $g, $b);
    }

    public function setRGBStroke($r, $g, $b)
    {
        return $this->page->setRGBStroke($r, $g, $b);
    }

    public function setCMYKFill($c, $m, $y, $k)
    {
        return $this->page->setCMYKFill($c, $m, $y, $k);
    }

    public function setCMYKStroke($c, $m, $y, $k)
    {
        return $this->page->setCMYKStroke($c, $m, $y, $k);
    }

    /**
     * Largeur de la page
     * @param integer $width
     */
    public function setWidth($width)
    {
        return $this->page->setWidth($width);
    }

    /**
     * Hauteur de la page
     * @param integer $height
     */
    public function setHeight($height)
    {
        return $this->page->setHeight($height);
    }

    /**
     * Taille prédéfinie
     * 
     * HPDF_PAGE_SIZE_LETTER      8.5 x 11 (inches)         612 x 792
     * HPDF_PAGE_SIZE_LEGAL       8.5 x 14 (inches)         612 x 1008
     * HPDF_PAGE_SIZE_A3          297 x 420 (mm)         841.89 x 1199.551
     * HPDF_PAGE_SIZE_A4          210 x 297 (mm)        595.276 x 841.89
     * HPDF_PAGE_SIZE_A5          148 x 210 (mm)        419.528 x 595.276
     * HPDF_PAGE_SIZE_B4          250 x 353 (mm)        708.661 x 1000.63
     * HPDF_PAGE_SIZE_B5          176 x 250 (mm)        498.898 x 708.661
     * HPDF_PAGE_SIZE_EXECUTIVE  7.25 x 10.5 (inches)      522 x 756
     * HPDF_PAGE_SIZE_US4x6         4 x 6 (inches)          288 x 432
     * HPDF_PAGE_SIZE_US4x8         4 x 8 (inches)          288 x 576
     * HPDF_PAGE_SIZE_US5x7         5 x 7 (inches)          360 x 504
     * HPDF_PAGE_SIZE_COMM10    4.125 x 9.5 (inches)        297 x 684
     * 
     * @param integer $size
     * @param integer $direction HPDF_PAGE_PORTRAIT ou HPDF_PAGE_LANDSCAPE
     */
    public function setSize($size, $direction)
    {
        $this->pageFormat = $size;
        $this->pageOrientation = $direction;
        return $this->page->setSize($size, $direction);
    }

    public function getPageFormat()
    {
        return $this->pageFormat;
    }
    
    public function getPageOrientation()
    {
        return $this->pageOrientation;
    }

    /**
     * Angle de rotation de la page
     * @param integer $angle
     */
    public function setRotate($angle)
    {
        return $this->page->setRotate($angle);
    }

    /**
     * Largeur de la page
     */
    public function getWidth()
    {
        return $this->page->getWidth();
    }

    /**
     * Hauteur de la page
     */
    public function getHeight()
    {
        return $this->page->getHeight();
    }

    /**
     * Crée une nouvelle destination pour un objet
     * @return \HaruDestination
     */
    public function createDestination()
    {
        return $this->page->createDestination();
    }

    /**
     * Crée une annotation
     * @param unknown_type $rectangle
     * @param string $text
     * @param unknown_type $encoder
     */
    public function createTextAnnotation($rectangle, $text, $encoder = null)
    {
        return $this->page->createTextAnnotation($rectangle, $text, $encoder);
    }

    public function createLinkAnnotation($rectangle, $destination)
    {
        return $this->page->createLinkAnnotation($rectangle, $destination);
    }

    public function createURLAnnotation($rectangle, $url)
    {
        return $this->page->createURLAnnotation($rectangle, $url);
    }

    /**
     * Calcule la largeur d'un texte
     * @param string $text
     * @return integer
     */
    public function getTextWidth($text)
    {
        return $this->page->getTextWidth($text);
    }

    /**
     * Mesure le nombre de caractères de $text qui peut être inclu dans la largeur spécifiée
     * @param string $text
     * @param integer $width
     * @param boolean $wordwrap
     * @return integer
     */
    public function measureText($text, $width, $wordwrap = true)
    {
        return $this->page->MeasureText($text, $width, $wordwrap);
    }

    /**
     * Retourne le mode graphique courant
     * @return integer
     */
    public function getGMode()
    {
        return $this->page->getGMode();
    }

    /**
     * Retourne la position graphique courante
     * @return array with 'x' and 'y'
     */
    public function getCurrentPos()
    {
        return $this->page->getCurrentPos();
    }

    /**
     * Retourne la position de texte courante
     * @return array with 'x' and 'y'
     */
    public function getCurrentTextPos()
    {
        return $this->page->getCurrentTextPos();
    }
    
    /**
     * Retourne la position de texte courante
     * @return array with 'x' and 'y'
     */

    /**
     * Retourne la fonte courante
     * @return \HaruFont
     */
    public function getCurrentFont()
    {
        return $this->page->getCurrentFont();
    }

    /**
     * Retourne la taille de fonte courante
     * @return float
     */
    public function getCurrentFontSize()
    {
        return $this->page->getCurrentFontSize();
    }

    /**
     * Retourne la largeur de ligne courante de la page
     * @return float
     */
    public function getLineWidth()
    {
        return $this->page->getLineWidth();
    }

    /**
     * ???
     * @return integer
     */
    public function getLineCap()
    {
        return $this->page->getLineCap();
    }

    /**
     * Retourne le style de jointure des lignes
     * @return integer
     */
    public function getLineJoin()
    {
        return $this->page->getLineJoin();
    }

    /**
     * Limite de pointe de la page ??
     * @return float
     */
    public function getMiterLimit()
    {
        return $this->page->getMiterLimit();
    }

    /**
     * Retourne le pattern courant
     * @return array ?
     */
    public function getDash()
    {
        return $this->page->getDash();
    }

    /**
     * Valeur de la netteté courante
     * @return float
     */
    public function getFlatness()
    {
        return $this->page->getFlatness();
    }

    /**
     * Espacement courant des caractères (0 = par défaut)
     * @return float
     */
    public function getCharSpace()
    {
        return $this->page->getCharSpace();
    }

    /**
     * Espacement courant des mots (0 = par défaut)
     * @return float
     */
    public function getWordSpace()
    {
        return $this->page->getWordSpace();
    }

    /**
     * Retourne l'échelle horizontale de la page (pour le texte) en pourcentage (défaut 100)
     * @return float
     */
    public function getHorizontalScaling()
    {
        return $this->page->getHorizontalScaling();
    }

    /**
     * Valeur courante de l'interligne
     * @return float
     */
    public function getTextLeading()
    {
        return $this->page->getTextLeading();
    }

    /**
     * Type de rendu du texte ?
     * @return integer
     */
    public function getTextRenderingMode()
    {
        return $this->page->getTextRenderingMode();
    }

    /**
     * Hauteur du texte (0 = par défaut)
     * @return float
     */
    public function getTextRise()
    {
        return $this->page->getTextRise();
    }

    /**
     * Couleur de remplissage par défaut
     * @return array ('r', 'g', 'b')
     */
    public function getRGBFill()
    {
        return $this->page->getRGBFill();
    }

    /**
     * Couleur de trait par défaut
     * @return array ('r', 'g', 'b')
     */
    public function getRGBStroke()
    {
        return $this->page->getRGBStroke();
    }

    /**
     * Couleur de remplissage par défaut au format CMYK
     * @return array ('c', 'm', 'y', 'k')
     */
    public function getCMYKFill()
    {
        return $this->page->getCMYKFill();
    }

    /**
     * Couleur de trait par défaut au format CMYK
     * @return array ('c', 'm', 'y', 'k')
     */
    public function getCMYKStroke()
    {
        return $this->page->getCMYKStroke();
    }

    /**
     * Niveau de gris de remplissage par défaut
     * @return float
     */
    public function getGrayFill()
    {
        return $this->page->getGrayFill();
    }

    /**
     * Niveau de gris de trait par défaut
     * @return float
     */
    public function getGrayStroke()
    {
        return $this->page->getGrayStroke();
    }

    /**
     * Espace des couleurs par défaut pour le remplissage
     * @return integer
     */
    public function getFillingColorSpace()
    {
        return $this->page->getFillingColorSpace();
    }

    /**
     * Espace des couleurs par défaut pour les traits
     * @return integer
     */
    public function getStrokingColorSpace()
    {
        return $this->page->getStrokingColorSpace();
    }

    /**
     * Configuration de la transition entre les pages (pour une présentation)
     * 
     * Types de transition : 
     * HPDF_TS_WIPE_RIGHT
     * HPDF_TS_WIPE_UP
     * HPDF_TS_WIPE_LEFT
     * HPDF_TS_WIPE_DOWN
     * HPDF_TS_BARN_DOORS_HORIZONTAL_OUT
     * HPDF_TS_BARN_DOORS_HORIZONTAL_IN
     * HPDF_TS_BARN_DOORS_VERTICAL_OUT
     * HPDF_TS_BARN_DOORS_VERTICAL_IN
     * HPDF_TS_BOX_OUT
     * HPDF_TS_BOX_IN
     * HPDF_TS_BLINDS_HORIZONTAL
     * HPDF_TS_BLINDS_VERTICAL
     * HPDF_TS_DISSOLVE
     * HPDF_TS_GLITTER_RIGHT
     * HPDF_TS_GLITTER_DOWN
     * HPDF_TS_GLITTER_TOP_LEFT_TO_BOTTOM_RIGHT
     * HPDF_TS_REPLACE
     * 
     * @param integer $type
     * @param float $displayTime
     * @param float $transitionTime
     */
    public function setSlideShow($type, $displayTime, $transitionTime)
    {
        return $this->page->setSlideShow($type, $displayTime, $transitionTime);
    }

    /**
     * Zoom par défaut de la page ?
     * @param float $zoom
     */
    public function setZoom($zoom)
    {
        return $this->page->setZoom($zoom);
    }
}
