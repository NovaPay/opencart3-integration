<?php
/**
 * Delivery price GET request schema class.
 * Retrieves calculated delivery price.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema\Request;

/**
 * Delivery price GET request schema class.
 * Retrieves calculated delivery price.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class DeliveryPriceGetRequest extends Request
{
    /**
     * The merchant ID.
     * 
     * @var string $merchant_id Merchant ID.
     */
    public $merchant_id;
    public $amount;
    public $volume_weight;
    public $weight;
    public $recipient_city;
    public $recipient_warehouse;

    /**
     * SessionGetRequest constructor.
     * 
     * @param string $merchant_id      Merchant ID.
     * @param float  $total            Total amount of the package.
     * @param float  $volumetricWeight Volumetric weight of the package.
     * @param float  $weight           Weight of the package in kilograms.
     * @param string $city             City reference id.
     * @param string $warehouse        Warehouse reference id.
     */
    public function __construct(
        $merchant_id, 
        $total, 
        $volumetricWeight, 
        $weight, 
        $city, 
        $warehouse
    ) {
        $this->merchant_id         = $merchant_id;
        $this->amount              = $total;
        $this->volume_weight       = $volumetricWeight;
        $this->weight              = $weight;
        $this->recipient_city      = $city;
        $this->recipient_warehouse = $warehouse;
    }
}