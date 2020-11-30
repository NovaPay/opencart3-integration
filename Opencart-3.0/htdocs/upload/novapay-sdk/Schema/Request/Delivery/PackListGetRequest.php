<?php
/**
 * Pack list GET request schema class.
 * The data about packaging.
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

namespace Novapay\Payment\SDK\Schema\Request\Delivery;

use Novapay\Payment\SDK\Schema\Request\Request;
use Novapay\Payment\SDK\Schema\Response\Delivery\Dimensions;

/**
 * Pack list GET request schema class.
 * The data about packaging.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/582b1069a0fe4f0298618f06
 */
class PackListGetRequest extends Request
{
    /**
     * The merchant ID.
     * 
     * @var string $merchant_id Merchant ID.
     */
    public $merchant_id;
    public $modelName = 'Common';
    public $calledMethod = 'getPackList';
    public $methodProperties = [];

    // public $Length;
    // public $Width;
    // public $Height;
    // public $VolumetricWeight;
    // public $TypeOfPacking;

    /**
     * CalculationGetRequest constructor.
     * 
     * @param string     $merchant_id   Merchant ID.
     * @param Dimensions $dimensions    Package dimensions.
     * @param string     $typeOfPackage Type of packaged. Not in use.
     */
    public function __construct($merchant_id, Dimensions $dimensions, $typeOfPackage = '') 
    {
        $this->merchant_id = $merchant_id;
        $this->methodProperties = (array) $dimensions;
        $this->methodProperties['TypeOfPacking'] = $typeOfPackage;
    }
}