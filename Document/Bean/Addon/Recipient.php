<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Bean\Addon;

use Osf\Pdf\Document\Bean\ContactBean;
use DB;

/**
 * Recipient trait
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage pdf
 */
trait Recipient
{
    /**
     * @var ContactBean
     */
    protected $recipient;
    
    /**
     * @param ContactBean $contact
     * @return $this
     */
    public function setRecipient(ContactBean $contact = null)
    {
        $this->recipient = $contact;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function hasRecipient(): bool
    {
        return (bool) $this->recipient;
    }
    
    /**
     * @param bool $computeTitle
     * @return ContactBean
     */
    public function getRecipient(bool $computeTitle = true): ContactBean
    {
        if (!($this->recipient instanceof ContactBean)) {
            $this->recipient = new ContactBean();
        }
        if ($computeTitle) {
            $this->recipient->computeAddressTitle();
        }
        return $this->recipient;
    }
    
    /**
     * Get the company hash from database
     * @return string
     */
    public function getRecipientCompanyHash(): string
    {
        return (string) DB::getCompanyTable()->findSafe($this->getRecipient(false)->getIdCompany())->getHash();
    }
}
