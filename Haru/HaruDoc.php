<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Haru;

use Osf\Exception\ArchException;

/**
 * Extends HaruDoc
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 30 sept. 2013
 * @package osf
 * @subpackage pdf
 * @deprecated since version 0.1
 */
class HaruDoc extends \HaruDoc
{
    const INTERNAL_ENCODING = 'ISO8859-15';
    //const INTERNAL_ENCODING = 'Ancient-UTF8-H';
    const EMBDED_FONTS = true;
    const DEFAULT_FONT_FAMILY    = 'arial';
    const BUILTIN_FONT_HELVETICA = 'builtin-helvetica';
    const BUILTIN_FONT_TIMES     = 'builtin-times';

    const FONT_CONDENSED        = 'condensed';
    const FONT_CONDENSED_BOLD   = 'condensed_bold';
    const FONT_LIGHT            = 'light';
    const FONT_LIGHT_ITALIC     = 'light_italic';
    const FONT_REGULAR          = 'regular';
    const FONT_REGULAR_ITALIC   = 'regular_italic';
    const FONT_BOLD             = 'bold';
    const FONT_BOLD_ITALIC      = 'bold_italic';
    const FONT_MONO             = 'mono';
    const FONT_MONO_ITALIC      = 'mono_italic';
    const FONT_MONO_BOLD        = 'mono_bold';
    const FONT_MONO_BOLD_ITALIC = 'mono_bold_italic';
    
    protected $builtinFonts = array(
        self::BUILTIN_FONT_HELVETICA => array(
            self::FONT_CONDENSED => 'Helvetica',
            self::FONT_CONDENSED_BOLD => 'Helvetica-Bold',
            self::FONT_LIGHT => 'Helvetica',
            self::FONT_LIGHT_ITALIC => 'Helvetica-Oblique',
            self::FONT_REGULAR => 'Helvetica-Bold',
            self::FONT_REGULAR_ITALIC => 'Helvetica-BoldOblique',
            self::FONT_BOLD => 'Helvetica-Bold',
            self::FONT_BOLD_ITALIC => 'Helvetica-BoldOblique',
            self::FONT_MONO => 'Courier',
            self::FONT_MONO_BOLD => 'Courier-Bold',
            self::FONT_MONO_BOLD_ITALIC => 'Courier-BoldOblique',
            self::FONT_MONO_ITALIC => 'Courier-Oblique',
        ),
        self::BUILTIN_FONT_TIMES => array(
            self::FONT_CONDENSED => 'Times-Roman',
            self::FONT_CONDENSED_BOLD => 'Times-Bold',
            self::FONT_LIGHT => 'Times-Roman',
            self::FONT_LIGHT_ITALIC => 'Times-Italic',
            self::FONT_REGULAR => 'Times-Bold',
            self::FONT_REGULAR_ITALIC => 'Times-BoldItalic',
            self::FONT_BOLD => 'Times-Bold',
            self::FONT_BOLD_ITALIC => 'Times-BoldItalic',
            self::FONT_MONO => 'Courier',
            self::FONT_MONO_BOLD => 'Courier-Bold',
            self::FONT_MONO_BOLD_ITALIC => 'Courier-BoldOblique',
            self::FONT_MONO_ITALIC => 'Courier-Oblique',
        )
    );
    
    protected $pages             = array();
    protected $encoding          = self::INTERNAL_ENCODING;
    protected $fontFamily        = self::DEFAULT_FONT_FAMILY;
    protected $fontSize          = 11;
    
    protected static $fontKeys = array();
    protected $loadedFonts = array();
    
    /**
     * @var \Osf\Pdf\Haru\Doc
     */
    protected $mock = null;
    
    /**
     * @var \Osf\Pdf\Haru\Page
     */
    protected $currentPage;
    
    public function __construct()
    {
        parent::__construct();
        //$this->useUTFEncodings();
    }
    
    /**
     * @return Page
     */
    public function newPage($format = \HaruPage::SIZE_A4, $orientation = \HaruPage::PORTRAIT)
    {
        $page = $this->addPage();
        $page->setSize($format, $orientation);
        return $page;
    }
    
    /**
     * Encode from Unicode to internal Haru encoding
     * @param string $text
     * @return string
     */
    public function encode($text)
    {
        //return $text;
        return iconv('UTF-8', $this->getEncoding(), $text);
    }

