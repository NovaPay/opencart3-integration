<?php

/**
 * Model class.
 * Using to make requests through the NovaPay API.
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

use Exception;
use Novapay\Payment\SDK\Schema\Request\Request;
use Novapay\Payment\SDK\Schema\Response\Response;
use Novapay\Payment\SDK\Security\RSA;
use Novapay\Novapay\SDK\Schema\HTTP\Header;

/**
 * Model class.
 * Using to make requests through the NovaPay API.
 *
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Model implements ConfigurationInterface
{
    const HOST_LIVE = 'https://api-ecom.novapay.ua/v1';
    const HOST_TEST = 'https://api-qecom.novapay.ua/v1';

    const MODE_LIVE = 'live';
    const MODE_TEST = 'test';

    private static $_mode;
    // private static $_host;
    private static $_privateKey;
    private static $_privateKeyFile;
    private static $_password;

    private static $_publicKey;
    private static $_publicKeyFile;

    private static $_merchantId;

    private static $_connectTimeout = 10;
    private static $_executeTimeout = 10;

    private static $_tracing = false;
    private static $_log = [];

    private $_headers = [];

    private $_request;
    private $_response;

    public function __construct()
    {
        $this->setHeader('Content-Type', 'application/json');
    }

    /**
     * Renders the public and protected variables into JSON format.
     *
     * @return string The model in JSON format
     */
    protected function render()
    {
        return json_encode($this);
    }

    /**
     * @param Request|null $data
     * @param string $method
     * @param string $uri
     *
     * @return array Header and body in array [string $header, string $body].
     */
    protected function send(Request $data = null, $method = 'GET', $uri = '/')
    {
        $this->setRequest($data);
        if ($this->isTracing()) {
            static::trace($this->toCurl($data, $method, $uri, null, true));
        }

        try {
            list($url, $payload, $signature, $posting) = $this->prepareRequest(
                $data,
                $method,
                $uri,
                true
            );
        } catch (\Exception $e) {
            $err = [];
            do {
                $err[] = sprintf(
                    "%s%s at %d in %s",
                    str_repeat(' ', 2 * count($err)),
                    $e->getMessage(),
                    $e->getLine(),
                    $e->getFile()
                );
                $e = $e->getPrevious();
            } while ($e);
            $err = json_encode(implode("\n", $err), JSON_UNESCAPED_UNICODE);
            return [
                "HTTP/1.1 401 Unauthorised\n" .
                    "Content-Type: application/json",
                "{\"type\":\"fatal\",\"error\":$err}"
            ];
        }

        $this->setHeader('x-sign', $signature);
        $this->setHeader('Content-Length', strlen($payload));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$_connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$_executeTimeout);

        if ($posting) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $this->getHeaders(true)
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $length = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = trim(substr($result, 0, $length));
        $body   = substr($result, $length);
        curl_close($ch);

        return [$header, $body];
    }

    /**
     * Return a curl command which can be used to debug requests.
     *
     * @param Request|null $data    Request data to send.
     * @param string       $method  Request method.
     * @param string       $uri     Request uri.
     * @param array|null   $headers Request headers,
     *                              if not provided used from current model.
     *
     * @return string               Curl command.
     */
    public function toCurl(
        Request $data = null,
        $method = 'GET',
        $uri = '/',
        $headers = null,
        $mustBeSigned = false
    ) {
        list(
            $url, $payload, $signature
        ) = $this->prepareRequest($data, $method, $uri, $mustBeSigned);

        if (is_array($headers)) {
            $text = [];

            foreach ($headers as $key => $value) {
                if ($value instanceof Header) {
                    $text[] = $value;
                    continue;
                }
                $text[] = "$key: $value";
            }

            $headers = $text;
        } else {
            $headers = $this->getHeaders(true);
        }

        $headers[] = 'x-sign: ' . $signature;
        $headers[] = 'Content-Length: ' . strlen($payload);

        if (empty($headers)) {
            $headers = ['X-Novapay: no headers'];
        }
        return sprintf(
            'curl -X %s -H "%s" -d "%s" %s -v',
            $method,
            implode('" -H "', $headers),
            addslashes($payload),
            $url
        );
    }

    protected static function trace($command)
    {
        self::$_log[] = $command;
    }

    public static function getLog()
    {
        return self::$_log;
    }

    public function setHeaders($headers = [])
    {
        $this->_headers = $headers;
    }

    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $value;
    }

    public function getHeaders($as_curl_format = false)
    {
        if (!$as_curl_format) {
            return $this->_headers;
        }
        $result = [];
        foreach ($this->_headers as $key => $value) {
            $result[] = "$key: $value";
        }
        return $result;
    }

    /**
     * Parses headers from the curl response.
     * Headers are separated by new line.
     * Returns
     * @param mixed $string
     *
     * @return [type]
     */
    public function parseHeaders($string)
    {
    }

    /**
     * Returns prepared data for request.
     *
     * @param Request|null $data         Request data.
     * @param string       $method       Request method.
     * @param string       $uri          Request uri.
     * @param bool         $mustBeSigned Sign data on TRUE.
     *
     * @return array [
     *      string $url,
     *      string $payload,
     *      string $signature,
     *      bool $isUpdating
     * ]
     */
    protected function prepareRequest(
        Request $data = null,
        $method = 'GET',
        $uri = '/',
        $mustBeSigned = false
    ) {
        $allowed = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];
        $updated = ['POST', 'PUT', 'PATCH'];
        if (!in_array($method, $allowed)) {
            throw new \Exception("Method [$method] is not allowed");
            // return ['', ''];
        }
        $signature = null;
        $url = static::getHost() . $uri;
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($mustBeSigned) {
            try {
                $signature = $this->sign($payload);
            } catch (\Exception $e) {
                throw new \Exception("Signing data error: {$e->getMessage()}", 500, $e);
            }
        }

        return [$url, $payload, $signature, in_array($method, $updated)];
    }

    /**
     * Sets the last request for current model.
     *
     * @param Request|null $request Last request.
     *
     * @return void
     */
    protected function setRequest(Request $request = null)
    {
        $this->_request = $request;
    }

    /**
     * Sets the last response for current model.
     *
     * @param Response|null $response Last response.
     *
     * @return void
     */
    protected function setResponse(Response $response = null)
    {
        $this->_response = $response;
    }

    /**
     * Returns last request in current model.
     *
     * @return Request The last request.
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Returns last response in current model.
     *
     * @return Response The last response.
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Signs the data and return a signature in base64 format.
     *
     * @param mixed $payload Data to sign.
     *
     * @return string        The signature.
     */
    public function sign($payload)
    {
        $key = new RSA(
            $payload,
            static::getPrivateKey()
                ? static::getPrivateKey()
                : static::getPrivateKeyFile(),
            static::getPublicKey()
                ? static::getPublicKey()
                : static::getPublicKeyFile(),
            static::getPassword()
        );
        return base64_encode($key->sign());
    }

    /**
     * Enables live, sets mode to 'live'.
     *
     * @return void
     */
    public static function enableLiveMode()
    {
        static::$_mode = self::MODE_LIVE;
    }

    /**
     * Disables live, sets mode to 'test'.
     *
     * @return void
     */
    public static function disableLiveMode()
    {
        static::$_mode = self::MODE_TEST;
    }

    /**
     * Returns URL of base endpoint no slash / at the end.
     *
     * @return string URL of base endpoint.
     */
    public static function getHost()
    {
        return self::MODE_LIVE === self::$_mode
            ? self::HOST_LIVE
            : self::HOST_TEST;
    }

    /**
     * Sets the private key value.
     *
     * @param string $key Private key.
     *
     * @return void
     */
    public static function setPrivateKey($key)
    {
        self::$_privateKey = $key;
    }

    /**
     * Sets the public key value.
     *
     * @param string $key Public key.
     *
     * @return void
     */
    public static function setPublicKey($key)
    {
        self::$_publicKey = $key;
    }

    public static function getPrivateKey()
    {
        return self::$_privateKey;
    }

    public static function getPublicKey()
    {
        return self::$_publicKey;
    }

    /**
     * Sets the password for the private key.
     *
     * @param string $pwd Password.
     *
     * @return void
     */
    public static function setPassword($pwd)
    {
        self::$_password = $pwd;
    }

    /**
     * The password for the private key.
     *
     * @return string Password.
     */
    protected static function getPassword()
    {
        return self::$_password;
    }

    /**
     * Sets the file path to a private key.
     *
     * @param string $file The file path to a private key.
     *
     * @return void
     */
    public static function setPrivateKeyFile($file)
    {
        self::$_privateKeyFile = $file;
    }

    /**
     * Returns the file path to private key.
     *
     * @return string The file path to private key.
     */
    public static function getPrivateKeyFile()
    {
        return self::$_privateKeyFile;
    }

    /**
     * Sets the file path to public key.
     *
     * @param string $file The file path to public key.
     *
     * @return void
     */
    public static function setPublicKeyFile($file)
    {
        self::$_publicKeyFile = $file;
    }

    /**
     * Returns the file path to public key.
     *
     * @return string The file path to public key.
     */
    public static function getPublicKeyFile()
    {
        return self::$_publicKeyFile;
    }

    /**
     * Sets the merchant id.
     *
     * @param string $id Merchant id
     *
     * @return void
     */
    public static function setMerchantId($id)
    {
        static::$_merchantId = $id;
    }

    /**
     * Returns merchant id.
     *
     * @return string Merchant id.
     */
    public static function getMerchantId()
    {
        return static::$_merchantId;
    }

    protected static function isTracing()
    {
        return (bool) self::$_tracing;
    }

    public static function enableTracing()
    {
        self::$_tracing = true;
    }

    public static function disableTracing()
    {
        self::$_tracing = false;
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
