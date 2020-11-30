<?php
/**
 * Processing error response schema class.
 * Stores detailed information about the processing error.
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

/**
 * Processing error response schema class.
 * Stores detailed information about the processing error.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Processing extends Error
{
    /**
     * Initializes a message returned from the body.
     * 
     * @return void
     */
    protected function initMessage($message = null)
    {
        // @todo use i18n
        $this->message = property_exists($this, 'error') 
            ? $this->error 
            : 'error property not defined';
    }
}