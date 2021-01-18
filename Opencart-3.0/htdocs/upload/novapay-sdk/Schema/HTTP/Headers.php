<?php
/**
 * Headers schema class.
 * Stores information about each HTTP header.
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
 * Headers schema class.
 * Stores information about each HTTP header.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Headers extends Schema
{
    public $version;
    public $status;
    public $headers = [];

    /**
     * Headers class constructor.
     * 
     * @param int    $code    Status code.
     * @param string $message Status message.
     * @param array  $headers HTTP headers.
     * @param string $version HTTP version.
     */
    public function __construct($code = 900, $message = '<message>', $headers = [], $version = '1.1')
    {
        $this->version = $version;
        $this->status = new Status($code, $message);
        if (is_string($headers)) {
            $headers = explode("\n", $headers);
        }
        if (is_array($headers) && count($headers)) {
            $this->parseHeaders($headers);
        }
    }

    /**
     * Parse headers array into objects.
     * Possible headers formats:
     *   ["Content-Type: text/html", "Connection: close"]
     *   ["Content-Type" => "text/html", "Connection" => "close"]
     * 
     * @param array $headers Array of headers.
     * 
     * @return array         Array of Header objects.
     */
    public function parseHeaders(array $headers)
    {
        $result = [];

        // check if the array in format
        // isNumeric = true
        // ["HTTP/1.1 200 OK", "Content-Type: text/html", "Connection: close"]
        // or isNumeric = false
        // ["Content-Type" => "text/html", "Connection" => "close"]
        $isNumeric = true;
        foreach (array_keys($headers) as $key) {
            if (!is_numeric($key)) {
                $isNumeric = false;
                break;
            }
        }
        if ($isNumeric) {
            $result = $this->_loadPlainHeaders($headers);
        } else {
            foreach ($headers as $key => $value) {
                $header = new Header($key, $value, count($result));
                $result[$header->getKey()] = $header;
            }
        }
        $this->headers = $result;
        return $result;
    }

    /**
     * Load plain headers array into objects.
     * Possible headers formats:
     *   ["HTTP/1.1 200 OK", "Content-Type: text/html", "Connection: close"]
     * In case of "HTTP/1.1 200 OK" header sets the version and 
     * status (code, message) for the current object.
     * 
     * @param array $headers Array of headers.
     * 
     * @return array         Array of Header objects.
     */
    private function _loadPlainHeaders(array $headers)
    {
        $result = [];
        foreach ($headers as $line) {
            $arr = explode(':', $line, 2);
            if (1 === count($arr)) {
                // HTTP/<version> <code> <message>
                if (preg_match('/^([^\/]+)\/(\S+) (\d+) (.*)$/', $arr[0], $matches)) {
                    $this->version = "{$matches[1]}/{$matches[2]}";
                    $this->status  = new Status($matches[3], $matches[4]);
                } else if ('' === $line) {
                    // empty header
                } else {
                    throw new \Exception("Incorrect header version: {$arr[0]}");
                }
            } else if (2 === count($arr)) {
                // <key>:<value>
                $header = new Header($arr[0], $arr[1], count($result));
                $result[$header->getKey()] = $header;
            }
        }
        return $result;
    }

    /**
     * Returns the header object|value by it's key if exists and FALSE if does not.
     * 
     * @param string $key       Header key.
     * @param bool   $valueOnly If TRUE returns only header value, otherwise object.
     * 
     * @return mixed      Header object|value if found, FALSE otherwise.
     */
    public function get($key, $valueOnly = false)
    {
        return 
            array_key_exists($key, $this->headers) 
            ? ($valueOnly ? $this->headers[$key]->value : $this->headers[$key])
            : false;
    }

    /**
     * Returns headers as array of ["$key: $value"]
     * 
     * @return array Array of headers.
     */
    public function getHeaders()
    {
        $result = [];
        foreach ($this->headers as $value) {
            $result[] = (string) $value;
        }
        return $result;
    }
}