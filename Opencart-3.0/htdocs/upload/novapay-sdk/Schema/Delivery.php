<?php
/**
 * Delivery schema class.
 * The delivery structure used in payment method.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema;

/**
 * Delivery schema class.
 * The delivery structure used in payment method.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Delivery extends Schema
{
    /**
     * The volume weight.
     * Minimum value is 0.0004.
     * 
     * @var float $volume_weight Volume weight.
     */
    public $volume_weight;

    /**
     * The weight in kilograms.
     * Minimum value is 0.1.
     * 
     * @var float $weight Weight in kilograms.
     */
    public $weight;

    /**
     * The ref id of recipient city.
     * 
     * @var string $recipient_city Ref id of recipient city.
     */
    public $recipient_city;

    /**
     * The ref id of recipient warehouse.
     * 
     * @var string $recipient_warehouse Ref id of recipient warehouse.
     */
    public $recipient_warehouse;

    /**
     * Constructor of the Delivery schema.
     * 
     * @param float  $weight        Weight in kilograms.
     * @param float  $volume_weight Volume weight
     * @param string $city          Ref id of recipient city.
     * @param string $warehouse     Ref id of recipient warehouse.
     */
    public function __construct(
        $weight = null, 
        $volume_weight = null, 
        $city = null, 
        $warehouse = null
    ) {
        $this->weight              = floor(floatval($weight) * 1000) / 1000;
        $this->volume_weight       = floor(floatval($volume_weight) * 1000) / 1000;
        $this->recipient_city      = $city;
        $this->recipient_warehouse = $warehouse;
    }
}