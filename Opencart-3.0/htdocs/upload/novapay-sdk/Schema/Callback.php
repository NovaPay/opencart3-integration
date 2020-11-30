<?php
/**
 * Callback schema class.
 * The callback structure storing URL for postback, success and fail pages.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema;

/**
 * Callback schema class.
 * The callback structure storing URL for postback, success and fail pages.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Callback extends Schema
{
    /**
     * The postback URL.
     * 
     * @var string $postback Postback URL.
     */
    public $postback;

    /**
     * The successfull URL.
     * 
     * @var string $success Successfull URL.
     */
    public $success;

    /**
     * The fail URL.
     * 
     * @var string $fail Fail URL.
     */
    public $fail;

    /**
     * Constructor of the Callback schema.
     *
     * @param string $postback_url Postback URL.
     * @param string $success_url  Successfull URL.
     * @param string $fail_url     Fail URL.
     */
    public function __construct($postback_url = null, $success_url = null, $fail_url = null)
    {
        // parent::__construct();
        $this->postback = $postback_url;
        $this->success  = $success_url;
        $this->fail     = $fail_url;
    }
}