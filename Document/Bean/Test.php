<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Pdf\Document\Bean;

use Osf\Test\Runner as OsfTest;

/**
 * Beans test
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage test
 */
class Test extends OsfTest
{
    public static function run()
    {
        self::reset();
        
        $pb = new ProductBean();
        $pb->setPrice(100);
        $pb->setPriceType(ProductBean::PRICE_HT);
        $pb->setQuantity(8);
        $pb->setTax(5.5);
        $pb->setDiscount(10);
        
        self::assertEqual($pb->getPriceHT(), (float) 100);
        self::assertEqual($pb->getPriceTTC(), (float) 105.5);
        self::assertEqual($pb->getPriceWithDiscountHT(), (float) 90);
        self::assertEqual($pb->getPriceWithDiscountTTC(), (100 + (100 * 0.055)) * 0.9);
        self::assertEqual($pb->getTotalPriceHT(), 100 * 0.9 * 8);
        self::assertEqual($pb->getTotalPriceTTC(), (100 + (100 * 0.055)) * 0.9 * 8);
        
        $pb->setPriceType(ProductBean::PRICE_TTC);
        $pb->setPrice(105.5);
        
        self::assertEqual($pb->getPriceHT(), (float) 100);
        self::assertEqual($pb->getPriceTTC(), (float) 105.5);
        self::assertEqual($pb->getPriceWithDiscountHT(), (float) 90);
        self::assertEqual($pb->getPriceWithDiscountTTC(), (100 + (100 * 0.055)) * 0.9);
        self::assertEqual($pb->getTotalPriceHT(), 100 * 0.9 * 8);
        self::assertEqual($pb->getTotalPriceTTC(), (100 + (100 * 0.055)) * 0.9 * 8);
        
        return self::getResult();
    }
}
