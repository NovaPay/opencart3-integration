<?php
/**
 * Session model class.
 * Processes sessions through API.
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

use Novapay\Payment\SDK\Schema\Callback;
use Novapay\Payment\SDK\Schema\Client;
use Novapay\Payment\SDK\Schema\Metadata;
use Novapay\Payment\SDK\Schema\Request\SessionGetRequest;
use Novapay\Payment\SDK\Schema\Request\SessionPostRequest;
use Novapay\Payment\SDK\Schema\Response\Response;
use Novapay\Payment\SDK\Schema\Response\SessionGetResponse;
use Novapay\Payment\SDK\Schema\Response\SessionPostResponse;

/**
 * Session model class.
 * Processes sessions through API.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Session extends Model implements SessionInterface
{
    // All statuses: created, expired, processing, holded, hold_confirmed, 
    //               processing_hold_completion, paid, failed, 
    //               processing_void, voided
    // 
    // session created
    const STATUS_CREATED   = 'created';
    // session expired, no further actions available
    const STATUS_EXPIRED   = 'expired';
    // session is processing, payer is entering his payment data
    const STATUS_PROCESS   = 'processing';
    // session amount is holded on payer account
    const STATUS_HOLDED    = 'holded';
    // hold is confirmed by seller for secure payment
    const STATUS_CONFIRMED = 'hold_confirmed';
    // hold completition is in process
    const STATUS_COMPLETE  = 'processing_hold_completion'; 
    // session is fully paid
    const STATUS_PAID      = 'paid';
    // session payment failed
    const STATUS_FAILED    = 'failed';
    // session amount voiding is in process
    const STATUS_VOIDING   = 'processing_void';
    // sesion payment voided
    const STATUS_VOIDED    = 'voided';

    /**
     * The session ID.
     *
     * @var string Session ID.
     */
    public $id;
    /**
     * The metadata for the payment.
     * 
     * @var Metadata Metadata object.
     */
    public $metadata;
    /**
     * The session payment status.
     * 
     * @var string Session status.
     */
    public $status;

    /**
     * Creates new session.
     * The new created session is stored in the object.
     * 
     * @param mixed    $merchant_id Mearchant ID.
     * @param Client   $client      Client object with data.
     * @param Metadata $metadata    Metadata object with data.
     * @param Callback $callback    Callback object with data.
     * 
     * @return bool                 TRUE on success, FALSE on failure.
     */
    public function create(
        $merchant_id, 
        Client $client, 
        Metadata $metadata, 
        Callback $callback
    ) {
        // POST /session
        // Creates payment session
        $request = new SessionPostRequest(
            $merchant_id, 
            $client, 
            $metadata, 
            $callback
        );

        $response = $this->send($request, 'POST', '/session');
        $res = Response::create(
            $response[1], 
            $response[0], 
            SessionPostResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof SessionPostResponse) {
            return false;
        }

        $this->id       = $res->id;
        $this->metadata = $res->metadata;

        return true;
    }

    /**
     * Retrieves the session status from the API by it's id.
     * POST /get-status
     * Return current session status
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID, if NULL id of session object is used.
     * 
     * @return bool               TRUE on success, FALSE on failure.
     */
    public function status($merchant_id, $session_id = null)
    {
        if (!$session_id) {
            $session_id = $this->id;
        }
        $request = new SessionGetRequest($merchant_id, $session_id);

        $response = $this->send($request, 'POST', '/get-status');
        $res = Response::create(
            $response[1], 
            $response[0],
            SessionGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof SessionGetResponse) {
            return false;
        }

        $this->id       = $res->id;
        $this->metadata = $res->metadata;
        $this->status   = $res->status;

        return true;
    }

    /**
     * Deletes the session.
     * POST /expire
     * Manually expire created session
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID, if NULL id of session object is used.
     * 
     * @return bool               TRUE on success, FALSE on failure.
     */
    public function expire($merchant_id, $session_id = null)
    {
        if (!$session_id) {
            $session_id = $this->id;
        }
        $request = new SessionDeleteRequest($merchant_id, $session_id);

        $response = $this->send($request, 'POST', '/expire');
        $res = Response::create(
            $response[1], 
            $response[0],
            SessionGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof SessionDeleteResponse) {
            return false;
        }

        $this->id       = $res->id;
        $this->metadata = $res->metadata;
        $this->status   = $res->status;

        return true;
    }

    /**
     * Checks if current session status matches $status.
     * 
     * @param string $status Session status.
     * 
     * @return bool          TRUE if session status is correct, otherwise FALSE.
     */
    public function is($status)
    {
        return $status === $this->status;
    }

    /**
     * Is session able to be canceled.
     * 
     * @return bool TRUE if session is able to be canceled.
     */
    public function isFresh()
    {
        return in_array($this->status, [self::STATUS_CREATED]);
    }

    /**
     * Is session not able to be updated, expired or voided/canceled.
     * 
     * @return bool TRUE if session is not able to be updated.
     */
    public function isDead()
    {
        return in_array(
            $this->status, 
            [
                self::STATUS_EXPIRED, 
                self::STATUS_VOIDED,
                self::STATUS_FAILED
            ]
        );
    }

    /**
     * Checks if session is closed or open.
     * 
     * @return bool TRUE if session is closed, FALSE if open.
     */
    public function isClosed()
    {
        return in_array(
            $this->status,
            [
                self::STATUS_EXPIRED, 
                self::STATUS_VOIDED,
                self::STATUS_FAILED,
                self::STATUS_PAID
            ]
        );
    }

    /**
     * Returns all session statuses.
     * 
     * @return array Statuses.
     */
    public static function getStatuses()
    {
        return [
            // session created
            static::STATUS_CREATED,
            // session expired, no further actions available
            static::STATUS_EXPIRED,
            // session is processing, payer is entering his payment data
            static::STATUS_PROCESS,
            // session amount is holded on payer account
            static::STATUS_HOLDED,
            // hold is confirmed by seller for secure payment
            static::STATUS_CONFIRMED,
            // hold completition is in process
            static::STATUS_COMPLETE,
            // session is fully paid
            static::STATUS_PAID,
            // session payment failed
            static::STATUS_FAILED,
            // session amount voiding is in process
            static::STATUS_VOIDING,
            // sesion payment voided
            static::STATUS_VOIDED,
        ];
   }
}