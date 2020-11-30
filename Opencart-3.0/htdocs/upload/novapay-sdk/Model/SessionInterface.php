<?php
/**
 * Session interface for communicating with frontend.
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

/**
 * Session interface for communicating with frontend.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
interface SessionInterface
{
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
    );

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
    public function status($merchant_id, $session_id = null);

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
    public function expire($merchant_id, $session_id = null);

    /**
     * Checks if current session status matches $status.
     * 
     * @param string $status Session status.
     * 
     * @return bool          TRUE if session status is correct, otherwise FALSE.
     */
    public function is($status);

    /**
     * Is session able to be canceled.
     * 
     * @return bool TRUE if session is able to be canceled.
     */
    public function isFresh();

    /**
     * Is session not able to be updated, expired or voided/canceled.
     * 
     * @return bool TRUE if session is not able to be updated.
     */
    public function isDead();
}