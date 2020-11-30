<?php
/**
 * Configuration interface for communicating with frontend.
 * Manages configurable data for requests.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Model;

/**
 * Configuration interface for communicating with frontend.
 * Manages configurable data for requests.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
interface ConfigurationInterface
{
    /**
     * Enables live, sets mode to 'live'.
     * 
     * @return void
     */
    public static function enableLiveMode();

    /**
     * Disables live, sets mode to 'test'.
     * 
     * @return void
     */
    public static function disableLiveMode();

    /**
     * Sets the private key value.
     * 
     * @param string $key Private key.
     * 
     * @return void
     */
    public static function setPrivateKey($key);

    /**
     * Sets the file path to a private key.
     * 
     * @param string $file The file path to a private key.
     * 
     * @return void
     */
    public static function setPrivateKeyFile($file);

    /**
     * Sets the password for the private key.
     * 
     * @param string $pwd Password.
     * 
     * @return void
     */
    public static function setPassword($pwd);

    /**
     * Sets the public key value.
     * 
     * @param string $key Public key.
     * 
     * @return void
     */
    public static function setPublicKey($key);

    /**
     * Sets the file path to a public key.
     * 
     * @param string $file The file path to a public key.
     * 
     * @return void
     */
    public static function setPublicKeyFile($file);
}