<?php
/**
 * Delivery confirm hold POST request schema class.
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
 * Delivery confirm hold POST request schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class DeliveryConfirmPostRequest extends Request
{
    /**
     * The merchant ID.
     * 
     * @var string $merchant_id Merchant ID.
     */
    public $merchant_id;
    public $session_id;

    /**
     * DeliveryConfirmPostRequest constructor.
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID.
     */
    public function __construct($merchant_id, $session_id)
    {
        $this->merchant_id = trim($merchant_id);
        $this->session_id  = trim($session_id);
    }
}