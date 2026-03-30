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
use Novapay\Payment\SDK\Schema\Request\ExportOperationsGetRequest;
use Novapay\Payment\SDK\Schema\Request\PaymentDeleteRequest;
use Novapay\Payment\SDK\Schema\Request\PaymentPostRequest;
use Novapay\Payment\SDK\Schema\Request\PaymentPutRequest;
use Novapay\Payment\SDK\Schema\Request\SessionDeleteRequest;
use Novapay\Payment\SDK\Schema\Response\Response;
use Novapay\Payment\SDK\Schema\Response\PaymentDeleteResponse;
use Novapay\Payment\SDK\Schema\Response\PaymentPostResponse;
use Novapay\Payment\SDK\Schema\Response\PaymentPutResponse;

class ExportOperations extends Model
{
    public $url;

    public function exportOperations(
        $merchant_id,
        $fromData,
        $toDate,
        $format,
        $email
    ) {
        // POST /payment
        // Add payment to created session and optionaly initialize its processing
        $request = new ExportOperationsGetRequest(
            $merchant_id,
            $fromData,
            $toDate,
            $format,
            $email
        );

        $response = $this->send($request, 'POST', '/export-operations');
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
        $request = new SessionDeleteRequest($merchant_id, $session_id);

        $response = $this->send($request, 'POST', '/void-session');
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
