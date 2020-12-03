<?php
/**
 * Postback POST request schema class.
 * The data used in postback (callback) post method.
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

use Novapay\Payment\SDK\Schema\Product;
use Novapay\Payment\SDK\Schema\Metadata;

/**
 * Postback POST request schema class.
 * The data used in postback (callback) post method.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class PostbackPostRequest extends Request
{
    /**
     * The session ID.
     * 
     * @var string $id Session ID.
     */
    public $id;
    /**
     * The session status.
     * See Novapay\Payment\SDK\Model\Session for all the available statuses.
     * 
     * @var string $status Session status.
     */
    public $status;
    /**
     * Transaction timestamp.
     * 
     * @var string $created_at Timestamp.
     */
    public $created_at;
    /**
     * Additional data passed to server on session creation
     * 
     * @var Novapay\Payment\SDK\Schema\Metadata $metadata Session metadata.
     */
    public $metadata;
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
     * The external order ID.
     * 
     * @var string $external_id External order ID.
     */
    public $external_id;
    /**
     * The payment amount.
     * 
     * @var string $amount Payment amount.
     */
    public $amount;
    /**
     * Array of order items (products Novapay\Payment\SDK\Schema\Product).
     * []<Novapay\Payment\SDK\Schema\Product>
     * 
     * @var array $products Order items (products).
     */
    public $products = [];

    /**
     * PostbackPostRequest constructor.
     * 
     * @param array $data Posted data.
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        foreach ($this->products as $i => $product) {
            if ($product instanceof Product) {
                continue;
            }
            $this->products[$i] = new Product(
                $product['description'],
                $product['price'],
                $product['count']
            );
        }
        if (!$this->metadata instanceof Metadata) {
            $this->metadata = new Metadata($this->metadata);
        }
    }
}