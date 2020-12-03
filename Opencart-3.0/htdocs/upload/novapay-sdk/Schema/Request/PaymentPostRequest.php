<?php

/**
 * Payment POST request schema class.
 * The data used in payment post method.
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

// @deprecated
// use Novapay\Payment\SDK\Schema\Delivery;

/**
 * Payment POST request schema class.
 * The data used in payment post method.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class PaymentPostRequest extends Request
{
    /**
     * The merchant ID.
     * 
     * @var string $merchant_id Merchant ID.
     */
    public $merchant_id;
    /**
     * The session ID.
     * 
     * @var string $session_id Session ID.
     */
    public $session_id;
    /**
     * The external order ID.
     * 
     * @var string $external_id External order ID.
     */
    public $external_id;
    /**
     * The payment amount.
     * 
     * @var string $amount Payment amount.
     */
    public $amount;
    /**
     * Array of order items (products).
     * 
     * @var array $products Order items (products).
     */
    public $products = [];
    /**
     * Use hold or direct payment. 
     * TRUE when hold must be used, FALSE when direct.
     * FALSE is the default value.
     * 
     * @var bool $use_hold TRUE when hold must be used, FALSE when direct.
     */
    public $use_hold = false;
    // @deprecated
    // public $delivery;
    // @deprecated
    // public $identifier;

    /**
     * PaymentPostRequest constructor.
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID.
     * @param array  $products    Order items (products).
     * @param string $amount      Payment amount.
     * @param bool   $use_hold    TRUE when hold must be used, FALSE when direct.
     * @param string $external_id External order ID.
     */
    public function __construct(
        $merchant_id,
        $session_id,
        array $products,
        $amount,
        $use_hold = false,
        $external_id = null
        // @deprecated
        // Delivery $delivery,
        // @deprecated
        // $identifier = null
    ) {
        $this->merchant_id = trim($merchant_id);
        $this->session_id  = trim($session_id);
        $this->products    = $products;
        $this->amount      = trim($amount);
        $this->use_hold    = (bool) $use_hold;
        $this->external_id = trim($external_id);
        // @deprecated
        // $this->delivery    = $delivery;
        // @deprecated
        // $this->identifier  = $identifier;
    }
}
