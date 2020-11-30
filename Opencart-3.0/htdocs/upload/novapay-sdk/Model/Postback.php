<?php
/**
 * Postback model class.
 * Processes postback request from API.
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

use Novapay\Payment\SDK\Model\Session;
use Novapay\Payment\SDK\Schema\Response\Response;
use Novapay\Payment\SDK\Schema\Request\PostbackPostRequest;
use Novapay\Payment\SDK\Security\RSA;

/**
 * Postback model class.
 * Processes postback request from API.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Postback extends Model
{
    public $version;
    public $code;
    public $body;
    public $sign;
    public $method;

    private $_session;

    /**
     * Postback constructor.
     * 
     * @param string $body    Request body.
     * @param array  $headers Request headers.
     * @param string $method  Request method.
     * @param int    $code    HTTP code.
     * @param string $version HTTP version.
     */
    public function __construct(
        $body = '', 
        array $headers = [], 
        $method = 'GET', 
        $code = 200, 
        $version = 'HTTP/1.1'
    ) {
        parent::__construct();

        $response = new Response($body, $headers, true);
        $this->setResponse($response);

        $head = $response->getHeaders();
        $this->version = $head->version ?? $version;
        $this->code    = $head->status->code ?? $code;
        $this->method  = $method;
        foreach ($head->headers as $header) {
            $this->setHeader($header->getKey(), $header->getValue());
        }
        $this->sign    = @base64_decode($head->get('X-Sign', true));
        $this->body    = $body;

        $data = json_decode($body, true);
        $this->setRequest(new PostbackPostRequest($data ? $data : []));

        $this->setSession($this->decodeSession($this->getRequest()));

        if (static::isTracing()) {
            $uri = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .
                   (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '..') .
                   (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
            static::trace(
                $this->toCurl(
                    $this->getRequest(), 
                    $method, 
                    $uri, 
                    $head->headers
                )
            );
        }
    }

    /**
     * Decodes (creates) session instance from postback request.
     * 
     * @param PostbackPostRequest $request Postback request.
     * 
     * @return Session                     Session object.
     */
    protected function decodeSession(PostbackPostRequest $request)
    {
        $session = new Session;
        $session->id       = $request->id;
        $session->metadata = $request->metadata;
        $session->status   = $request->status;
        return $session;
    }

    /**
     * Sets session instance here.
     * 
     * @param Session $session Session object.
     * 
     * @return self            Returns $this.
     */
    public function setSession(Session $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Returns session retrieved from postback call.
     * 
     * @return Session Session instance.
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Verifies the data with the signature.
     * 
     * @return bool TRUE if signature verified, otherwise FALSE.
     */
    public function verify()
    {
        $key = new RSA(
            $this->body,
            '',
            static::getPublicKey() 
                ? static::getPublicKey() 
                : static::getPublicKeyFile()
        );
        return $key->verify($this->sign);
    }
}