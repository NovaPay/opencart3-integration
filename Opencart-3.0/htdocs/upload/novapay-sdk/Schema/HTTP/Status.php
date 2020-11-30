<?php
/**
 * Status schema class.
 * Stores information about each HTTP status.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema\HTTP;

use Novapay\Payment\SDK\Schema\Schema;

/**
 * Status schema class.
 * Stores information about each HTTP status.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Status extends Schema
{
    public $code;
    public $message;

    /**
     * Status class constructore.
     * 
     * @param int    $code    Status code.
     * @param string $message Status message.
     */
    public function __construct($code, $message = '')
    {
        $this->code    = intval($code);
        $this->message = trim($message);
    }
}