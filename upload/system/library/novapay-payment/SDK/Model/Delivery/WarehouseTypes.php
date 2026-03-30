<?php
/**
 * Delivery Warehouse Types model class.
 * Retrieves the information about types of warehouse.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d8211a0fe4f08e8f7ce45
 */

namespace Novapay\Payment\SDK\Model\Delivery;

use Novapay\Payment\SDK\Model\Model;
use Novapay\Payment\SDK\Schema\Response\Delivery\Response;
use Novapay\Payment\SDK\Schema\Request\Delivery\WarehouseTypesGetRequest;
use Novapay\Payment\SDK\Schema\Response\Delivery\WarehouseTypesGetResponse;

/**
 * Delivery Warehouse Types model class.
 * Retrieves the information about types of warehouse.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d8211a0fe4f08e8f7ce45
 */
class WarehouseTypes extends Model
{
    /**
     * Payment processing URL to redirect user there.
     * 
     * @var string Processing URL.
     */
    public $items = [];

    /**
     * Search for the warehouses by city reference id.
     * 
     * @param string $cityRef City reference id.
     * 
     * @return bool        TRUE on success, FALSE on failure.
     */
    public function getByCityRef($cityRef)
    {
        $request = new WarehouseTypesGetRequest(Model::getMerchantId(), $cityRef);
        $response = $this->send($request, 'POST', '/delivery-info');
        $res = Response::create(
            $response[1],
            $response[0],
            WarehouseTypesGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof WarehouseTypesGetResponse) {
            return false;
        }

        $this->items = $res->items;

        return true;
    }
}
