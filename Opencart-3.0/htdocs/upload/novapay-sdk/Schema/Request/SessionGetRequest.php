<?php
/**
 * Session GET request schema class.
 * The data used in session post method.
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

use Novapay\Payment\SDK\Schema\Schema;
use Novapay\Payment\SDK\Schema\Callback;
use Novapay\Payment\SDK\Schema\Client;
use Novapay\Payment\SDK\Schema\Metadata;

/**
 * Session GET request schema class.
 * The client structure used in payment and session methods.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class SessionGetRequest extends Request
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
     * SessionGetRequest constructor.
     * 
     * @param string $merchant_id Merchant ID.
     * @param string $session_id  Session ID.
     */
    public function __construct($merchant_id, $session_id) {
        $this->merchant_id = $merchant_id;
        $this->session_id  = $session_id;
    }
}