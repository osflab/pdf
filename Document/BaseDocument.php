<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document;

use Osf\Pdf\Haru\Page;
use Osf\Pdf\Haru\HaruDoc;
use Osf\Pdf\Document\Bean\BaseDocumentBeanInterface;
use Osf\Pdf\Document\Bean\BaseDocumentBean;

include_once __DIR__ . '/Helpers.php';

/**
 * Base PDF document
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 26 sept. 2013
 * @package osf
 * @subpackage pdf
 */
class BaseDocument extends HaruDoc
{
    /**
     * @var \Osf\Pdf\Document\Bean\BaseDocumentBeanInterface
     */
    protected $bean;
    
    use Helpers;
    
    /**
     * @var Page
     */
    protected $currentPage;
    
    public function __construct(BaseDocumentBeanInterface $bean)
    {
        $this->bean = $bean;
        parent::__construct();
        if (!$this->getPageMode()) {
            $pageMode = (HaruDoc::PAGE_MODE_USE_THUMBS | HaruDoc::PAGE_MODE_USE_OUTLINE) & !HaruDoc::PAGE_MODE_FULL_SCREEN;
            $this->setPageMode($pageMode);
        }
        $this->setCompressionMode(HaruDoc::COMP_ALL);
        if (!$bean->getDate() && ($bean instanceof BaseDocumentBean)) {
            $bean->setDate(new \DateTime());
        }
    }
    
    /**
     * @return Page
     */
    public function newPage($format = \HaruPage::SIZE_A4, $orientation = \HaruPage::PORTRAIT)
    {
        $this->currentPage = parent::newPage($format, $orientation);
        return $this->currentPage;
    }
    
    /**
     * @return Page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
    
    /**
     * Set read password, persmissions and encryption parameters
     * @param unknown_type $password
     */
    public function setRestrictions($ownerPassword, $userPassword)
    {
        $this->setPassword($ownerPassword, $userPassword);
        $this->setPermission(HaruDoc::ENABLE_READ | HaruDoc::ENABLE_PRINT);
        $this->setEncryptionMode(HaruDoc::ENCRYPT_R2, 128);
    }
    
    protected function setHeaders($keywords = null)
    {
        $this->setInfoAttr(HaruDoc::INFO_AUTHOR, $this->encode('SimpleManager by OpenStates - http://www.openstates.com'));
        $this->setInfoAttr(HaruDoc::INFO_CREATOR, $this->encode('SimpleManager PDF Generator'));
        $this->setInfoAttr(HaruDoc::INFO_TITLE, $this->encode($this->bean->getTitle()));
        if ($this->bean->getSubject()) {
            $this->setInfoAttr(HaruDoc::INFO_SUBJECT, $this->encode($this->bean->getSubject()));
        }
        $date = $this->bean->getDate();
        $this->setInfoDateAttr(HaruDoc::INFO_CREATION_DATE,
                $date->format('Y'),
                $date->format('m'),
                $date->format('d'),
                $date->format('h'),
                $date->format('i'),
                $date->format('s'), $date->getOffset() >= 0 ? '+' : '-', intval(abs($date->getOffset() / (60 * 60))), 0);
        $this->setInfoDateAttr(HaruDoc::INFO_MOD_DATE,
                $date->format('Y'),
                $date->format('m'),
                $date->format('d'),
                $date->format('h'),
                $date->format('i'),
                $date->format('s'), $date->getOffset() >= 0 ? '+' : '-', intval(abs($date->getOffset() / (60 * 60))), 0);
        if ($keywords) {
            $this->setInfoAttr(HaruDoc::INFO_KEYWORDS, $this->encode($keywords));
        }
    }
    
    /**
     * @return \Osf\Pdf\Document\Bean\BaseDocumentBeanInterface
     */
    public function getBean()
    {
        return $this->bean;
    }
}
