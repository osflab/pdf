<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document;

use Osf\Pdf\Document\Helper\Footer;
use Osf\Pdf\Document\Helper\TextBox;
use Osf\Pdf\Document\Helper\Address;

/**
 * Trait d'accès aux helpers PDF
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 31 oct. 2013
 * @package osf
 * @subpackage pdf
 */
trait Helpers
{
    /**
     * @return Address
     */
    public function address()
    {
        return new Address($this);
    }
    
    /**
     * @return TextBox
     */
    public function textBox()
    {
        return new TextBox($this);
    }
    
    /**
     * @return Footer
     */
    public function footer()
    {
        return new Footer($this);
    }
}
