<?php
/**
 * Dimensions schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/582b1069a0fe4f0298618f06
 */

namespace Novapay\Payment\SDK\Schema\Response\Delivery;

use Novapay\Payment\SDK\Schema\Schema;

/**
 * Dimensions schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/582b1069a0fe4f0298618f06
 */
class Dimensions extends Schema
{
    /**
     * Длина упаковки
     * 
     * @var string[36]
     */
    public $Length;
    /**
     * Ширина упаковки
     * 
     * @var string[36]
     */
    public $Width;
    /**
     * Высота упаковки
     * 
     * @var string[36]
     */
    public $Height;
    /**
     * Объем упаковки
     * 
     * @var string[36]
     */
    public $VolumetricWeight;
}