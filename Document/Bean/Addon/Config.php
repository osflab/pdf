<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Bean\Addon;

use Osf\Pdf\Document\Config\BaseDocumentConfig;

/**
 * Document general configuration
 * 
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage pdf
 */
trait Config
{
    /**
     * @var BaseDocumentConfig
     */
    protected $config;
    
    /**
     * @return \Osf\Pdf\Document\Config\BaseDocumentConfig
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->setConfig(new BaseDocumentConfig());
        }
        return $this->config;
    }

    /**
     * @param BaseDocumentConfig $config
     * @return $this
     */
    public function setConfig(BaseDocumentConfig $config)
    {
        $this->config = $config;
        return $this;
    }
}
