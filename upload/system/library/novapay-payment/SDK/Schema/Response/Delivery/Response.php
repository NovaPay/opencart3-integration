<?php
/**
 * Delivery response schema class.
 * The data used in communication with API when returning results.
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

use Novapay\Payment\SDK\Schema\Response\Response as BasicResponse;

/**
 * Delivery response schema class.
 * The data used in communication with API when returning results.
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
class Response extends BasicResponse
{

    /**
     * Creates a response specific object based on the HTTP response.
     * 
     * @param string $body           HTTP body.
     * @param array  $headers        HTTP headers.
     * @param string $expected_class Expected Response class.
     * 
     * @return Response              The response object.
     */
    public static function create(
        $body, 
        $headers = [], 
        $expected_class = null
    ) {
        $me = new Response($body, $headers, true);
        // status is always 200 :(
        // so check the $me->errors
        if (property_exists($me, 'errors') && is_array($me->errors) && count($me->errors)) {
            return Error::create($me);
        }
        if (!$expected_class || !class_exists($expected_class)) {
            return $me;
        }
        $response = new $expected_class($body, $headers);
        foreach ($me as $key => $value) {
            $response->$key = $value;
        }
        $response->afterUpdate();
        return $response;
    }
}