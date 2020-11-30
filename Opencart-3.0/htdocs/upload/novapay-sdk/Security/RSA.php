<?php
/**
 * The RSA digital signature class to create keys, sign and verify data.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Security;

/**
 * The RSA digital signature class to create keys, sign and verify data.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class RSA
{
    /**
     * The data to sign
     *
     * @var mixed
     */
    public $data;

    /**
     * Specifies the type of private key to create.
     * 
     * @var int
     */
    private $_type = OPENSSL_KEYTYPE_RSA;
    /**
     * Amount of bits in coding function.
     *
     * @var int
     */
    private $_bits = 2048;
    /**
     * Algorithm used in coding functions.
     * 
     * @var int
     */
    private $_algorithm = OPENSSL_ALGO_SHA1;

    private $_privateKey;
    private $_privateKeyFile;
    private $_password;
    private $_publicKey;
    private $_publicKeyFile;

    /**
     * The RSA encoding class constructor.
     *
     * @param mixed  $data             The data to sign.
     * @param string $privateKeyOrFile The file path to private key or its value.
     * @param string $publicKeyOrFile  The file path to public key or its value.
     * @param string $password         The private key password.
     * @param mixed  $type             The key type, default is OPENSSL_KEYTYPE_RSA.
     * @param int    $bits             The private key bits, default is 2048.
     * @param mixed  $algorithm        The coding algorithm, 
     *                                 default is OPENSSL_ALGO_SHA256.
     */
    public function __construct(
        $data = null, 
        $privateKeyOrFile = null, 
        $publicKeyOrFile = null, 
        $password = null,
        $type = null, 
        $bits = null, 
        $algorithm = null
    ) {
        $this->data = $data;
        if ('-----BEGIN' === substr($privateKeyOrFile, 0, 10)) {
            $this->setPrivateKey($privateKeyOrFile);
        } else {
            $this->setPrivateKeyFile($privateKeyOrFile);
        }
        if ('-----BEGIN' === substr($publicKeyOrFile, 0, 10)) {
            $this->setPublicKey($publicKeyOrFile);
        } else {
            $this->setPublicKeyFile($publicKeyOrFile);
        }
        if (null !== $password) {
            $this->_password = $password;
        }
        if (null !== $type) {
            $this->_type = $type;
        }
        if (null !== $bits) {
            $this->_bits = $bits;
        }
        if (null !== $algorithm) {
            $this->_algorithm = $algorithm;
        }
    }

    /**
     * Creates the key with the options 
     *      [bits, type] 
     *      and sets 
     *      [privateKey, publicKey].
     * Keys can be used for signing data or verifying 
     *      [sign(), verify()] 
     *      and exporting keys
     *      [exportPrivate(), exportPublic()]
     *
     * @return bool TRUE on success, FALSE on failure.
     */
    public function create()
    {
        $pair = openssl_pkey_new(
            [
                "private_key_bits" => $this->_bits,
                "private_key_type" => $this->_type,
            ]
        );
        openssl_pkey_export($pair, $this->_privateKey);

        $details = openssl_pkey_get_details($pair);
        $this->_publicKey = $details['key'];

        return $this->_privateKey && $this->_publicKey;
    }

    /**
     * Checks the private and public certificates.
     *
     * @return bool TRUE of public key matches private, otherwise FALSE.
     */
    public function check()
    {
        $key = openssl_pkey_get_private($this->_getPrivateKey());
        $details = openssl_pkey_get_details($key);

        $ok = $this->_getPublicKey() === $details['key'];

        openssl_free_key($key);

        return $ok;
    }

    /**
     * Creates a signature for the data inside of object with private key.
     * 
     * @return mixed The signature on success, NULL on failure.
     */
    public function sign()
    {
        $signature = null;
        try {
            $key = openssl_pkey_get_private(
                $this->_getPrivateKey(), 
                $this->_getPassword()
            );
            openssl_sign($this->data, $signature, $key, $this->_algorithm);

            openssl_free_key($key);
        } catch (\Exception $e) {
            throw new \Exception("Cannot sign RSA key: {$e->getMessage()}", 500, $e);
        }
        return $signature;
    }

    /**
     * Creates a signature for the data inside of the object with 
     * private key in base64 encoding.
     *
     * @return string The signature in base64 encoding.
     */
    public function sign64()
    {
        return base64_encode($this->sign());
    }

    /**
     * Verifying the signature with the current object data and public key.
     * 
     * @param string $signature The signature to verify.
     * 
     * @return bool TRUE if signature is valid, otherwise FALSE.
     */
    public function verify($signature)
    {
        $key = openssl_pkey_get_public($this->_getPublicKey());
        $ok = openssl_verify($this->data, $signature, $key, $this->_algorithm);

        openssl_free_key($key);

        return 1 === $ok ? true : (-1 === $ok ? null : false);
    }

    /**
     * Verifying the signature in base64 encoding with the current object 
     * data and public key.
     * 
     * @param string $signature The signature in base64 encoding to verify.
     * 
     * @return bool TRUE if signature is valid, otherwise FALSE.
     */
    public function verify64($signature)
    {
        return $this->verify(base64_decode($signature));
    }

    /**
     * Sets private key value.
     * 
     * @param string $key Private RSA key value.
     * 
     * @return void
     */
    public function setPrivateKey($key)
    {
        $this->_privateKey = $key;
    }

    /**
     * Sets public key value.
     * 
     * @param string $key Public RSA key value.
     * 
     * @return void
     */
    public function setPublicKey($key)
    {
        $this->_publicKey = $key;
    }

    /**
     * Sets private RSA key file path.
     * 
     * @param string $file Key file path.
     * 
     * @return void
     */
    public function setPrivateKeyFile($file)
    {
        $this->_privateKeyFile = $file;
    }

    /**
     * Sets public RSA key file path.
     * 
     * @param string $file Key file path.
     * 
     * @return void
     */
    public function setPublicKeyFile($file)
    {
        $this->_publicKeyFile = $file;
    }

    /**
     * Returns the private key's value.
     *
     * @return string The private key.
     */
    private function _getPrivateKey()
    {
        if (null !== $this->_privateKey) {
            return $this->_privateKey;
        }
        if (null !== $this->_privateKeyFile) {
            $this->_privateKey = file_get_contents($this->_privateKeyFile);
            return $this->_privateKey;
        }
        return false;
    }

    /**
     * Returns the private key's password.
     *
     * @return string Password.
     */
    private function _getPassword()
    {
        return null === $this->_password ? '' : $this->_password;
    }

    /**
     * Returns the public key's value.
     *
     * @return string The public key.
     */
    private function _getPublicKey()
    {
        if (null !== $this->_publicKey) {
            return $this->_publicKey;
        }
        if (null !== $this->_publicKeyFile) {
            $this->_publicKey = file_get_contents($this->_publicKeyFile);
            return $this->_publicKey;
        }
        return false;
    }

    /**
     * Export private key into file.
     * 
     * @param string $file The private key file path.
     * 
     * @return bool TRUE on success, FALSE on failure.
     */
    public function exportPrivate($file = null)
    {
        if (null !== $file) {
            $this->_privateKeyFile = $file;
        }
        if (!$this->_privateKeyFile) {
            return false;
        }
        return file_put_contents($this->_privateKeyFile, $this->_privateKey) > 0;
    }

    /**
     * Export public key into file.
     * 
     * @param string $file The public key file path.
     * 
     * @return bool TRUE on success, FALSE on failure.
     */
    public function exportPublic($file = null)
    {
        if (null !== $file) {
            $this->_publicKeyFile = $file;
        }
        if (!$this->_publicKeyFile) {
            return false;
        }
        return file_put_contents($this->_publicKeyFile, $this->_publicKey) > 0;
    }

    /**
     * Returns the file path to private key.
     * 
     * @return string The file path to private key.
     */
    public function getPrivateKeyFile()
    {
        return $this->_privateKeyFile;
    }

    /**
     * Returns the file path to public key.
     * 
     * @return string The file path to public key.
     */
    public function getPublicKeyFile()
    {
        return $this->_publicKeyFile;
    }

}