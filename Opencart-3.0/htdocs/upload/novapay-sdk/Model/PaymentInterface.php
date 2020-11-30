<?php
/**
 * Payment interface for communicating with frontend.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Model;

/**
 * Payment interface for communicating with frontend.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
interface PaymentInterface
{
    /**
     * Creates new payment in the current session.
     * Every new payment populates session products[] array, 
     * so it works like a shopping cart.
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID.
     * @param array  $products    Order items (products).
     * @param string $amount      Payment amount.
     * @param bool   $use_hold    TRUE when hold must be used, FALSE when direct.
     * @param string $external_id External order ID.
     * 
     * @return bool               TRUE on success, FALSE on failure.
     */
    public function create(
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
    );

    /**
     * Cancels a payment in the current session.
     * POST /void
     * Void paid or holded payments (paid ones can be voided only till 23:59)
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID.
     * 
     * @return bool               TRUE on success, FALSE on failure.
     */
    public function cancel($merchant_id, $session_id);

    /**
     * Completes holded payment.
     * POST /complete-hold
     * Complete holded payments (created with use_hold: true parameter)
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID.
     * @param string $amount      Amount to pay,
     *                            optional parameter for hold partial completion.
     * 
     * @return bool               TRUE on success, FALSE on failure.
     */
    public function complete($merchant_id, $session_id, $amount = null);
}