<?php
/**
 * Basic response schema class.
 * The data used in communication with API when returning results.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema\Response;

use Novapay\Payment\SDK\Schema\HTTP\Render\RenderableInterface;
use Novapay\Payment\SDK\Schema\HTTP\Response as HTTPResponse;
use Novapay\Payment\SDK\Schema\Response\Error\Error;

/**
 * Basic response schema class.
 * The data used in communication with API when returning results.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Response extends HTTPResponse implements RenderableInterface
{
    private $_url;
    private $_method;

    /**
     * Sets the headers for current response.
     *
     * @param mixed $headers Headers as array|Headers.
     * 
     * @return void
     */
    public function setHeaders($headers)
    {
        parent::setHeaders($headers);

        $this->getVersion();
    }

    /**
     * Creates a response specific object based on the HTTP response.
     * 
     * @param string $body           HTTP body.
     * @param array  $headers        HTTP headers.
     * @param string $expected_class Expected Response class.
     * 
     * @return Response              The response object.
     */
    public static function create(
        $body, 
        $headers = [], 
        $expected_class = null
    ) {
        $me = new Response($body, $headers, true);
        $status = $me->getStatus(true);
        $first  = intval(substr($status, 0, 1));
        switch ($first) {
        case 2:
            if (!$expected_class || !class_exists($expected_class)) {
                return $me;
            }
            $response = new $expected_class($body, $headers);
            // $response->setHeaders($me->getHeaders());
            // $response->setBody($me->getBody());
            foreach ($me as $key => $value) {
                $response->$key = $value;
            }
            $response->afterUpdate();
            return $response;
        case 4: // 4XX
            return Error::create($me);
        }

        return $me;
    }

    /**
     * Sets the URL of response.
     * 
     * @param string $url Url of response.
     * 
     * @return Response Current instance.
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
     * @return Response Current instance.
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
     * {@inheritDoc}
     */
    public function getHead()
    {
        return $this->getHeaders();
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return parent::getBody();
    }
}