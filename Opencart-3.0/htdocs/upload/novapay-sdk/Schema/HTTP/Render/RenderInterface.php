<?php
/**
 * Render interface for HTTP queries.
 * Useful for logging and debugging.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema\HTTP\Render;

/**
 * Render interface for HTTP queries.
 * Useful for logging and debugging.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
interface RenderInterface
{
    /**
     * Renders $target object to string & can be run in Postman, Terminal, etc.
     * 
     * @param RenderableInterface $target Target object which must be rendered.
     * 
     * @return string
     */
    public function render(RenderableInterface $target);
}