<?php
/**
 * Calculation GET request schema class.
 * The data about delivery document calculations.
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

namespace Novapay\Payment\SDK\Schema\Request\Delivery;

use Novapay\Payment\SDK\Model\Delivery\Document;
use Novapay\Payment\SDK\Schema\Request\Request;

/**
 * Calculation GET request schema class.
 * The data about delivery document calculations.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702ee2a0fe4f0cf4fc53ef
 */
class CalculationGetRequest extends Request
{
    /**
     * The merchant ID.
     * 
     * @var string $merchant_id Merchant ID.
     */
    public $merchant_id;
    public $modelName = 'InternetDocument';
    public $calledMethod = 'getDocumentPrice';
    public $methodProperties = [];

    // public $CitySender;
    // public $CityRecipient;
    // public $Weight;
    // public $ServiceType;
    // public $Cost;
    // public $CargoType;
    // public $SeatsAmount;
    // public $RedeliveryCalculate;
    // public $PackCount;
    // public $PackRef;
    // public $Amount;
    // public $CargoDetails;

    /**
     * CalculationGetRequest constructor.
     * 
     * @param string   $merchant_id Merchant ID.
     * @param Document $document    Delivery document.
     */
    public function __construct($merchant_id, Document $document) 
    {
        $this->merchant_id = $merchant_id;
        $this->methodProperties = $document;
    }
}