    /**
     * @return PageCompletion
     */
    public function addPage()
    {
        $haruPage = parent::addPage();
        $page = new Page($this, $haruPage);
        $this->pages[] = $page;
        $this->currentPage = $page;
        return $page;
    }
    
    /**
     * @see HaruDoc::getCurrentPage()
     * @return \Osf\Pdf\Haru\Page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
    
    public function getEncoding()
    {
        return $this->encoding;
    }
    
    /**
     * Set font family (default is ubuntu)
     * @param string $family
     * @return \Osf\Pdf\Haru\HaruDoc
     */
    public function setFontFamily($family)
    {
        $this->fontFamily = $family;
        return $this;
    }
    
    /**
     * @param int $size
     * @return \Osf\Pdf\Haru\HaruDoc
     */
    public function setFontSize($size)
    {
        $this->fontSize = (float) $size;
        return $this;
    }
    
    public function getFontFamily()
    {
        return $this->fontFamily;
    }

    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * Fichier d'une fonte à partir du type et de la famille
     * @param unknown_type $fontType
     * @param unknown_type $fontFamily
     * @throws ArchException
     * @return string
     */
    protected function getFontFile($fontType, $fontFamily = null)
    {
        if ($fontFamily === null) {
            $fontFamily = $this->fontFamily;
        }
        if (!preg_match('/^[a-z_]+$/', $fontFamily)) {
            throw new ArchException('Bad font family syntax, see HaruDoc::FONT_*');
        }
        if (!preg_match('/^[a-z_]+$/', $fontType)) {
            throw new ArchException('Bad font type syntax, see HaruDoc::FONT_*');
        }
        $fontFile = __DIR__ . '/../../../../vendors/fonts/' . $fontFamily . '_' . $fontType . '.ttf';
        if (!file_exists($fontFile)) {
            throw new ArchException('Font ' . $fontFamily . '::' . $fontType . ' not found');
        }
        return $fontFile;
    }
    
    /**
     * Charge une fonte externe à partir du type et d'une famille
     * @return \HaruFont
     */
    public function getFontType($fontType = self::FONT_REGULAR, $fontFamily = null, $encoding = null)
    {
        $fontFamily = $fontFamily === null ? $this->getFontFamily() : $fontFamily;
        if ($encoding === null) {
            $encoding = $this->getEncoding();
        }
        if (array_key_exists($fontFamily, $this->builtinFonts)) {
            if (!isset($this->builtinFonts[$fontFamily][$fontType])) {
                throw new ArchException('Bad font type');
            }
            return parent::getFont($this->builtinFonts[$fontFamily][$fontType], $encoding);
        }
        $fontName = $fontType . '#' . $fontFamily;
        if (is_string($fontName) && array_key_exists($fontName, self::$fontKeys)) {
            $fontName = self::$fontKeys[$fontName];
        }
        if (!array_key_exists($fontName, $this->loadedFonts)) {
            $fontFile = $this->getFontFile($fontType, $fontFamily);
            $this->loadedFonts[$fontName] = $this->loadTTF($fontFile, self::EMBDED_FONTS);
            self::$fontKeys[$this->loadedFonts[$fontName]] = $fontName;
        }
        return parent::getFont($this->loadedFonts[$fontName], $encoding);
    }
    
    /**
     * @see HaruDoc::getFont()
     */
    public function getFont($fontname, $encoding = null)
    {
        if (array_key_exists($fontname, self::$fontKeys)) {
            list ($type, $family) = explode('#', self::$fontKeys[$fontname]);
            return $this->getFontType($type, $family);
        }
        return parent::getFont($fontname, $encoding);
    }
    
    /**
     * @return \HaruOutline
     */
    public function createOutline($title, $parent_outline = null, $encoder = null)
    {
        return parent::createOutline($this->encode($title), $parent_outline, $encoder);
    }
    
    /**
     * @return \Osf\Pdf\Haru\Doc
     */
    public function getMock()
    {
        if ($this->mock === null) {
            $this->mock = new self();
            $this->mock->setPageMode($this->getPageMode());
            $this->mock->setPageLayout($this->getPageLayout());
            $this->mock->newPage($this->getCurrentPage()->getPageFormat(), $this->getCurrentPage()->getPageOrientation());
        }
        return $this->mock;
    }
}
