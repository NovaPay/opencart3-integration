<?php
/**
 * Delivery Cities model class.
 * Retrieves the information about cities.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Model\Delivery;

use Novapay\Payment\SDK\Model\Model;
use Novapay\Payment\SDK\Schema\Response\Delivery\Response;
use Novapay\Payment\SDK\Schema\Request\Delivery\CitiesGetRequest;
use Novapay\Payment\SDK\Schema\Response\Delivery\CitiesGetResponse;
use Novapay\Payment\SDK\Schema\Response\Error\Error;

/**
 * Delivery Cities model class.
 * Retrieves the information about cities.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class Cities extends Model
{
    const MIN_LENGTH = 3;

    /**
     * Payment processing URL to redirect user there.
     * 
     * @var string Processing URL.
     */
    public $items = [];

    /**
     * Search for the cities by search term.
     * 
     * @param string $term Search term.
     * 
     * @return bool        TRUE on success, FALSE on failure.
     */
    public function search($term = '')
    {
        $request = new CitiesGetRequest(Model::getMerchantId(), $term);
        if (mb_strlen($term) < static::MIN_LENGTH) {
            $error = Response::create(
                '{"type": "' . Error::TYPE_VALIDATION . '", ' . 
                '"errors": {"error": {"message": ' . 
                '"FindByString - too few characters in search string. Minimum ' . 
                static::MIN_LENGTH . ' characters."}}}', 
                ['HTTP/1.1 400 Bad request', 'Content-Type: application/json']
            );
            $this->setResponse($error);
            return false;
        }
        $response = $this->send($request, 'POST', '/delivery-info');
        $res = Response::create(
            $response[1],
            $response[0],
            CitiesGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof CitiesGetResponse) {
            return false;
        }

        $this->items = $res->items;

        return true;
    }
}
