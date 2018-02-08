<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Helper;

use Osf\Exception\ArchException;

use Osf\Pdf\Haru\Page;
use Osf\Pdf\Document\BaseDocument;

/**
 * Classe mère des helpers PDF
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 31 oct. 2013
 * @package osf
 * @subpackage pdf
 */
abstract class BaseHelper
{
    const RENDER_GRAPHIC = 0;
    const RENDER_TEXT = 1;
    const RENDER_PATH = 2;
    
    protected $initialParams;
    protected $initialPage;
    
    /**
     * @var BaseDocument
     */
    protected $document;
    
    public function __construct(BaseDocument $document)
    {
        $this->document = $document;
    }
    
    /**
     * Enregistre les paramètres effectue les préparations
     */
    public function startRender(Page $page, $namespace)
    {
        $currentFont = $page->getCurrentFont();
        $currentSize = $page->getCurrentFontSize();
        $this->initialParams[$namespace]['font'] = !$currentFont ? null : $currentFont;
        $this->initialParams[$namespace]['size'] = !$currentSize ? null : $currentSize;
        //$this->initialParams['mode'] = $page->getGMode();
        $this->initialPage[$namespace] = $page;
    }
    
    /**
     * Rétablit les paramètres
     */
    public function stopRender(Page $page, $namespace)
    {
        if (!isset($this->initialPage[$namespace]) || $page !== $this->initialPage[$namespace]) {
            throw new ArchException('This is not the same page, check startRender()');
        }
        $params = $this->initialParams[$namespace];
        if ($params['font'] || $params['size']) {
            $page->setFontAndSize($params['font'], $params['size']);
        }
    }
    
    abstract public function render(Page $page = null);
}
