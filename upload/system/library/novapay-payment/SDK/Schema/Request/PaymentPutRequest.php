<?php

/**
 * Payment PUT request schema class.
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
 * Payment PUT request schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class PaymentPutRequest extends Request
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
     * The amount to pay for holded payment.
     * 
     * @var string Amount to pay.
     */
    public $amount;

    /**
     * PaymentPutRequest constructor.
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID.
     * @param string $amount      Amount to pay,
     *                            optional parameter for hold partial completion.
     */
    public function __construct(
        $merchant_id,
        $session_id,
        $amount = null
    ) {
        $this->merchant_id = trim($merchant_id);
        $this->session_id  = trim($session_id);
        $this->amount      = $amount ? $this->fixAmount($amount) : null;
    }

    /**
     * Replaces wrong characters in the amount to make it float
     * 
     * @param mixed $amount Amount with any characters.
     * 
     * @return float        Fixed amount
     */
    protected function fixAmount($amount)
    {
        // remove all symbols accept [comma, dot, digit]
        $result = preg_replace('/[^\d\.,]+/', '', $amount);
        // replace comma with dot
        $result = str_replace(',', '.', $result);
        return (float) $result;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return mixed The structured data for JSON serialization.
     */
    public function jsonSerialize()
    {
        $data = get_object_vars($this);
        if (null === $this->amount) {
            unset($data['amount']);
        }
        return $data;
    }
}
