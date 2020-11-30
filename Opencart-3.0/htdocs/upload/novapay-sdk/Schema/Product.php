<?php
/**
 * Product schema class.
 * The product structure used in payment method.
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
 * Product schema class.
 * The product structure used in payment method.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Product extends Schema
{
    /**
     * The product description/title.
     * 
     * @var string $description Product description.
     */
    public $description;

    /**
     * Payment item total price.
     * 
     * @var string $price Item total price.
     */
    public $price;

    /**
     * The payment item quantity.
     * 
     * @var string $count Item quantity.
     */
    public $count;

    /**
     * Constructor of the Delivery schema.
     * 
     * @param string $title The description/title.
     * @param mixed  $price The total price.
     * @param mixed  $count The quantity.
     */
    public function __construct($title = null, $price = null, $count = null) 
    {
        $this->description = $title;
        $this->price       = sprintf('%.2f', floatval(trim($price)));
        $this->count       = trim($count);
    }
}