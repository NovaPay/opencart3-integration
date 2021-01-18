<?php
/**
 * Header schema class.
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
 * Header schema class.
 * Stores information about each HTTP header.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Header extends Schema
{
    public $key;
    public $value;
    public $offset;
    public $options = [];

    /**
     * Header class constructore.
     * 
     * @param string $key    Header key.
     * @param string $value  Header value.
     * @param int    $offset Header position.
     */
    public function __construct($key, $value = '', $offset = -1)
    {
        $this->key    = trim($key);
        $this->parseValue($value);
        $this->offset = intval($offset);
    }

    /**
     * Parses header raw value into value and options.
     * 
     * @param string $raw_value Header raw value.
     * 
     * @return void
     */
    protected function parseValue($raw_value)
    {
        // @todo take care about quotes > "sdf==;"; max-length=1024
        $values = explode(';', $raw_value);
        $this->value = trim(array_shift($values));

        $this->options = [];
        foreach ($values as $value) {
            $arr = explode('=', $value, 2);
            $key = ucwords(trim($arr[0]));
            if (empty($key)) {
                continue;
            }
            $this->options[$key] = trim($arr[isset($arr[1]) ? 1 : 0], "\"\t\n ");
        }
    }

    /**
     * Returns option value.
     *
     * @param string $name Option name.
     *
     * @return string|null Option value if exists, NULL otherwise.
     */
    public function getOption($name)
    {
        return $this->options[$name] ?? null;
    }

    /**
     * Returns header key in title case format.
     * 
     * @return string Header key.
     */
    public function getKey()
    {
        return ucwords($this->key, "-");
    }

    /**
     * Returns header value with options.
     * 
     * @return string Header value.
     */
    public function getValue()
    {
        if (!count($this->options)) {
            return $this->value;
        }

        $options = [];
        foreach ($this->options as $key => $value) {
            $options[] = "$key=$value";
        }
        return $this->value . '; ' . implode('; ', $options);
    }

    /**
     * Returns header content.
     * Content-Type: application/json; charset=utf8
     * 
     * @return string Header content.
     */
    public function __toString()
    {
        $result = [];
        $result[] = $this->key . ': ' . $this->value;
        foreach ($this->options as $key => $value) {
            $result[] = "$key=$value";
        }
        return implode('; ', $result);
    }
}