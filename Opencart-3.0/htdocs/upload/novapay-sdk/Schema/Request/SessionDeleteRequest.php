<?php
/**
 * Session DELETE request schema class.
 * The data used in session delete method.
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
 * Session DELETE request schema class.
 * The data used in session delete method.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class SessionDeleteRequest extends Request
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
     * SessionDeleteRequest constructor.
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