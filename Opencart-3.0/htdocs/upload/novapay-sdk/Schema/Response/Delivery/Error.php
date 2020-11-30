<?php
/**
 * Delivery error response schema class.
 * Stores detailed information about the error.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * 
 *           Different error handling than in basic response.
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/58f0730deea270153c8be3cd
 */

namespace Novapay\Payment\SDK\Schema\Response\Delivery;

use Exception;

/**
 * Delivery error response schema class.
 * Stores detailed information about the error.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * 
 *           Different error handling than in basic response.
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/58f0730deea270153c8be3cd
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
            throw new Exception(
                sprintf(
                    'Error constructor accepts only %s but %s provided', 
                    Response::class, 
                    get_class($body)
                )
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
        $me = $body;
        $error = new self($me);
        $error->type = static::TYPE_PROCESSING;
        if (isset($me->errors) && count($me->errors)) {
            $error->message = implode("\n", $me->errors);
        }
        return $error;
    }
}