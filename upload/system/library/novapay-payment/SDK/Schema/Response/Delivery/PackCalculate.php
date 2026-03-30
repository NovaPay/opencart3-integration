<?php
/**
 * Package Calculate schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702ee2a0fe4f0cf4fc53ef
 */

namespace Novapay\Payment\SDK\Schema\Response\Delivery;

use Novapay\Payment\SDK\Schema\Schema;

/**
 * Package Calculate schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702ee2a0fe4f0cf4fc53ef
 */
class PackCalculate extends Schema
{
    /**
     * Кол-во упаковок
     * 
     * @var int[36]
     */
    public $PackCount;
    /**
     * Идентификатор упаковки
     * 
     * @var string[36]
     */
    public $PackRef;
}