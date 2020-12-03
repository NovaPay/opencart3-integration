<?php
/**
 * Session POST request schema class.
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

use Novapay\Payment\SDK\Schema\Callback;
use Novapay\Payment\SDK\Schema\Client;
use Novapay\Payment\SDK\Schema\Metadata;

/**
 * Session POST request schema class.
 * The data used in session post method.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class SessionPostRequest extends Request
{
    /**
     * The merchant ID.
     * 
     * @var string $merchant_id Merchant ID.
     */
    public $merchant_id;

    /**
     * The first name of the Client.
     * 
     * @var string $client_first_name First name.
     */
    public $client_first_name;

    /**
     * The last name of the Client.
     * 
     * @var string $client_last_name Last name.
     */
    public $client_last_name;

    /**
     * The middle name of the Client.
     * 
     * @var string $client_patronymic Middle name (patronymic).
     */
    public $client_patronymic;

    /**
     * The phone number of the Client.
     * 
     * @var string $phone Phone number.
     */
    public $client_phone;

    /**
     * The callback URL to handle postback calls.
     * 
     * @var string $callback_url Callback URL.
     */
    public $callback_url;

    /**
     * The metadata object to store order specific data.
     * 
     * @var object $metadata Metadata object.
     */
    public $metadata;

    /**
     * The URL of successfull page when payment is accepted.
     * 
     * @var string $success_url URL of successfull page.
     */
    public $success_url;

    /**
     * The URL of fail page when payment is declined.
     * 
     * @var string $fail_url URL of fail page.
     */
    public $fail_url;

    private $_client;
    private $_metadata;
    private $_callback;

    /**
     * SessionPostRequest constructor.
     * 
     * @param string                      $merchant_id Merchant ID.
     * @param Novapay\Payment\SDK\Schema\Client   $client      Client object.
     * @param Novapay\Payment\SDK\Schema\Metadata $metadata    Metadata object.
     * @param Novapay\Payment\SDK\Schema\Callback $callback    Callback object.
     */
    public function __construct(
        $merchant_id, 
        Client $client = null, 
        Metadata $metadata = null, 
        Callback $callback = null
    ) {
        if (null === $client) {
            $client = new Client();
        }
        if (null === $metadata) {
            $metadata = new Metadata();
        }
        if (null === $callback) {
            $callback = new Callback();
        }
        $this->merchant_id = $merchant_id;
        $this->setClient($client);
        $this->setMeta($metadata);
        $this->setCallback($callback);
    }

    public function setClient(Client $client)
    {
        $this->_client = $client;
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function setMeta(Metadata $meta)
    {
        $this->_metadata = $meta;
    }

    public function getMeta()
    {
        return $this->_metadata;
    }

    public function setCallback(Callback $callback)
    {
        $this->_callback = $callback;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    public function jsonSerialize()
    {
        $client   = $this->getClient();
        $callback = $this->getCallback();
        $meta     = $this->getMeta();
        return [
            'merchant_id'       => $this->merchant_id,
            'client_first_name' => $client->first_name,
            'client_last_name'  => $client->last_name,
            'client_patronymic' => $client->middle_name,
            'client_phone'      => $client->phone,
            'client_email'      => $client->email,
            'callback_url'      => $callback->postback,
            'success_url'       => $callback->success,
            'fail_url'          => $callback->fail,
            'metadata'          => $meta->jsonSerialize()
        ];
    }
}