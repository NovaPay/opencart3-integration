<?php
/**
 * Request schema class.
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

use Novapay\Payment\SDK\Schema\Schema;
use Novapay\Payment\SDK\Schema\HTTP\Headers;
use Novapay\Payment\SDK\Schema\HTTP\Render\RenderableInterface;

/**
 * Request schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Request extends Schema implements RenderableInterface
{
    private $_url;
    private $_method;
    private $_head;

    /**
     * Sets the URL of response.
     * 
     * @param string $url Url of response.
     * 
     * @return Request Current instance.
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Set HTTP method of response.
     * 
     * @param string $method HTTP method.
     * 
     * @return Request Current instance.
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Sets the head of the response.
     * 
     * @param Headers $head Head instance.
     * 
     * @return Request Current instance.
     */
    public function setHead(Headers $head)
    {
        $this->_head = $head;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHead()
    {
        if (!$this->_head) {
            return new Headers();
        }
        return $this->_head->getHeaders();
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return (string) $this;
    }

    /**
     * Allows a class to decide how it will react when it is treated like a 
     * string. For example, what echo $obj; will print. This method must 
     * return a string, as otherwise a fatal E_RECOVERABLE_ERROR level error 
     * is emitted.
     * 
     * @return string Object state.
     */
    public function __toString()
    {
        $data = [];

        foreach ($this as $key => $value) {
            if ('_' === substr($key, 0, 1)) {
                continue;
            }
            $data[$key] = $value;
        }
        return sprintf(
            "%s %s", 
            get_class($this),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}