<?php
/**
 * Validation error response schema class.
 * Stores detailed information about the validation error.
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
 * Validation error response schema class.
 * Stores detailed information about the validation error.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Validation extends Error
{
    /**
     * Initializes a message returned from the body.
     * 
     * @return void
     */
    protected function initMessage($message = null)
    {
        // @todo use i18n
        $this->message = 'errors property not defined';
        if (!property_exists($this, 'errors')) {
            return;
        }
        $this->message = "Invalid fields:\n";
        foreach ($this->errors as $err) {
            if (!property_exists($err, 'message')) {
                $this->message .= "- message field of errors.error not defined\n";
                continue;
            }
            $this->message .= "- {$err->message}\n";
        }
        $this->message = rtrim($this->message);
    }
}