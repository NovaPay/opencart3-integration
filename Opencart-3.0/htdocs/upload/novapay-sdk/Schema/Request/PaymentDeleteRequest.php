<?php

/**
 * Payment DELETE request schema class.
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
 * Payment DELETE request schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class PaymentDeleteRequest extends Request
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
     * PaymentDeleteRequest constructor.
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
        $session_id
    ) {
        $this->merchant_id = trim($merchant_id);
        $this->session_id  = trim($session_id);
    }
}
