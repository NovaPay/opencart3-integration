<?php
/**
 * HTTP response schema class.
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

namespace Novapay\Payment\SDK\Schema\HTTP;

use Novapay\Payment\SDK\Schema\Schema;
use Novapay\Payment\SDK\Schema\HTTP\Headers;

/**
 * HTTP response schema class.
 * The data used in communication with API when returning results.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Response extends Schema
{
    private $_headers;
    private $_body;

    /**
     * HTTP Response constructor.
     * 
     * @param string $body       Response body.
     * @param array  $headers    Headers as string|array.
     * @param bool   $parse_body If TRUE parses body into object.
     */
    public function __construct($body, $headers = [], $parse_body = false)
    {
        if (is_string($headers)) {
            $headers = explode("\n", $headers);
        }
        $this->setHeaders($headers);
        $this->setBody($body, $parse_body);
    }

    /**
     * Sets the headers for current response.
     *
     * @param mixed $headers Headers as array|Headers.
     * 
     * @return void
     */
    public function setHeaders($headers)
    {
        if ($headers instanceof Headers) {
            $this->_headers = $headers;
            return;
        }
        if (!is_array($headers)) {
            throw new \Exception('Headers MUST be array or ' . Headers::class);
        }

        $this->_headers = new Headers();
        if (count($headers)) {
            $this->_headers->parseHeaders($headers);
        }
    }

    /**
     * Returns the HTTP header object.
     * 
     * @return Headers HTTP headers.
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Sets the response's body and/or parse it based on the header content type.
     * 
     * @param string $body       The body as a string.
     * @param bool   $parse_body If TRUE parses the body based on content type,
     *                           if FALSE skips parsing procedure.
     * 
     * @return bool              TRUE on success, FALSE if cannot parse.
     */
    public function setBody($body, $parse_body = false)
    {
        $this->_body = $body;
        if (!$parse_body) {
            return true;
        }
        $header = $this->getHeader('Content-Type');
        if (!$header) {
            return false;
        }
        switch ($tested = strtolower($header->value)) {
        case 'application/json':
            $json = json_decode($body);
            if (null === $json) {
                // empty response
                // it is quite frequent response in this API.
                $this->setValues([]);
            } else {
                $this->setValues($json);
            }
            $this->afterUpdate();
            return true;
            break;
        }

        return false;
    }

    /**
     * Function called right after body is parsed and values are set.
     * 
     * @return void
     */
    protected function afterUpdate()
    {
    }

    /**
     * Returns the HTTP body.
     * 
     * @return string HTTP HTTP body.
     */
    protected function getBody()
    {
        return $this->_body;
    }

    /**
     * Returns the header by it's key if exists and FALSE if does not.
     * 
     * @param string $key Header key.
     * 
     * @return mixed      Header object if found, FALSE otherwise.
     */
    public function getHeader($key)
    {
        return $this->_headers->get($key);
    }

    /**
     * Returns HTTP version.
     *
     * @return string HTTP version.
     */
    public function getVersion()
    {
        return $this->_headers->version;
    }

    /**
     * Returns HTTP status object|code.
     * Object is instanceof Novapay\Payment\SDK\Schema\HTTP\Status.
     * 
     * @param bool $only_code If TRUE returns 
     *
     * @return mixed          HTTP status|code.
     */
    public function getStatus($only_code = false)
    {
        if ($only_code) {
            return $this->_headers->status->code;
        }
        return $this->_headers->status;
    }

    /**
     * Returns data which can be serialized by json_encode(), which is a value 
     * of any type other than a resource.
     * Serializes the object to a value that can be serialized natively by 
     * json_encode().
     * 
     * @return array Data array.
     */
    public function jsonSerialize()
    {
        return parent::jsonSerialize();
        // return [
        //     'version' => $this->_headers->version,
        //     'status'  => $this->_headers->status->jsonSerialize(),
        //     'head'    => $this->_headers->headers,
        //     'body'    => $this->_body,
        //     'fields'  => get_object_vars($this)
        // ];
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
        $data['_version'] = $this->getVersion();
        $data['_status']  = $this->getStatus();

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