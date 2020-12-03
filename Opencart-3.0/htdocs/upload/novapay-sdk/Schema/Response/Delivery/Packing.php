<?php
/**
 * Packing schema class.
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
 * Packing schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/582b1069a0fe4f0298618f06
 */
class Packing extends Schema
{
    /**
     * Идентификатор упаковки string[36]
     */
    public $Ref;
    /**
     * Описание ну Украинском языке string[36]
     */
    public $Description;
    /**
     * Описание ну русском языке string[36]
     */
    public $DescriptionRu;
    /**
     * Длинна упаковки string[36]
     */
    public $Length;
    /**
     * Вес упаковки string[36]
     */
    public $Width;
    /**
     * Высота упаковки string[36]
     */
    public $Height;
    /**
     * Объем упаковки string[36]
     */
    public $VolumetricWeight;
    /**
     * Тип упаковки (не используется) string[36]
     */
    public $TypeOfPacking;
}