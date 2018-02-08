<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Bean;

/**
 * Base document content bean
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 12 nov. 2013
 * @package osf
 * @subpackage pdf
 */
class BaseDocumentBean extends AbstractPdfDocumentBean
{
    const FILENAME_DATE_FORMAT = 'Ymd-Hi';
    
    const STATUS_CREATED   = 'created';
    const STATUS_SENT      = 'sent';
    const STATUS_READ      = 'read';
    const STATUS_PROCESSED = 'processed';
    const STATUS_CANCELED  = 'canceled';
    
    const STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_SENT,
        self::STATUS_READ,
        self::STATUS_PROCESSED,
        self::STATUS_CANCELED,
    ];
    
    const TYPE_LETTER  = 'letter';
    const TYPE_FORM    = 'form';
    const TYPE_QUOTE   = 'quote';
    const TYPE_ORDER   = 'order';
    const TYPE_INVOICE = 'invoice';
    
    const TYPES = [
        self::TYPE_LETTER,
        self::TYPE_FORM,
        self::TYPE_QUOTE,
        self::TYPE_ORDER,
        self::TYPE_INVOICE,
    ];
    
    const COLORS = [
        self::TYPE_LETTER  => 'blue',
        self::TYPE_FORM    => 'blue',
        self::TYPE_QUOTE   => 'blue',
        self::TYPE_ORDER   => 'blue',
        self::TYPE_INVOICE => 'blue'
    ];
    
    const ICONS = [
        self::TYPE_LETTER  => 'envelope-o',
        self::TYPE_FORM    => 'check-square-o',
        self::TYPE_QUOTE   => 'file-o',
        self::TYPE_ORDER   => 'file-text-o',
        self::TYPE_INVOICE => 'file-text'
    ];
    
    const EVENT_CREATION         = 'creation';
    const EVENT_UPDATE           = 'update';
    const EVENT_SENDING          = 'sending';
    const EVENT_READ             = 'read';
    const EVENT_PROCESS          = 'process';
    const EVENT_DELETE           = 'delete';
    const EVENT_STATUS_CREATED   = 'status_created';
    const EVENT_STATUS_SENT      = 'status_sent';
    const EVENT_STATUS_PROCESSED = 'status_processed';
    const EVENT_STATUS_CANCELED  = 'status_canceled';
    
    const EVENTS =  [
        self::EVENT_CREATION,
        self::EVENT_UPDATE,
        self::EVENT_SENDING,
        self::EVENT_READ,
        self::EVENT_PROCESS,
        self::EVENT_DELETE,
        self::EVENT_STATUS_CREATED,
        self::EVENT_STATUS_SENT,
        self::EVENT_STATUS_PROCESSED,
        self::EVENT_STATUS_CANCELED,
    ];
    
    private const ALLOWED_PERMISSIVE = [
        self::STATUS_CREATED => [
            self::STATUS_CANCELED  => true,
            self::STATUS_PROCESSED => true,
        ],
        self::STATUS_SENT => [
            self::STATUS_CANCELED  => true,
            self::STATUS_PROCESSED => true,
            self::STATUS_CREATED   => true,
        ],
        self::STATUS_READ => [
            self::STATUS_CANCELED  => true,
            self::STATUS_PROCESSED => true,
            self::STATUS_CREATED   => true,
        ],
        self::STATUS_PROCESSED => [
            self::STATUS_CANCELED  => true,
            self::STATUS_CREATED   => true,
        ],
        self::STATUS_CANCELED => [
            self::STATUS_PROCESSED => true,
            self::STATUS_CREATED   => true,
        ],
    ];
    
    const ALLOWED_OWNER_ACTIONS = [
        self::TYPE_INVOICE => [
            self::STATUS_CREATED   => [self::STATUS_CANCELED => true],
            self::STATUS_SENT      => [self::STATUS_PROCESSED => true],
            self::STATUS_READ      => [self::STATUS_PROCESSED => true],
            self::STATUS_PROCESSED => [],
            self::STATUS_CANCELED  => [self::STATUS_CREATED => true],
        ],
        self::TYPE_ORDER => [
            self::STATUS_CREATED   => [self::STATUS_CANCELED => true],
            self::STATUS_SENT      => [],
            self::STATUS_READ      => [self::STATUS_PROCESSED => true],
            self::STATUS_PROCESSED => [],
            self::STATUS_CANCELED  => [self::STATUS_CREATED => true],
        ],
        self::TYPE_QUOTE => self::ALLOWED_PERMISSIVE,
        self::TYPE_LETTER => self::ALLOWED_PERMISSIVE,
    ];
    
    use Addon\Libs;
    use Addon\Parts;
    use Addon\Config;
    use Addon\Status;
    use Addon\Provider;
    use Addon\Recipient;
}
