<?php
/**
 * Basic error response schema class.
 * Stores detailed information about the error.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema\Response\Error;

use Novapay\Payment\SDK\Schema\Response\Response;

/**
 * Basic error response schema class.
 * Stores detailed information about the error.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Error extends Response
{
    const TYPE_VALIDATION = 'validation';
    const TYPE_PROCESSING = 'processing';
    const TYPE_FATAL      = 'fatal';

    /**
     * The error type.
     * Possible values are 'validation', 'processing', 'fatal'
     * 
     * @var string
     */
    public $type;

    public $message;

    /**
     * Error Response constructor.
     * 
     * @param Novapay\Payment\SDK\Schema\Response\Response $body    The response object.
     * @param array                                $headers Omitting current value.
     */
    public function __construct($body, $headers = [])
    {
        if (!$body instanceof Response) {
            throw new \Exception(
                'Error constructor accepts only ' . Response::class
            );
        }
        $this->setHeaders($body->getHeaders());
        $this->setBody($body->getBody());
        foreach ($body as $key => $value) {
            $this->$key = $value;
        }
        $this->initMessage();
    }

    /**
     * Returns error message.
     * 
     * @return string The error message.
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Initializes a message returned from the body.
     * 
     * @return void
     */
    protected function initMessage()
    {
        $this->message = '<error message>';
    }

    /**
     * @param Response $body           The response object.
     * @param array    $headers        Omitting current value.
     * @param null     $expected_class Expected class name.
     * 
     * @return Error      The error response.
     */
    public static function create($body, $headers = [], $expected_class = null)
    {
        $error = null;
        $me = $body;
        switch (@$me->type) {
        case static::TYPE_PROCESSING:
            $error = new Processing($me);
            break;
        case static::TYPE_VALIDATION:
            $error = new Validation($me);
            break;
        case static::TYPE_FATAL:
            $error = new Fatal($me);
            break;
        default:
            $error = new Error($me);
        }

        return $error;
    }
}