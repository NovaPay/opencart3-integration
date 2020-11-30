<?php
/**
 * Metadata schema class.
 * Storing specific data for order.
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
 * Metadata schema class.
 * Storing specific data for order.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Metadata extends Schema
{
    private $_meta = [];

    /**
     * Constructor of the Metadata schema.
     *
     * @param mixed $meta Metadata.
     */
    public function __construct($meta = null)
    {
        if (null !== $meta) {
            $this->set($meta);
        }
    }

    /**
     * Sets data to current metadata object.
     * Data can be object or assicated array and will be converted into 
     * associated array.
     * 
     * @param mixed $meta Metadata
     * 
     * @return boolean    TRUE if successfully set, FALSE otherwise.
     */
    public function set($meta)
    {
        if (is_object($meta)) {
            $meta = (array) $meta;
        }
        if (!is_array($meta)) {
            return false;
        }

        $this->_meta = $meta;

        return true;
    }

    /**
     * Sets the specific value of metadata by key.
     * 
     * @param mixed $key   Key of the value.
     * @param mixed $value Value in any form.
     * 
     * @return void
     */
    public function value($key, $value = null)
    {
        $this->_meta[$key] = $value;
    }

    /**
     * Removes a value from metadata object.
     *
     * @param mixed $key Key of the value.
     * 
     * @return boolean   TRUE if value existed and deleted, FALSE otherwise.
     */
    public function delete($key)
    {
        if (array_key_exists($key, $this->_meta)) {
            unset($this->_meta[$key]);
            return true;
        }
        return false;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return mixed The structured data for JSON serialization.
     */
    public function jsonSerialize()
    {
        return $this->_meta;
    }

    /**
     * Returns metadata value by it's key.
     * 
     * @param mixed $name Metadata key.
     * 
     * @return mixed      Medadata value or NULL if undefined.
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->_meta)
            ? $this->_meta[$name] : null;
    }
}