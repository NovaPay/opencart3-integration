<?php
/**
 * Renderable interface for targets in HTTP queries.
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

use Novapay\Payment\SDK\Schema\HTTP\Headers;

/**
 * Renderable interface for targets in HTTP queries.
 * Useful for logging and debugging.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
interface RenderableInterface
{
    /**
     * Returns URL (endpoint).
     * 
     * @return string Url.
     */
    public function getUrl();

    /**
     * Returns HTTP method used.
     * Such as GET, POST, PUT, PATCH, DELETE.
     * 
     * @return string HTTP method.
     */
    public function getMethod();

    /**
     * Returns headers object with HTTP headers and other info.
     * 
     * @return Novapay\Payment\SDK\Schema\HTTP\Headers Headers object.
     */
    public function getHead();

    /**
     * Returns body as a string.
     * 
     * @return string Body string.
     */
    public function getBody();
}