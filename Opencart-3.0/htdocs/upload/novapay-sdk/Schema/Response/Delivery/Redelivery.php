<?php
/**
 * Redelivery schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */

namespace Novapay\Payment\SDK\Schema\Response\Delivery;

use Novapay\Payment\SDK\Schema\Schema;

/**
 * Redelivery schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class Redelivery extends Schema
{
    /**
     * Тип груза
     * 
     * @var string 
     */
    public $CargoType;
    /**
     * Сумма
     * 
     * @var float
     */
    public $Amount;
}