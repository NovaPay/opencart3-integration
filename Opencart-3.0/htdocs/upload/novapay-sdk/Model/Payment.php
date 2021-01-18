<?php
/**
 * Payment model class.
 * Processes payments through API.
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

use Novapay\Payment\SDK\Schema\Delivery;
use Novapay\Payment\SDK\Schema\Request\PaymentDeleteRequest;
use Novapay\Payment\SDK\Schema\Request\PaymentPostRequest;
use Novapay\Payment\SDK\Schema\Request\PaymentPutRequest;
use Novapay\Payment\SDK\Schema\Response\Response;
use Novapay\Payment\SDK\Schema\Response\PaymentDeleteResponse;
use Novapay\Payment\SDK\Schema\Response\PaymentPostResponse;
use Novapay\Payment\SDK\Schema\Response\PaymentPutResponse;

/**
 * Payment model class.
 * Processes payments through API.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Payment extends Model implements PaymentInterface
{
    /**
     * Payment processing URL to redirect user there.
     * 
     * @var string Processing URL.
     */
    public $url;

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
        $external_id = null,
        Delivery $delivery = null
    ) {
        // POST /payment
        // Add payment to created session and optionaly initialize its processing
        $request = new PaymentPostRequest(
            $merchant_id, 
            $session_id,
            $products,
            $amount,
            $use_hold,
            $external_id,
            $delivery
        );

        $response = $this->send($request, 'POST', '/payment');
        $res = Response::create(
            $response[1], 
            $response[0], 
            PaymentPostResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof PaymentPostResponse) {
            return false;
        }

        $this->url = $res->url;

        return true;
    }

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
    public function cancel($merchant_id, $session_id)
    {
        $request = new PaymentDeleteRequest($merchant_id, $session_id);

        $response = $this->send($request, 'POST', '/void');
        $res = Response::create(
            $response[1], 
            $response[0], 
            PaymentDeleteResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof PaymentDeleteResponse) {
            return false;
        }

        $this->url = '';

        return true;
    }

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
    public function complete($merchant_id, $session_id, $amount = null)
    {
        $request = new PaymentPutRequest($merchant_id, $session_id, $amount);

        $response = $this->send($request, 'POST', '/complete-hold');
        $res = Response::create(
            $response[1], 
            $response[0], 
            PaymentPutResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof PaymentPutResponse) {
            return false;
        }

        $this->url = '';

        return true;
    }
}
