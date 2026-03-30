<?php
/**
 * Curl render class.
 * Useful for logging and debugging with curl command.
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
 * Curl render class.
 * Useful for logging and debugging with curl command.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Curl implements RenderInterface
{
    /**
     * Renders $target object to string & can be run in Postman, Terminal, etc.
     * 
     * @param RenderableInterface $target Target object which must be rendered.
     * 
     * @return string
     */
    public function render(RenderableInterface $target)
    {
        return sprintf(
            'curl -X %s -H "%s" -d "%s" %s -v',
            $target->getMethod(),
            implode('" -H "', $target->getHead()->getHeaders()),
            addslashes($target->getBody()),
            $target->getUrl()
        );
    }
}