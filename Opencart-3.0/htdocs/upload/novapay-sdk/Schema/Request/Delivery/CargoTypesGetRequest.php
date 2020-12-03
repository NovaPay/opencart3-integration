<?php
/**
 * Cargo types GET request schema class.
 * The data about cargo types for delivery section.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/55702571a0fe4f0b64838909
 */

namespace Novapay\Payment\SDK\Schema\Request\Delivery;

use Novapay\Payment\SDK\Schema\Request\Request;

/**
 * Cargo types GET request schema class.
 * The data about cargo types for delivery section.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/55702571a0fe4f0b64838909
 */
class CargoTypesGetRequest extends Request
{
    /**
     * The merchant ID.
     * 
     * @var string $merchant_id Merchant ID.
     */
    public $merchant_id;
    public $modelName = 'Common';
    public $calledMethod = 'getCargoTypes';
    public $methodProperties = [];

    /**
     * WarehousesGetRequest constructor.
     * 
     * @param string $merchant_id Merchant ID.
     */
    public function __construct($merchant_id) 
    {
        $this->merchant_id = $merchant_id;
        // empty object required :D
        $this->methodProperties = (object) [];
    }
}