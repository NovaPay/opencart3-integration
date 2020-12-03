<?php
/**
 * Delivery Common model class.
 * Retrieves information about different directories.
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
use Novapay\Payment\SDK\Schema\Request\Delivery\CargoTypesGetRequest;
use Novapay\Payment\SDK\Schema\Request\Delivery\PackListGetRequest;
use Novapay\Payment\SDK\Schema\Response\Delivery\Response;
use Novapay\Payment\SDK\Schema\Response\Delivery\CargoTypesGetResponse;
use Novapay\Payment\SDK\Schema\Response\Delivery\Dimensions;
use Novapay\Payment\SDK\Schema\Response\Delivery\PackListGetResponse;

/**
 * Delivery Common model class.
 * Retrieves information about different directories.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class Common extends Model
{
    /**
     * Retrieves cargo types.
     * 
     * @return array|bool Array of CargoType[] on success, FALSE on failure.
     */
    public function getCargoTypes()
    {
        $request = new CargoTypesGetRequest(Model::getMerchantId());
        $response = $this->send($request, 'POST', '/delivery-info');
        $res = Response::create(
            $response[1],
            $response[0],
            CargoTypesGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof CargoTypesGetResponse) {
            return false;
        }

        return $res->items;
    }

    /**
     * Retrieves packings for provided dimensions.
     * 
     * @param Dimensions $dimensions    Package dimensions.
     * @param string     $typeOfPacking Packing type. Not in use.
     * 
     * @return array|bool Array of Packing[] on success, FALSE on failure.
     */
    public function getPackList(Dimensions $dimensions, $typeOfPacking = '')
    {
        $request = new PackListGetRequest(
            Model::getMerchantId(), 
            $dimensions, 
            $typeOfPacking
        );
        $response = $this->send($request, 'POST', '/delivery-info');
        $res = Response::create(
            $response[1],
            $response[0],
            PackListGetResponse::class
        );
        $this->setResponse($res);
        
        if (!$res instanceof PackListGetResponse) {
            return false;
        }

        return $res->items;
    }
}
