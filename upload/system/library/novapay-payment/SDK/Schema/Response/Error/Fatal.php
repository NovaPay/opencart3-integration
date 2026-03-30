<?php
/**
 * Fatal error response schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema\Response\Error;

use Novapay\Payment\SDK\Schema\Response\Response;

/**
 * Fatal error response schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Fatal extends Error
{
    public $error;

    /**
     * Creates a fatal error from the basic error.
     * Just for property casting.
     * 
     * @param Error $error Basic error.
     */
    public function __construct(Response $error)
    {
        $this->setHeaders($error->getHeaders());
        $this->setBody($error->getBody());
        foreach ($error as $key => $value) {
            $this->$key = $value;
        }
    }
}