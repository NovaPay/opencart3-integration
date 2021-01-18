<?php
/**
 * Schema class.
 * Contains information in the specific structure used in Models.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema;

/**
 * Schema class.
 * Contains information sent in Requests & Responses.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Schema implements \JsonSerializable
{
    /**
     * Specify data which should be serialized to JSON.
     *
     * @return mixed The structured data for JSON serialization.
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * Sets the object properties.
     * 
     * @param mixed $values      An array or object of properties.
     * @param bool  $onlyDefined If TRUE sets only values for defined properties,
     *                           otherwise create new properties and set values.
     * 
     * @return void
     */
    public function setValues($values, $onlyDefined = false)
    {
        if (!is_array($values) && !is_object($values)) {
            throw new \Exception('Values MUST be array|object');
        }
        foreach ($values as $key => $value) {
            $key = trim($key);
            if ($onlyDefined && !property_exists($this, $key)) {
                continue;
            }
            $this->$key = $value;
        }
    }
}